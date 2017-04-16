<?php

namespace malotor\EventsCafe\Domain\Model\Events;

use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;

class DrinksServed extends TabEvent
{
    private $items;

    /**
     * DrinksOrdered constructor.
     *
     * @param $items
     */
    public function __construct($items)
    {
        $this->items = $items;
    }


    public function getItems()
    {
        return $this->items;
    }


}