<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Tab\Tab;
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
        $newTab = Tab::openWithId(TabId::fromString($command->getTabId()),
            $command->getTableNumber(), $command->getWaiterId());
        $this->tabRepopsitory->add($newTab);
    }

}