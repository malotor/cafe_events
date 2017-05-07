<?php

namespace malotor\EventsCafe\Application\DataTransformer;

use malotor\EventsCafe\Domain\ReadModel\Items;
use malotor\EventsCafe\Domain\ReadModel\Tabs;


class TabToArrayDataTransformer implements DataTranformer
{
    private $result = [];

    public function write($input)
    {
        if (is_array($input)) {
            foreach ($input as $tab) {
                $this->result[] = $this->convertTab($tab);
                //unset($tmp);
            }
        } else {
            if (get_class($input) == 'malotor\EventsCafe\Domain\ReadModel\Tabs') {
                $this->result = $this->convertTab($input);
            }
        }

    }

    private function convertTab(Tabs $tab)
    {
        $tmp['id'] = $tab->getTabId();
        $tmp['table'] = $tab->getTableNumber();
        $tmp['waiter'] = $tab->getWaiter();
        $tmp['status'] = $tab->getOpen() ? 'open' : 'close';
        /** @var Items $drink */
        $drinks = $tab->getOutstandingDrinks();
        foreach ($tab->getOutstandingDrinks() as $drink) {
            $tmp['outstanding_drinks'][] = $drink->getDescription();
        }
        /** @var Items $drink */
        foreach ($tab->getOutstandingFoods() as $food) {
            $tmp['outstanding_foods'][] = $food->getDescription();
        }

        foreach ($tab->getPreparedFoods() as $food) {
            $tmp['prepared_foods'][] = $food->getDescription();
        }

        foreach ($tab->getServedItems() as $item) {
            $tmp['served_items'][] = $item->getDescription();
        }

        $tmp['amountPaid'] = $tab->getAmountPaid();
        $tmp['orderValue'] = $tab->getOrderValue();
        $tmp['tipValue'] = $tab->getAmountPaid() - $tab->getOrderValue();

        return $tmp;
    }

    public function read()
    {
        return $this->result;
    }
}