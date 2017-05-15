<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class TabNotOpenException extends \Exception
{
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'The tab is not open';
        parent::__construct($message, $code, $previous);
    }
}