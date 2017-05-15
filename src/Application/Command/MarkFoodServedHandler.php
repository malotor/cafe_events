<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Tab\Tab;
use malotor\EventsCafe\Domain\Model\Tab\TabId;
use malotor\EventsCafe\Domain\Model\Tab\TabRepository;

class MarkFoodServedHandler
{
    private $tabRepopsitory;

    public function __construct(TabRepository $tabRepository)
    {
        $this->tabRepopsitory = $tabRepository;
    }

    public function handle(MarkFoodServedCommand $command)
    {
        /** @var Tab $tab */
        $tab = $this->tabRepopsitory->get(TabId::fromString($command->id));
        $tab->serveFood($command->items);
        $this->tabRepopsitory->add($tab);
    }

}