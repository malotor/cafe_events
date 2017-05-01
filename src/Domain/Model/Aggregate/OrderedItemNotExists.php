<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

use Throwable;

class OrderedItemNotExists extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'The Ordered item does not exists';
        parent::__construct($message, $code, $previous);
    }
}