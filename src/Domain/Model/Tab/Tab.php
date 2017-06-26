<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

use malotor\EventsCafe\Domain\Model\Aggregate\Aggregate;

use malotor\EventsCafe\Domain\Model\Events\DrinksOrdered;
use malotor\EventsCafe\Domain\Model\Events\DrinksServed;
use malotor\EventsCafe\Domain\Model\Events\FoodOrdered;
use malotor\EventsCafe\Domain\Model\Events\FoodPrepared;
use malotor\EventsCafe\Domain\Model\Events\FoodServed;
use malotor\EventsCafe\Domain\Model\Events\TabClosed;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;

use malotor\EventsCafe\Domain\Model\OrderedItem\OrderedItem;

class Tab extends Aggregate
{
    private $table;
    private $waiter;

    private $open = false;
    /** @var OrderedItem[] */
    private $outstandingDrinks = [];
    /** @var OrderedItem[] */
    private $outstandingFoods = [];

    /** @var OrderedItem[] */
    private $preparedFood = [];

    /** @var OrderedItem[] */
    private $servedItems = [];

    private function __construct(TabId $id, $table, $waiter)
    {
        $this->id = $id;
        $this->table = $table;
        $this->waiter = $waiter;
        $this->open = true;
    }

    static public function open($table, $waiter): Tab
    {

        $id = TabId::create();
        $newTab = new Tab($id, $table, $waiter);

        $newTab->recordThat(new TabOpened($id, $table, $waiter));

        return $newTab;

    }

    static public function openWithId(TabId $id, $table, $waiter): Tab
    {
        $newTab = new Tab($id, $table, $waiter);
        $newTab->recordThat(new TabOpened($id, $table, $waiter));

        return $newTab;
    }

    static public function createEmptyWithId($id): Aggregate
    {
        return new Tab($id, null, null);
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

    public function isOpen(): bool
    {
        return $this->open;
    }


    public function placeItemInOrder($id, $isDrink, $price)
    {

        if (!$this->open) {
            throw TabNotOpen::create();
        }

        $item = new OrderedItem($id,$isDrink,$price);

        if ($isDrink)
        {
            $this->applyAndRecordThat(new DrinksOrdered($this->getAggregateId(),
                [$item]));
            return;
        }

        $this->applyAndRecordThat(new FoodOrdered($this->getAggregateId(),
                [$item]));

    }

    public function placeOrder($orderedItems)
    {
        foreach ($orderedItems as $item)
        {
            $this->placeItemInOrder($item[0], $item[1], $item[2]);
        }
    }

    public function serveDrinks($drinksServed)
    {
        $this->assertDrinksAreOutstanding($drinksServed);
        $this->applyAndRecordThat(new DrinksServed($this->getAggregateId(),
            $drinksServed));
    }

    private function assertDrinksAreOutstanding($items)
    {
        array_walk($items, function($drink) {
            if (!in_array($drink, array_keys($this->outstandingDrinks)))
                throw DrinkIsNotOutstanding::create();
        });
    }

    public function prepareFood($foodPrepared)
    {
        $this->assertFoodsAreOutstanding($foodPrepared);
        $this->applyAndRecordThat(new FoodPrepared($this->getAggregateId(), $foodPrepared));

    }

    private function assertFoodsAreOutstanding($items)
    {

        array_walk($items, function($food) {
            if (!in_array($food, array_keys($this->outstandingFoods)))
                throw FoodNotOutstanding::create();
        });

    }

    public function serveFood($foodServed)
    {
        $this->assertFoodsArePrepared($foodServed);
        $this->applyAndRecordThat(new FoodServed($this->getAggregateId(),
            $foodServed));
    }

    private function assertFoodsArePrepared($items)
    {
        array_walk($items, function($food) {
            if (!in_array($food, array_keys($this->preparedFood)))
                throw FoodIsNotPrepared::create();
        });

    }

    public function close(float $amount)
    {
        $this->assertHasNotUnservedItems();

        $tabAmount = $this->calculateTotalAmount();

        if ($tabAmount > $amount) {
            throw  MustPayEnoughException::create();
        }

        $this->applyAndRecordThat(new TabClosed($this->getAggregateId(),
            $amount, $tabAmount, ($amount - $tabAmount)));

    }

    private function assertHasNotUnservedItems()
    {
        if (!(empty($this->outstandingDrinks) && empty($this->outstandingFoods))) {
            throw TabHasUnservedItems::create();
        }
    }

    private function calculateTotalAmount()
    {
        return array_reduce($this->servedItems, function ($res, $a) {
            return $res += $a->getPrice();
        }, 0);
    }

    public function outstandingItems()
    {
        return count($this->outstandingFoods) + count($this->outstandingDrinks);
    }

    public function isItemOutstanding($itemMenuNumber): bool
    {
        return in_array($itemMenuNumber,
                array_keys($this->outstandingFoods)) || in_array($itemMenuNumber,
                array_keys($this->outstandingDrinks));
    }

    public function isItemServed($itemMenuNumber): bool
    {
        return in_array($itemMenuNumber, array_keys($this->servedItems));
    }

    public function isFoodPrepared($itemMenuNumber): bool
    {
        return in_array($itemMenuNumber, array_keys($this->preparedFood));
    }

    public function applyTabOpened(TabOpened $tabOpenedEvent)
    {
        $this->waiter = $tabOpenedEvent->getWaiterId();
        $this->table = $tabOpenedEvent->getTableNumber();
    }

    public function applyDrinksOrdered(DrinksOrdered $drinksOrdered)
    {
        array_walk($drinksOrdered->getItems(), function($drink) {
            $this->outstandingDrinks[$drink->getMenuNumber()] = $drink;
        });
    }

    public function applyFoodOrdered(FoodOrdered $foodOrdered)
    {
        array_walk($foodOrdered->getItems(), function($food) {
            $this->outstandingFoods[$food->getMenuNumber()] = $food;
        });
    }

    public function applyDrinksServed(DrinksServed $drinksServed)
    {
        array_walk($drinksServed->getItems(), function($drinkServedNumber) {
            $item = $this->outstandingDrinks[$drinkServedNumber];
            unset($this->outstandingDrinks[$drinkServedNumber]);
            $this->servedItems[$drinkServedNumber] = $item;
        });
    }

    public function applyFoodPrepared(FoodPrepared $foodPrepared)
    {
        array_walk($foodPrepared->getItems(), function($food) {
            $item = $this->outstandingFoods[$food];
            $this->preparedFood[$food] = $item;
        });
    }

    public function applyFoodServed(FoodServed $foodServed)
    {
        array_walk($foodServed->getItems(), function($food) {
            $item = $this->preparedFood[$food];
            $this->servedItems[$food] = $item;
            unset($this->outstandingFoods[$food]);
            unset($this->preparedFood[$food]);
        });
    }

    public function applyTabClosed(TabClosed $tabClosed)
    {
        $this->open = false;
    }


}