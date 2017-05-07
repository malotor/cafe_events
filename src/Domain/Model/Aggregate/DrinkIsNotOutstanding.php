<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class DrinkIsNotOutstanding extends \Exception
{
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Drinks is not Outstanding';
        parent::__construct($message, $code, $previous);
    }
}