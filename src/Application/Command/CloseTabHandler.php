<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItemNotExists;
use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;

class CloseTabHandler
{
    private $tabRepopsitory;

    public function __construct(TabRepository $tabRepository)
    {
        $this->tabRepopsitory = $tabRepository;
    }

    public function handle(CloseTab $command)
    {
        /** @var Tab $tab */
        $tab = $this->tabRepopsitory->get(TabId::fromString($command->id));
        $tab->close($command->getAmountPaid());
        $this->tabRepopsitory->add($tab);
    }

}