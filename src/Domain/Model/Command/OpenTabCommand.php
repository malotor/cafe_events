<?php

namespace malotor\EventsCafe\Domain\Model\Command;

use Ramsey\Uuid\Uuid;

class OpenTabCommand
{
    public $id;
    public $tableNumber;
    public $waiterId;

}