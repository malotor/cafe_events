<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

interface Projection
{
    public function eventType();
    public function project($event);
}