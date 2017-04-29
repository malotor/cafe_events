<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItemNotExists;
use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class PlaceOrderHandler
{
    private $tabRepopsitory;
    private $orderedItemsRepository;

    public function __construct(TabRepository $tabRepository , $orderedItemsRepository)
    {
        $this->tabRepopsitory = $tabRepository;
        $this->orderedItemsRepository = $orderedItemsRepository;
    }

    public function handle(PlaceOrderCommand $command)
    {

        $orderedItems = [];
        foreach ($command->items as $itemNumber) {
            $orderedItems[] = $this->orderedItemsRepository->findById($itemNumber);
        }

        /** @var Tab $tab */
        $tab = $this->tabRepopsitory->get($command->id);
        $tab->placeOrder($orderedItems);
    }

}