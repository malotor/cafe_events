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
        if (get_class($input) ==  'malotor\EventsCafe\Domain\ReadModel\Tabs') {
            $this->result = $this->convertTab($input);
            return;
        }

        if (is_array($input))
        foreach ($input as $tab)
        {
            $this->result[] = $this->convertTab($tab);
            unset($tmp);
        }

    }

    public function read()
    {
        return $this->result;
    }


    private function convertTab(Tabs $tab)
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

        return $tmp;
    }
}