<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvents;

interface Projection
{
    public function project(DomainEvents $eventStream);
}