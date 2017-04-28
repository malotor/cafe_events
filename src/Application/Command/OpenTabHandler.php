<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class OpenTabHandler
{
    private $tabRepopsitory;

    public function __construct(TabRepository $tabRepository )
    {
        $this->tabRepopsitory = $tabRepository;
    }

    private function handle(OpenTabCommand $command)
    {
        $newTab = Tab::open($command->tableNumber, $command->waiterId);
        $this->tabRepopsitory->add($newTab);
    }

    public function handleOpenTabCommand(OpenTabCommand $command)
    {
        $this->handle($command);
    }

}