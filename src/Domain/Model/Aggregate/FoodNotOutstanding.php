<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class FoodNotOutstanding extends \Exception
{

    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Food is not outstanding';
        parent::__construct($message, $code, $previous);
    }
}