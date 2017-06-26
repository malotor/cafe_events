<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\OrderedItem\OrderedItemsRepository;
use malotor\EventsCafe\Domain\Model\Tab\Tab;
use malotor\EventsCafe\Domain\Model\Tab\TabId;
use malotor\EventsCafe\Domain\Model\Tab\TabRepository;

class PlaceOrderHandler
{
    private $tabRepopsitory;
    private $orderedItemsRepository;

    public function __construct(
        TabRepository $tabRepository,
        OrderedItemsRepository $orderedItemsRepository
    ) {
        $this->tabRepopsitory = $tabRepository;
        $this->orderedItemsRepository = $orderedItemsRepository;
    }

    public function handle(PlaceOrderCommand $command)
    {

        /** @var Tab $tab */
        $tab = $this->tabRepopsitory->get(TabId::fromString($command->id));

        foreach ($command->items as $itemNumber) {
            $orderedItem = $this->orderedItemsRepository->findById($itemNumber);
            $tab->placeItemInOrder($orderedItem->getMenuNumber() , $orderedItem->isDrink(), $orderedItem->getPrice());
        }
        $this->tabRepopsitory->add($tab);

    }

}