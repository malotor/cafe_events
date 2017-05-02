<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;

class MarkDrinksServedHandler
{
    private $tabRepopsitory;

    public function __construct(TabRepository $tabRepository)
    {
        $this->tabRepopsitory = $tabRepository;
    }

    public function handle(MarkDrinksServedCommand $command)
    {
        /** @var Tab $tab */
        $tab = $this->tabRepopsitory->get(TabId::fromString($command->id));
        $tab->serveDrinks($command->items);
        $this->tabRepopsitory->add($tab);
    }

}