<?php

namespace malotor\EventsCafe\Domain\ReadModel;

/**
 * Tabs
 */
class Tabs
{
    /**
     * @var string
     */
    private $tabId;

    /**
     * @var string
     */
    private $waiter;

    /**
     * @var string
     */
    private $tableNumber;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $outstandingDrinks;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $outstandingFoods;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $preparedFoods;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $servedItems;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->outstandingDrinks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->outstandingFoods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->preparedFoods = new \Doctrine\Common\Collections\ArrayCollection();
        $this->servedItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get tabId
     *
     * @return string
     */
    public function getTabId()
    {
        return $this->tabId;
    }

    /**
     * Set waiter
     *
     * @param string $waiter
     *
     * @return Tabs
     */
    public function setWaiter($waiter)
    {
        $this->waiter = $waiter;

        return $this;
    }

    /**
     * Get waiter
     *
     * @return string
     */
    public function getWaiter()
    {
        return $this->waiter;
    }

    /**
     * Set table
     *
     * @param string $table
     *
     * @return Tabs
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return string
     */
    public function getTableNumber()
    {
        return $this->tableNumber;
    }

    /**
     * Add outstandingDrink
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $outstandingDrink
     *
     * @return Tabs
     */
    public function addOutstandingDrink(\malotor\EventsCafe\Domain\ReadModel\Items $outstandingDrink)
    {
        $this->outstandingDrinks[] = $outstandingDrink;

        return $this;
    }

    /**
     * Remove outstandingDrink
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $outstandingDrink
     */
    public function removeOutstandingDrink(\malotor\EventsCafe\Domain\ReadModel\Items $outstandingDrink)
    {
        $this->outstandingDrinks->removeElement($outstandingDrink);
    }

    /**
     * Get outstandingDrinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOutstandingDrinks()
    {
        return $this->outstandingDrinks;
    }

    /**
     * Add outstandingFood
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $outstandingFood
     *
     * @return Tabs
     */
    public function addOutstandingFood(\malotor\EventsCafe\Domain\ReadModel\Items $outstandingFood)
    {
        $this->outstandingFoods[] = $outstandingFood;

        return $this;
    }

    /**
     * Remove outstandingFood
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $outstandingFood
     */
    public function removeOutstandingFood(\malotor\EventsCafe\Domain\ReadModel\Items $outstandingFood)
    {
        $this->outstandingFoods->removeElement($outstandingFood);
    }

    /**
     * Get outstandingFoods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOutstandingFoods()
    {
        return $this->outstandingFoods;
    }

    /**
     * Add preparedFood
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $preparedFood
     *
     * @return Tabs
     */
    public function addPreparedFood(\malotor\EventsCafe\Domain\ReadModel\Items $preparedFood)
    {
        $this->preparedFoods[] = $preparedFood;

        return $this;
    }

    /**
     * Remove preparedFood
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $preparedFood
     */
    public function removePreparedFood(\malotor\EventsCafe\Domain\ReadModel\Items $preparedFood)
    {
        $this->preparedFoods->removeElement($preparedFood);
    }

    /**
     * Get preparedFoods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPreparedFoods()
    {
        return $this->preparedFoods;
    }

    /**
     * Add servedItem
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $servedItem
     *
     * @return Tabs
     */
    public function addServedItem(\malotor\EventsCafe\Domain\ReadModel\Items $servedItem)
    {
        $this->servedItems[] = $servedItem;

        return $this;
    }

    /**
     * Remove servedItem
     *
     * @param \malotor\EventsCafe\Domain\ReadModel\Items $servedItem
     */
    public function removeServedItem(\malotor\EventsCafe\Domain\ReadModel\Items $servedItem)
    {
        $this->servedItems->removeElement($servedItem);
    }

    /**
     * Get servedItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getServedItems()
    {
        return $this->servedItems;
    }
}

