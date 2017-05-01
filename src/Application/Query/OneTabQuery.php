<?php

namespace malotor\EventsCafe\Application\Query;

class OneTabQuery
{
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}