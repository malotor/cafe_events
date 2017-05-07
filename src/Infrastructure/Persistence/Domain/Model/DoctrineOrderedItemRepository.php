<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Domain\Model;

use Doctrine\ORM\EntityManager;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItemNotExists;
use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItemsRepository;
use malotor\EventsCafe\Domain\ReadModel\Items;

class DoctrineOrderedItemRepository implements OrderedItemsRepository
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function findById($id): OrderedItem
    {
        /** @var Items $readOrderedItem */
        $readOrderedItem = $this->em->find('malotor\EventsCafe\Domain\ReadModel\Items',
            $id);
        if ($readOrderedItem === null) {
            throw new OrderedItemNotExists();
        }

        return new OrderedItem($id, $readOrderedItem->getIsDrink(),
            $readOrderedItem->getPrice());
    }
}