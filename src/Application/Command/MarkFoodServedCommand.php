<?php

namespace malotor\EventsCafe\Application\Command;

class MarkFoodServedCommand
{
    public $id;
    public $items;

    public function __construct($id, $items)
    {
        $this->id = $id;
        $this->items = $items;
    }

}