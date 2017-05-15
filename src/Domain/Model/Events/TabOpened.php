<?php

namespace malotor\EventsCafe\Domain\Model\Events;

use malotor\EventsCafe\Domain\Model\Tab\TabId;

class TabOpened extends TabEvent
{
    private $tableNumber;
    private $waiterId;

    /**
     * TabOpenedEvent constructor.
     *
     * @param $id
     * @param $tableNumber
     * @param $waiterId
     */
    public function __construct(TabId $id, $tableNumber, $waiterId)
    {
        $this->id = $id;
        $this->tableNumber = $tableNumber;
        $this->waiterId = $waiterId;
    }

    /**
     * @return mixed
     */
    public function getTableNumber()
    {
        return $this->tableNumber;
    }

    /**
     * @return mixed
     */
    public function getWaiterId()
    {
        return $this->waiterId;
    }


}