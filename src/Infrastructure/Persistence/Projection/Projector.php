<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use Buttercup\Protects\DomainEvents;

class Projector
{
    /**
     * @var array
     */
    private $projections = [];

    public function register(array $projections)
    {
        foreach ($projections as $projection) {
            $this->projections[$projection->eventType()] = $projection;
        }
    }

    public function project(DomainEvents $events)
    {
        foreach ($events as $event) {
            if (isset($this->projections[get_class($event)])) {
                $this->projections[get_class($event)]->project($event);
            }
        }
    }
}