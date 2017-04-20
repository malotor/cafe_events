<?php

namespace malotor\EventsCafe\Domain\Model\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class OpenTabHandler
{
    private $tabRepopsitory;

    public function __construct(TabRepository $tabRepository )
    {
        $this->tabRepopsitory = $tabRepository;
    }

    public function handle(OpenTabCommand $command)
    {

        $newTab = Tab::open($command->tableNumber, $command->waiterId);

        $this->tabRepopsitory->add($newTab);

    }

}