<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

use malotor\EventsCafe\Domain\Model\Events\TabOpened;

class TabOpenedProjection implements Projection
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
        $stmt = $this->pdo->prepare("INSERT INTO tabs (tab_id, waiter, tableNumber, open) VALUES (:tab_id, :waiter, :tableNumber, 1)");

        $stmt->execute([
            ':tab_id'      => $event->getAggregateId(),
            ':waiter'      => $event->getWaiterId(),
            ':tableNumber' => $event->getTableNumber(),
        ]);
    }

    public function eventType()
    {
        return TabOpened::class;
    }
}