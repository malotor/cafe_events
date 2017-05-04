<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class TabNotOpenException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'The tab is not open';
        parent::__construct($message, $code, $previous);
    }
}