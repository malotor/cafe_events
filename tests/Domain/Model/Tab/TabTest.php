<?php

use PHPUnit\Framework\TestCase;

use malotor\EventsCafe\Domain\Model\Tab\Tab;
use malotor\EventsCafe\Domain\Model\OrderedItem\OrderedItem;
use Ramsey\Uuid\Uuid;
use malotor\EventsCafe\Domain\Model\Tab\TabId;
use Buttercup\Protects\AggregateHistory;
use malotor\EventsCafe\Domain\Model\Events;

class TabTest extends TestCase
{
    /**
     * @test
     */
    public function new_tab_should_be_open()
    {
        $tab = Tab::open(1,"John");

        $this->assertTrue($tab->isOpen());
    }

    /**
     * @test
     */
    public function it_should_place_an_order()
    {

        $tab = Tab::open(1,"John");

        $this->assertEquals(0,$tab->outstandingItems());

        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $this->assertEquals(2,$tab->outstandingItems());

    }

    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\DrinkIsNotOutstanding
     */
    public function should_not_serve_drinks_that_are_not_outstanding()
    {

        $tab = Tab::open(1,"John");


        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([
            3
        ]);

    }

    /**
     * @test
     */
    public function served_drinks_must_be_outstanding()
    {

        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(4,true,3.5),
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);

        $this->assertEquals(2,$tab->outstandingItems());
        $this->assertFalse($tab->isItemOutstanding(1));
        $this->assertTrue($tab->isItemOutstanding(2));
        $this->assertTrue($tab->isItemOutstanding(4));
    }



    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\FoodNotOutstanding
     */
    public function food_not_ordered_cannot_be_marked_as_prepared()
    {

        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->prepareFood([3]);

    }


    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\FoodIsNotPrepared
     */
    public function food_not_prepared_cannot_be_served()
    {

        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveFood([2]);

    }

    /**
     * @test
     */
    public function food_prepared_still_be_outstanding()
    {

        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->prepareFood([2]);

        $this->assertTrue($tab->isItemOutstanding(2));

    }

    /**
     * @test
     */
    public function food_ordered_and_prepared_can_be_served()
    {

        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $this->assertFalse($tab->isItemOutstanding(2));
        $this->assertTrue($tab->isItemServed(2));

    }


    /**
     * @test
     */
    public function food_served_is_no_longer_prepared()
    {

        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $this->assertFalse($tab->isFoodPrepared(2));

    }



    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\TabHasUnservedItems
     */
    public function can_not_close_tab_with_unserved_drinks()
    {
        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->prepareFood([2]);
        $tab->serveFood([2]);
        $tab->close(10);

    }

    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\TabHasUnservedItems
     */
    public function can_not_close_tab_with_unprepared_food()
    {
        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);
        $tab->close(10);
    }


    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\TabHasUnservedItems
     */
    public function can_not_close_tab_with_unserved_food()
    {
        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->close(10);
    }

    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\MustPayEnoughException
     */
    public function cannot_be_closed_with_not_enough_pay()
    {
        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $tab->close(3);

    }

    /**
     * @test
     */
    public function can_close_tab_by_paying_exact_amount()
    {
        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $tab->close(7);

        $this->assertFalse($tab->isOpen());
    }

    /**
     * @test
     */
    public function can_close_tab_with_tip()
    {
        $tab = Tab::open(1,"John");
        $orderedItems = [
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $tab->close(9);

        $this->assertFalse($tab->isOpen());
    }


    /**
     * @tests
     */
    public function it_should_be_reconstituted_from_events()
    {
        $aggregateId = TabId::fromString("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1");

        $eventHistory = new AggregateHistory($aggregateId, [
            new Events\TabOpened($aggregateId,1,'Jane Doe'),
            new Events\DrinksOrdered($aggregateId,
                [
                    new OrderedItem(1,true,10)
                ]
            ),
            new Events\DrinksServed($aggregateId,[1]),
            new Events\FoodOrdered($aggregateId,
                [
                    new OrderedItem(2,false,13)
                ]
            ),
            new Events\FoodPrepared($aggregateId, [2]),
            new Events\FoodServed($aggregateId,[2]),
            new Events\TabClosed($aggregateId,23, 23, 0),
        ]);


        $anAggregate = Tab::reconstituteFrom($eventHistory);

        $this->assertEquals("adb40bf1-e79c-442e-ae7c-3c9cfcdd38f1",(string) $anAggregate->getAggregateId());
        //$this->assertEquals(4, $anAggregate->get());
    }
}
