<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

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
            "INSERT INTO tabs (tab_id, waiter, 'table') VALUES (:tab_id, :waiter, :table)"
        );

        $stmt->execute([
            ':tab_id' => $event->getAggregateId(),
            ':waiter'   => $event->getWaiterId(),
            ':table' => $event->getTableNumber(),
        ]);

    }

}