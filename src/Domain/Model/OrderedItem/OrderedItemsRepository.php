<?php

namespace malotor\EventsCafe\Domain\Model\OrderedItem;

interface OrderedItemsRepository
{
    public function findById($id): OrderedItem;
}