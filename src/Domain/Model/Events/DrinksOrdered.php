<?php

namespace malotor\EventsCafe\Domain\Model\Events;

class DrinksOrdered extends TabEvent
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

    /**
     * @return OrderedItem[]
     */
    public function getItems()
    {
        return $this->items;
    }
}