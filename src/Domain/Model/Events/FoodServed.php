<?php

namespace malotor\EventsCafe\Domain\Model\Events;

class FoodServed extends TabEvent
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
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }


}