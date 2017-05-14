<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvent;
use Buttercup\Protects\DomainEvents;

interface Projection
{
    public function eventType();
    public function project($event);
}