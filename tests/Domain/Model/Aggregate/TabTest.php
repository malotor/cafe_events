<?php

use PHPUnit\Framework\TestCase;

use malotor\EventsCafe\Domain\Model\Aggregate\Tab;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use Ramsey\Uuid\Uuid;

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
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\DrinkIsNotOutstanding
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
            new OrderedItem(1,true,2.5),
            new OrderedItem(2,false,4.5),
        ];
        $tab->placeOrder($orderedItems);

        $tab->serveDrinks([1]);

        $this->assertEquals(1,$tab->outstandingItems());
        $this->assertFalse($tab->isItemOutstanding(1));
        $this->assertTrue($tab->isItemOutstanding(2));
    }



    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\FoodNotOutstanding
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
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\FoodIsNotPrepared
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
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\TabHasUnservedItems
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
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\TabHasUnservedItems
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
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\TabHasUnservedItems
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
     * @expectedException \malotor\EventsCafe\Domain\Model\Aggregate\MustPayEnoughException
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
}
