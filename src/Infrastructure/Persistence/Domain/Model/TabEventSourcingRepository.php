<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Domain\Model;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\IsEventSourced;
use Buttercup\Protects\RecordsEvents;
use malotor\EventsCafe\Domain\Model\Tab\Tab;
use malotor\EventsCafe\Domain\Model\Tab\TabNotExists;
use malotor\EventsCafe\Domain\Model\Tab\TabRepository;
use malotor\EventsCafe\Infrastructure\Persistence\EventStore\EventStore;

class TabEventSourcingRepository implements TabRepository
{
    /**
     * @var EventStore
     */
    private $eventStore;


    private $projector;

    public function __construct(
        EventStore $eventStore,
        $projector
    ) {
        $this->eventStore = $eventStore;
        $this->projector = $projector;
    }

    /**
     * @param IdentifiesAggregate $aggregateId
     *
     * @return IsEventSourced
     */
    public function get(IdentifiesAggregate $aggregateId)
    {
        $eventStream = $this->eventStore->getAggregateHistoryFor($aggregateId);

        if ($eventStream->count() == 0) {
            throw TabNotExists::create();
        }

        return Tab::reconstituteFrom($eventStream);
    }

    /**
     * @param RecordsEvents $aggregate
     *
     * @return void
     */
    public function add(RecordsEvents $aggregate)
    {
        $events = $aggregate->getRecordedEvents();
        $this->eventStore->commit($events);
        $this->projector->project($events);

        $aggregate->clearRecordedEvents();
    }
}