<?php

namespace malotor\EventsCafe\Application\Command;


class OpenTabCommand
{
    public $tabId;
    public $tableNumber;
    public $waiterId;

    /**
     * OpenTabCommand constructor.
     *
     * @param $tabId
     * @param $tableNumber
     * @param $waiterId
     */
    public function __construct($tabId, $tableNumber, $waiterId)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->waiterId = $waiterId;
    }

    /**
     * @return mixed
     */
    public function getTabId()
    {
        return $this->tabId;
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