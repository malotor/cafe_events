<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\EventStore;

use malotor\EventsCafe\Infrastructure\Serialize\JsonSerializer;
use PHPUnit\Framework\TestCase;

use Buttercup\Protects\DomainEvents;
use malotor\EventsCafe\Domain\Model\Tab\TabId;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;

use malotor\EventsCafe\Domain\Model\Tab\Tab;
use malotor\EventsCafe\Infrastructure\Persistence\EventStore\RedisEventStore;

use JMS\Serializer\SerializerBuilder;

class RedisEventStoreTest extends TestCase
{
    private $eventStore;

    public function setUp()
    {
        $serializer = new JsonSerializer(__DIR__ . '/../../../../resources/serializer');

        $client = new \Predis\Client('tcp://redis:6379');

        $this->eventStore = new RedisEventStore($client, $serializer);
    }

    /**
     * @test
     */
    public function it_should_recover_aggregate_history()
    {

        $domainEvents = new DomainEvents([
            new TabOpened(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"),1,'John')
        ]);
        $this->eventStore->commit($domainEvents);
        /** @var $anAggregate Tab */
        $anAggregateHistory = $this->eventStore->getAggregateHistoryFor(TabId::fromString("e247721a-b70c-4431-9ba5-1867340ae241"));

        $tab = Tab::reconstituteFrom($anAggregateHistory);

        $this->assertEquals("e247721a-b70c-4431-9ba5-1867340ae241", (string) $tab->getAggregateId());
        $this->assertEquals(1, $tab->getTable());
        $this->assertEquals("John", $tab->getWaiter());
    }
}
