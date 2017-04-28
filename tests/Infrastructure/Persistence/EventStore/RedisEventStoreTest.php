<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\EventStore;

use PHPUnit\Framework\TestCase;

use Buttercup\Protects\DomainEvents;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;

use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class RedisEventStoreTest extends TestCase
{
    private $repository;

    public function setUp()
    {

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
