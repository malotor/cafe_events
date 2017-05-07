<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

interface OrderedItemsRepository
{
    public function findById($id): OrderedItem;
}