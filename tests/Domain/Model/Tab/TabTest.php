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
    public function it_should_be_opened_when_its_created()
    {
        $tab = Tab::open(1,"John");

        $this->assertTrue($tab->isOpen());
    }


    /**
     * @test
     */
    public function it_should_not_have_any_outstanding_item_when_its_created()
    {
        $tab = Tab::open(1,"John");

        $this->assertEquals(0,$tab->outstandingItems());
    }


    /**
     * @test
     */
    public function it_should_place_new_items_as_outstanding()
    {

        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5],
        ]);

        $this->assertEquals(2,$tab->outstandingItems());

        $this->assertTrue($tab->isItemOutstanding(1));
        $this->assertTrue($tab->isItemOutstanding(2));
        $this->assertFalse($tab->isItemOutstanding(3));

    }

    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\DrinkIsNotOutstanding
     */
    public function it_should_not_mark_drinks_as_served_it_are_not_outstanding()
    {

        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5],
        ]);

        $tab->serveDrinks([
            3
        ]);

    }

    /**
     * @test
     */
    public function it_should_mark_drinks_as_served_if_outstanding()
    {
        $tab = self::openTabWithMenuItems([
            [4,true,3.5],
            [1,true,2.5],
            [2,false,4.5]
        ]);
        $tab->serveDrinks([1]);

        $this->assertTrue($tab->isItemServed(1));
    }


    /**
     * @test
     */
    public function it_should_remove_drinks_from_outstanding_when_it_has_been_served()
    {

        $tab = self::openTabWithMenuItems([
            [4,true,3.5],
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->serveDrinks([1]);

        $this->assertEquals(2,$tab->outstandingItems());

        $this->assertFalse($tab->isItemOutstanding(1));
    }


    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\FoodNotOutstanding
     */
    public function it_should_not_mark_food_as_prepared_it_it_are_not_outstanding()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->prepareFood([3]);

    }


    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\FoodIsNotPrepared
     */
    public function it_should_not_mark_food_as_served_if_it_are_not_prepared()
    {

        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->serveFood([2]);

    }

    /**
     * @test
     */
    public function it_should_keep_food_as_outstanding_when_it_is_prepared()
    {

        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->prepareFood([2]);

        $this->assertTrue($tab->isItemOutstanding(2));

    }

    /**
     * @test
     */
    public function it_could_mark_food_as_served_if_they_are_prepared()
    {

        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $this->assertFalse($tab->isItemOutstanding(2));
        $this->assertTrue($tab->isItemServed(2));

    }


    /**
     * @test
     */
    public function it_should_unmark_food_as_prepared_when_it_is_served()
    {

        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $this->assertFalse($tab->isFoodPrepared(2));

    }



    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\TabHasUnservedItems
     */
    public function it_should_not_be_closed_it_has_unserved_drinks()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->prepareFood([2]);
        $tab->serveFood([2]);
        $tab->close(10);

    }

    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\TabHasUnservedItems
     */
    public function it_should_not_be_closed_it_has_unprepared_food()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->serveDrinks([1]);
        $tab->close(10);
    }


    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\TabHasUnservedItems
     */
    public function it_should_not_be_closed_it_has_served_food()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->close(10);
    }

    /**
     * @test
     * @expectedException \malotor\EventsCafe\Domain\Model\Tab\MustPayEnoughException
     */
    public function it_should_not_be_closed_without_enough_pay()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $tab->close(3);

    }

    /**
     * @test
     */
    public function it_should_be_closed_with_exact_pay()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

        $tab->serveDrinks([1]);
        $tab->prepareFood([2]);
        $tab->serveFood([2]);

        $tab->close(7);

        $this->assertFalse($tab->isOpen());
    }

    /**
     * @test
     */
    public function it_should_be_closed_with_a_tip()
    {
        $tab = self::openTabWithMenuItems([
            [1,true,2.5],
            [2,false,4.5]
        ]);

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


    protected static function openTabWithMenuItems($orderedItems) : Tab
    {
        $tab = Tab::open(1,"John");

        foreach ($orderedItems as $item)
        {
            $tab->placeItemInOrder($item[0],$item[1],$item[2]);
        }

        return $tab;
    }
}
