<?php

namespace malotor\EventsCafe\Domain\Model\OrderedItem;

use Throwable;

class OrderedItemNotExists extends \Exception
{

    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'The Ordered item does not exists';
        parent::__construct($message, $code, $previous);
    }
}