<?php

namespace malotor\EventsCafe\Tests\Infrastructure\Persistence\EventStore;

use Buttercup\Protects\DomainEvents;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;
use PHPUnit\Framework\TestCase;

use malotor\EventsCafe\Infrastructure\Persistence\EventStore\PDOEventStore;
use JMS\Serializer\SerializerBuilder;

class PDOEventStoreTest extends TestCase
{

    private $pdo;
    private $repository;

    public function setUp()
    {
        $this->pdo = new \PDO(
            'sqlite::memory:',
            null,
            null,
            array(\PDO::ATTR_PERSISTENT => true)
        );

        $sql = file_get_contents(__DIR__ . '/../../../../resources/db/events_cafe.sql');
        $this->pdo->exec($sql);

        $serializer = SerializerBuilder::create()
            ->addMetadataDir(__DIR__ . '/../../../../resources/serializer')
            ->build();

        $this->repository = new PDOEventStore($this->pdo, $serializer);
    }

    /**
     * @test
     */
    public function it_should_commit_events_to_databse()
    {

        $domainEvents = new DomainEvents([
            new TabOpened(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"),1,'John')
        ]);
        $this->repository->commit($domainEvents);
        $stm = $this->pdo->query("SELECT * FROM events");

        var_dump($stm->fetchAll(\PDO::FETCH_ASSOC));

    }


    /**
     * @test
     */
    public function it_should_recover_aggregate_history()
    {

        $domainEvents = new DomainEvents([
            new TabOpened(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"),1,'John')
        ]);
        $this->repository->commit($domainEvents);

        $anAggregate = $this->repository->getAggregateHistoryFor(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"));
        var_dump($anAggregate);

    }
}
