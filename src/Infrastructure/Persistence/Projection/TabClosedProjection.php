<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use malotor\EventsCafe\Domain\Model\Events\DrinksOrdered;
use malotor\EventsCafe\Domain\Model\Events\DrinksServed;
use malotor\EventsCafe\Domain\Model\Events\FoodOrdered;
use malotor\EventsCafe\Domain\Model\Events\FoodPrepared;
use malotor\EventsCafe\Domain\Model\Events\FoodServed;
use malotor\EventsCafe\Domain\Model\Events\TabClosed;
use malotor\EventsCafe\Domain\Model\Events\TabOpened;

class TabClosedProjection implements Projection
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
        $stmt = $this->pdo->prepare("UPDATE tabs SET open = 0, amountPaid = :amount_paid, orderValue = :order_value WHERE tab_id = :tab_id");

        $stmt->execute([
            ':tab_id'      => $event->getAggregateId(),
            ':amount_paid' => $event->getAmountPaid(),
            ':order_value' => $event->getOrderValue()
        ]);

    }

    public function eventType()
    {
        return TabClosed::class;
    }


}