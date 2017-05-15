<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class FoodIsNotPrepared extends \Exception
{

    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Food is not prepared';
        parent::__construct($message, $code, $previous);
    }
}