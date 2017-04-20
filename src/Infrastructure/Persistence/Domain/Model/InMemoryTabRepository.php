<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Domain\Model;

use Buttercup\Protects\IdentifiesAggregate;
use Buttercup\Protects\RecordsEvents;
use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;

class InMemoryTabRepository implements TabRepository
{


    public function get(IdentifiesAggregate $aggregateId)
    {
        // TODO: Implement get() method.
    }

    public function add(RecordsEvents $aggregate)
    {
        // TODO: Implement add() method.
    }
}