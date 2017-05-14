<?php

namespace malotor\EventsCafe\Application\Command;

use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItemNotExists;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItemsRepository;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;
use malotor\EventsCafe\Domain\Model\Aggregate\TabId;
use malotor\EventsCafe\Domain\Model\Aggregate\TabNotExists;
use malotor\EventsCafe\Domain\Model\Aggregate\TabRepository;
use PHPUnit\Framework\TestCase;
use malotor\EventsCafe\Application\Command\PlaceOrderHandler;
use Prophecy\Argument;



class PlaceOrderHandlerTest extends TestCase
{


    public function it_should_place_an_order_on_a_tab()
    {
        $repository = $this->prophesize(TabRepository::class);
        $itemRepository = $this->prophesize(OrderedItemsRepository::class);

        $itemRepository->findById(Argument::any())->will(function ($args)  {
            return new OrderedItem($args[0],true,0);
        });

        $tab = TabSpy::createEmptyWithId(1,"Jhon Doe");

        $repository->get(Argument::any())->will(function ($args)  use ($tab) {
           return $tab;
        });

        $handler = new PlaceOrderHandler($repository->reveal() , $itemRepository->reveal());

        $anAggregateId = TabId::create();
        $orderedItemsIds = [ 1 ];

        $handler->handle(new PlaceOrderCommand($anAggregateId,$orderedItemsIds));

        //$tab->placeOrder($anAggregateId,$orderedItemsIds);

        $this->assertTrue($tab->itemHasBeenOrdered(1));
    }

}


class TabSpy extends Tab {

    public $items = [];
    //public $tab;

    /*
    public function __construct($table, $waiter)
    {
        $this->tab = Tab::open($table,$waiter);
    }
    */

    public function placeOrder($orderedItems)
    {
        $this->items = $orderedItems;
        $this->tab->placeOrder($orderedItems);
    }

    public function itemHasBeenOrdered($id)
    {

        foreach ($this->items as $orderedItem) {
            if ($orderedItem->getMenuNumber() == $id)
                return true;
        }
        return false;
    }
}