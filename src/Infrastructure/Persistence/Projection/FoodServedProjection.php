<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use malotor\EventsCafe\Domain\Model\Events\DrinksOrdered;
use malotor\EventsCafe\Domain\Model\Events\DrinksServed;
use malotor\EventsCafe\Domain\Model\Events\FoodOrdered;
use malotor\EventsCafe\Domain\Model\Events\FoodPrepared;
use malotor\EventsCafe\Domain\Model\Events\FoodServed;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;

class FoodServedProjection implements Projection
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }


    public function project($event)
    {
        /** @var OrderedItem $item */
        foreach ($event->getItems() as $item) {
            $stmt = $this->pdo->prepare("INSERT INTO tabs_served_items (tabid,itemid) VALUES (:tab_id, :item_id)");

            $stmt->execute([
                ':tab_id'  => $event->getAggregateId(),
                ':item_id' => $item
            ]);

            $stmt = $this->pdo->prepare("DELETE FROM tabs_outstanding_foods WHERE tabId = :tab_id AND itemId = :item_id");

            $stmt->execute([
                ':tab_id'  => $event->getAggregateId(),
                ':item_id' => $item
            ]);

            $stmt = $this->pdo->prepare("DELETE FROM tabs_prepared_foods WHERE tabId = :tab_id AND itemId = :item_id");

            $stmt->execute([
                ':tab_id'  => $event->getAggregateId(),
                ':item_id' => $item
            ]);
        }
    }

    public function eventType()
    {
        return FoodServed::class;
    }


}