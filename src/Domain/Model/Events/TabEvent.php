<?php

namespace malotor\EventsCafe\Domain\Model\Events;

use Buttercup\Protects\DomainEvent;

abstract class TabEvent implements DomainEvent
{
    protected $id;

    public function getAggregateId()
    {
        return $this->id;
    }

}