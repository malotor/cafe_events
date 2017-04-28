<?php

namespace malotor\EventsCafe\Application\DataTransformer;

use malotor\EventsCafe\Domain\ReadModel\Tabs;

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
            $this->result[] = $tmp;
            unset($tmp);
        }
    }

    public function read()
    {
        return $this->result;
    }
}