<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

use malotor\EventsCafe\Domain\Model\Events\DrinksOrdered;
use malotor\EventsCafe\Domain\Model\Events\DrinksServed;
use malotor\EventsCafe\Domain\Model\Events\FoodOrdered;
use malotor\EventsCafe\Domain\Model\Events\FoodPrepared;
use malotor\EventsCafe\Domain\Model\Events\FoodServed;
use malotor\EventsCafe\Domain\Model\Events\TabClosed;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;


class Tab extends Aggregate
{


    private $table;
    private $waiter;

    private $open = false;
    /** @var OrderedItem[]  */
    private $outstandingDrinks = [];
    /** @var OrderedItem[]  */
    private $outstandingFoods = [];

    /** @var OrderedItem[]  */
    private $preparedFood = [];

    /** @var OrderedItem[]  */
    private $servedItems = [];

    private function __construct(TabId $id, $table, $waiter)
    {
        $this->id = $id;
        $this->table = $table;
        $this->waiter = $waiter;
        $this->open = true;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return mixed
     */
    public function getWaiter()
    {
        return $this->waiter;
    }


    static public function open($table, $waiter) : Tab
    {

        $id = TabId::create();
        $newTab =  new Tab($id, $table, $waiter);

        $newTab->recordThat(
            new TabOpened($id, $table, $waiter)
        );

        return $newTab;

    }

    static public function createEmptyWithId($id): Aggregate
    {
        return new Tab($id, null, null);
    }

    public function isOpen(): bool
    {
        return $this->open;
    }


    public function placeOrder($orderedItems)
    {
        if (!$this->open)
            throw new TabNotOpenException();

        $drinks = array_filter($orderedItems, function ($a) { return $a->isDrink(); });
        $food = array_filter($orderedItems, function ($a) { return !$a->isDrink(); });


        if (count($drinks) > 0 ) $this->applyAndRecordThat(new DrinksOrdered($this->getAggregateId(), $drinks));
        if (count($food) > 0 ) $this->applyAndRecordThat(new FoodOrdered($this->getAggregateId(), $food));

    }

    public function serveDrinks($drinksServed)
    {
        $this->assertDrinksAreOutstanding($drinksServed);
        $this->applyAndRecordThat(new DrinksServed($this->getAggregateId(), $drinksServed));
    }

    public function prepareFood($foodPrepared)
    {
        $this->assertFoodsAreOutstanding($foodPrepared);
        $this->applyAndRecordThat(new FoodPrepared($this->getAggregateId(),$foodPrepared));

    }

    public function serveFood($foodServed)
    {
        $this->assertFoodsArePrepared($foodServed);
        $this->applyAndRecordThat(new FoodServed($this->getAggregateId(),$foodServed));
    }


    public function close(float $amount)
    {
        $this->assertHasNotUnservedItems();

        $tabAmount = $this->calculateTotalAmount();

        if ($tabAmount > $amount)
            throw new MustPayEnoughException();

        $this->applyAndRecordThat(new TabClosed($this->getAggregateId(),$amount, $tabAmount , ($tabAmount-$amount)));

    }


    private function calculateTotalAmount()
    {
        return array_reduce(
            $this->servedItems,
            function($res, $a) { return $res += $a->getPrice(); },
            0
        );
    }


    public function outstandingItems()
    {
        return count($this->outstandingFoods) + count($this->outstandingDrinks);
    }

    public function isItemOutstanding($itemMenuNumber): bool
    {
        return in_array($itemMenuNumber, array_keys($this->outstandingFoods)) || in_array($itemMenuNumber, array_keys($this->outstandingDrinks));
    }

    public function isItemServed($itemMenuNumber): bool
    {
        return in_array($itemMenuNumber, array_keys($this->servedItems));
    }

    public function applyTabOpened(TabOpened $tabOpenedEvent)
    {
        $this->waiter = $tabOpenedEvent->getWaiterId();
        $this->table = $tabOpenedEvent->getTableNumber();
    }

    public function applyDrinksOrdered(DrinksOrdered $drinksOrdered)
    {
        foreach ($drinksOrdered->getItems() as $drink)
        {
            $this->outstandingDrinks[$drink->getMenuNumber()] = $drink;
        }
    }

    public function applyFoodOrdered(FoodOrdered $foodOrdered)
    {
        foreach ($foodOrdered->getItems() as $food)
        {
            $this->outstandingFoods[$food->getMenuNumber()] = $food;
        }
    }


    public function applyDrinksServed(DrinksServed $drinksServed)
    {
        foreach ($drinksServed->getItems() as $drink) {

            foreach ( $this->outstandingDrinks as $itemMenuNumber => $item)
            {
                if ($itemMenuNumber == $drink) {
                    $this->servedItems[$itemMenuNumber] = $item;
                    unset($this->outstandingDrinks[$itemMenuNumber]);
                 }

            }
        }

    }

    public function applyFoodPrepared(FoodPrepared $foodPrepared)
    {
        foreach ($foodPrepared->getItems() as $food) {
            $item = $this->outstandingFoods[$food];
            $this->preparedFood[$food] = $item;
        }
    }

    public function applyFoodServed(FoodServed $foodServed)
    {
        foreach ($foodServed->getItems() as $food) {
            $item = $this->preparedFood[$food];
            $this->servedItems[$food] = $item;
            unset($this->outstandingFoods[$food]);
            unset($this->preparedFood[$food]);
        }

    }

    public function applyTabClosed(TabClosed $tabClosed)
    {
        //TODO Save amount and tip
        $this->open = false;
    }

    private function assertDrinksAreOutstanding($drinksServed)
    {

        foreach ($drinksServed as $drink) {
            $inArray = false;
            foreach ($this->outstandingDrinks as $item)
            {
                if ($item->getMenuNumber() == $drink ) $inArray = true;
            }
            if (!$inArray)
                throw new DrinkIsNotOutstanding();
        }
    }

    private function assertFoodsAreOutstanding($foodsServed)
    {
        foreach ($foodsServed as $food) {
            $inArray = false;
            foreach ($this->outstandingFoods as $item)
            {
                if ($item->getMenuNumber() == $food ) $inArray = true;
            }
            if (!$inArray)
                throw new FoodNotOutstanding();
        }

    }


    private function assertFoodsArePrepared($foodsServed)
    {
        foreach ($foodsServed as $food) {
            $inArray = false;
            foreach ($this->preparedFood as $item)
            {
                if ($item->getMenuNumber() == $food ) $inArray = true;
            }
            if (!$inArray)
                throw new FoodIsNotPrepared();
        }
    }

    private function assertHasNotUnservedItems()
    {
        if (!(empty($this->outstandingDrinks) && empty($this->outstandingFoods)) )
            throw new TabHasUnservedItems();
    }


}