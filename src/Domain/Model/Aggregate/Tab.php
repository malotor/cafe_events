<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

use malotor\EventsCafe\Domain\Model\Command\OpenTabCommand;
use malotor\EventsCafe\Domain\Model\Events\DrinksOrdered;
use malotor\EventsCafe\Domain\Model\Events\DrinksServed;
use malotor\EventsCafe\Domain\Model\Events\FoodOrdered;
use malotor\EventsCafe\Domain\Model\Events\FoodPrepared;
use malotor\EventsCafe\Domain\Model\Events\FoodServed;
use malotor\EventsCafe\Domain\Model\Events\TabOpenedEvent;
use Ramsey\Uuid\Uuid;



class Tab extends Aggregate
{


    private $table;
    private $waiter;

    private $open = false;

    private $outstandingDrinks = [];
    private $outstandingFoods = [];

    private $preparedFood = [];

    private $servedItems = [];

    private function __construct(Uuid $id, $table, $waiter)
    {
        $this->id = $id;
        $this->table = $table;
        $this->waiter = $waiter;
        $this->open = true;
    }


    static public function open($table, $waiter) : Tab
    {

        $id = Uuid::uuid4();
        $newTab =  new Tab($id, $table, $waiter);

        $newTab->recordThat(
            new TabOpenedEvent($id, $table, $waiter)
        );

        return $newTab;

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


        if (count($drinks) > 0 ) $this->applyAndRecordThat(new DrinksOrdered($drinks));
        if (count($food) > 0 ) $this->applyAndRecordThat(new FoodOrdered($food));

    }

    public function serveDrinks($drinksServed)
    {
        $this->assertDrinksAreOutstanding($drinksServed);
        $this->applyAndRecordThat(new DrinksServed($drinksServed));
    }

    public function prepareFood($foodPrepared)
    {
        $this->assertFoodsAreOutstanding($foodPrepared);
        $this->applyAndRecordThat(new FoodPrepared($foodPrepared));

    }

    public function serveFood($foodServed)
    {
        $this->assertFoodsArePrepared($foodServed);
        $this->applyAndRecordThat(new FoodServed($foodServed));
    }


    public function close(float $amount)
    {
        $this->assertHasNotUnservedItems();

        $tabAmount = $this->calculateTotalAmount();

        if ($tabAmount != $amount)
            throw new MustPayEnoughException();

        $this->open = false;
    }

    private function calculateTotalAmount()
    {
        return array_reduce(
            $this->servedItems,
            function(&$res, $a) { $res += $a->getPrice(); },
            0
        );
    }

    public function outstandingItems()
    {
        return count($this->outstandingFoods) + count($this->outstandingDrinks);
    }

    public function isItemOutstanding($itemMenuNumber): bool
    {
        if (in_array($itemMenuNumber,$this->outstandingDrinks) or in_array($itemMenuNumber, $this->outstandingFoods))
            return true;

        return false;
    }

    public function isItemServed($itemMenuNumber): bool
    {
        if (in_array($itemMenuNumber,$this->servedItems))
            return true;

        return false;
    }


    public function applyDrinksOrdered(DrinksOrdered $drinksOrdered)
    {
        /** @var OrderedItem $drink */
        foreach ($drinksOrdered->getItems() as $drink)
        {
            $this->outstandingDrinks[] = $drink->getMenuNumber();
        }
    }

    public function applyFoodOrdered(FoodOrdered $foodOrdered)
    {
        /** @var $food $drink */
        foreach ($foodOrdered->getItems() as $food)
        {
            $this->outstandingFoods[] = $food->getMenuNumber();
        }
    }


    public function applyDrinksServed(DrinksServed $drinksServed)
    {
        foreach ($drinksServed->getItems() as $drink) {
            foreach ( $this->outstandingDrinks as $key => $item)
            {
                if ($item == $drink) {
                    $this->servedItems[] = $drink;
                    unset($this->outstandingDrinks[$key]);
                 }

            }
        }

    }

    public function applyFoodPrepared(FoodPrepared $foodPrepared)
    {
        $this->preparedFood = array_merge($this->preparedFood, $foodPrepared->getItems());
    }

    public function applyFoodServed(FoodServed $foodServed)
    {
        foreach ($foodServed->getItems() as $food) {
            foreach ( $this->outstandingFoods as $key => $item)
            {
                if ($item == $food) {
                    $this->servedItems[] = $food;
                    unset($this->outstandingFoods[$key]);
                    unset($this->preparedFood[$key]);
                }

            }
        }

    }

    private function assertDrinksAreOutstanding($drinksServed)
    {
        foreach ($drinksServed as $drink) {
            if (!in_array($drink, $this->outstandingDrinks))
                throw new DrinkIsNotOutstanding();

        }
    }

    private function assertFoodsAreOutstanding($foodsServed)
    {
        foreach ($foodsServed as $food) {
            if (!in_array($food, $this->outstandingFoods))
                throw new FoodNotOutstanding();

        }
    }


    private function assertFoodsArePrepared($foodsServed)
    {
        foreach ($foodsServed as $food) {
            if (!in_array($food, $this->preparedFood))
                throw new FoodIsNotPrepared();

        }
    }

    private function assertHasNotUnservedItems()
    {
        if (!(empty($this->outstandingDrinks) && empty($this->outstandingFoods)) )
            throw new TabHasUnservedItems();
    }


}