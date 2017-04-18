<?php

namespace malotor\EventsCafe\Domain\Model\Events;

use malotor\EventsCafe\Domain\Model\Aggregate\TabId;

class TabClosed extends TabEvent
{
    private $amountPaid;
    private $orderValue;
    private $tipValue;

    /**
     * TabClosed constructor.
     *
     * @param $id
     * @param $amountPaid
     * @param $orderValue
     * @param $tipValue
     */
    public function __construct(TabId $id, $amountPaid, $orderValue, $tipValue)
    {
        $this->id = $id;
        $this->amountPaid = $amountPaid;
        $this->orderValue = $orderValue;
        $this->tipValue = $tipValue;
    }


    /**
     * @return mixed
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    /**
     * @return mixed
     */
    public function getOrderValue()
    {
        return $this->orderValue;
    }

    /**
     * @return mixed
     */
    public function getTipValue()
    {
        return $this->tipValue;
    }


}