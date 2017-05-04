<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use malotor\EventsCafe\Domain\Model\Events\DrinksOrdered;
use malotor\EventsCafe\Domain\Model\Events\DrinksServed;
use malotor\EventsCafe\Domain\Model\Events\FoodOrdered;
use malotor\EventsCafe\Domain\Model\Events\FoodPrepared;
use malotor\EventsCafe\Domain\Model\Events\FoodServed;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;

class TabProjection extends BaseProjection
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function projectTabOpened(TabOpened $event)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO tabs (tab_id, waiter, tableNumber, open) VALUES (:tab_id, :waiter, :tableNumber, 1)"
        );

        $stmt->execute([
            ':tab_id' => $event->getAggregateId(),
            ':waiter'   => $event->getWaiterId(),
            ':tableNumber' => $event->getTableNumber(),
        ]);

    }

    public function projectDrinksOrdered(DrinksOrdered $event)
    {

        foreach ($event->getItems() as $item)
        {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tabs_outstanding_drinks (tabid,itemid) VALUES (:tab_id, :item_id)"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item->getMenuNumber()
            ]);
        }

    }

    public function projectFoodOrdered(FoodOrdered $event)
    {
        /** @var OrderedItem $item */
        foreach ($event->getItems() as $item)
        {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tabs_outstanding_foods (tabid,itemid) VALUES (:tab_id, :item_id)"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item->getMenuNumber()
            ]);
        }
    }

    public function projectFoodPrepared(FoodPrepared $event)
    {
        /** @var OrderedItem $item */
        foreach ($event->getItems() as $item)
        {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tabs_prepared_foods (tabid,itemid) VALUES (:tab_id, :item_id)"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item
            ]);
        }
    }

    public function projectDrinksServed(DrinksServed $event)
    {
        /** @var OrderedItem $item */
        foreach ($event->getItems() as $item)
        {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tabs_served_items (tabid,itemid) VALUES (:tab_id, :item_id)"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item
            ]);

            $stmt = $this->pdo->prepare(
                "DELETE FROM tabs_outstanding_drinks WHERE tabId = :tab_id AND itemId = :item_id"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item
            ]);
        }
    }

    public function projectFoodServed(FoodServed $event)
    {
        /** @var OrderedItem $item */
        foreach ($event->getItems() as $item)
        {
            $stmt = $this->pdo->prepare(
                "INSERT INTO tabs_served_items (tabid,itemid) VALUES (:tab_id, :item_id)"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item
            ]);

            $stmt = $this->pdo->prepare(
                "DELETE FROM tabs_outstanding_foods WHERE tabId = :tab_id AND itemId = :item_id"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item
            ]);

            $stmt = $this->pdo->prepare(
                "DELETE FROM tabs_prepared_foods WHERE tabId = :tab_id AND itemId = :item_id"
            );

            $stmt->execute([
                ':tab_id' => $event->getAggregateId(),
                ':item_id'   => $item
            ]);
        }
    }
}