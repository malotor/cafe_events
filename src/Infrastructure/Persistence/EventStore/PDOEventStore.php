<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\EventStore;

use Buttercup\Protects\AggregateHistory;
use Buttercup\Protects\DomainEvents;
use Buttercup\Protects\IdentifiesAggregate;

class PDOEventStore implements EventStore
{

    public function commit(DomainEvents $events)
    {
        // TODO: Implement commit() method.
    }

    public function getAggregateHistoryFor(IdentifiesAggregate $id)
    {
        // TODO: Implement getAggregateHistoryFor() method.
    }
}