<?php

namespace malotor\EventsCafe\Domain\Model\Events;

use Buttercup\Protects\IdentifiesAggregate;
use Ramsey\Uuid\Uuid;
use Buttercup\Protects\DomainEvent;

class TabOpenedEvent extends TabEvent
{
    public $tableNumber;
    public $waiterId;

    /**
     * TabOpenedEvent constructor.
     *
     * @param $id
     * @param $tableNumber
     * @param $waiterId
     */
    public function __construct(Uuid $id, $tableNumber, $waiterId)
    {
        $this->id = $id;
        $this->tableNumber = $tableNumber;
        $this->waiterId = $waiterId;
    }

}