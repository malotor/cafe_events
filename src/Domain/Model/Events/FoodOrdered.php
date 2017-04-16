<?php

namespace malotor\EventsCafe\Domain\Model\Events;

class FoodOrdered extends TabEvent
{
    private $items;

    /**
     * FoodOrdered constructor.
     *
     * @param $items
     */
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * @return OrderedItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}