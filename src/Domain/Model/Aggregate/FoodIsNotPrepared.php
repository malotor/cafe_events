<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class FoodIsNotPrepared extends \Exception {

    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'Food is not prepared';
        parent::__construct($message, $code, $previous);
    }
}