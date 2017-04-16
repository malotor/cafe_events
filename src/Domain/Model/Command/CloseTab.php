<?php

namespace malotor\EventsCafe\Domain\Model\Command;

class CloseTab
{
    public $id;
    public $amountPaid;

    /**
     * CloseTab constructor.
     *
     * @param $id
     * @param $amountPaid
     */
    public function __construct($id, $amountPaid)
    {
        $this->id = $id;
        $this->amountPaid = $amountPaid;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAmountPaid()
    {
        return $this->amountPaid;
    }



}