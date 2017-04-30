<?php

namespace malotor\EventsCafe\Application\DataTransformer;

use malotor\EventsCafe\Domain\Model\Aggregate\OrderedItem;
use malotor\EventsCafe\Domain\ReadModel\Tabs;
use malotor\EventsCafe\Domain\ReadModel\Items;

class TabToArrayDataTransformer implements DataTranformer
{
    private $result = [];

    public function write($input)
    {
        /** @var Tabs $tab */
        foreach ($input as $tab)
        {

            $tmp['id'] = $tab->getTabId();
            $tmp['table'] = $tab->getTableNumber();
            $tmp['waiter'] = $tab->getWaiter();
            $tmp['status'] = $tab->getOpen() ? 'open' : 'close';
            /** @var Items $drink */
            $drinks = $tab->getOutstandingDrinks();
            foreach ($tab->getOutstandingDrinks() as $drink)
            {
                $tmp['outstanding_drinks'][] = $drink->getDescription();
            }
            /** @var Items $drink */
            foreach ($tab->getOutstandingFoods() as $food)
            {
                $tmp['outstanding_foods'][] = $food->getDescription();
            }

            $this->result[] = $tmp;
            unset($tmp);
        }
    }

    public function read()
    {
        return $this->result;
    }
}