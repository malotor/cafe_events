<?php

namespace malotor\EventsCafe\Domain\Model\Events;

use malotor\EventsCafe\Domain\Model\Tab\TabId;

class FoodPrepared extends TabEvent
{
    private $items;

    public function __construct(TabId $id, $items)
    {
        $this->id = $id;
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
    }
}