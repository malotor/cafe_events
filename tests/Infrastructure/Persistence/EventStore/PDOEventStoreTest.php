<?php

namespace malotor\EventsCafe\Tests\Infrastructure\Persistence\EventStore;

use Buttercup\Protects\DomainEvents;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;
use PHPUnit\Framework\TestCase;

use malotor\EventsCafe\Infrastructure\Persistence\EventStore\PDOEventStore;
use JMS\Serializer\SerializerBuilder;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class PDOEventStoreTest extends TestCase
{


    private $pdo;
    private $repository;

    public function setUp()
    {
        $this->pdo = new \PDO(
            'sqlite::memory',
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
        $results = $stm->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertEquals("e247721a-b70c-4431-9ba5-1867340ae241", $results[0]['aggregate_id']);
        $this->assertEquals(TabOpened::class, $results[0]['type']);

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
        /** @var $anAggregate Tab */
        $anAggregateHistory = $this->repository->getAggregateHistoryFor(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"));

        $tab = Tab::reconstituteFrom($anAggregateHistory);

        $this->assertEquals("e247721a-b70c-4431-9ba5-1867340ae241", (string) $tab->getAggregateId());
        $this->assertEquals(1, $tab->getTable());
        $this->assertEquals("John", $tab->getWaiter());
    }
}
