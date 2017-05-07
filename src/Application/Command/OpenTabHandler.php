<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\Tab;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;

class OpenTabHandler
{
    private $tabRepopsitory;

    public function __construct(TabRepository $tabRepository)
    {
        $this->tabRepopsitory = $tabRepository;
    }

    public function handle(OpenTabCommand $command)
    {
        $newTab = Tab::openWithId(TabId::fromString($command->tabId),
            $command->tableNumber, $command->waiterId);
        $this->tabRepopsitory->add($newTab);
    }

}