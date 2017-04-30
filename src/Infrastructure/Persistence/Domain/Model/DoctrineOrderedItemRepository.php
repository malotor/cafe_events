<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Domain\Model;

use Doctrine\ORM\EntityManager;
use JMS\Serializer\Tests\Fixtures\Order;
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
        try {
            /** @var Items $readOrderedItem */
            $readOrderedItem = $this->em->find('malotor\EventsCafe\Domain\ReadModel\Items', $id);
            return new OrderedItem($id, $readOrderedItem->getIsDrink(),$readOrderedItem->getPrice());
        } catch (\Exception $e)
        {
            throw new OrderedItemNotExists();
        }
    }
}