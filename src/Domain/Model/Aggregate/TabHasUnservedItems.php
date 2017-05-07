<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class TabHasUnservedItems extends \Exception
{

    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Tab has unserved items';
        parent::__construct($message, $code, $previous);
    }
}