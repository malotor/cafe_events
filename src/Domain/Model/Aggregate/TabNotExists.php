<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class TabNotExists extends \Exception
{
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'The tab does not exists';
        parent::__construct($message, $code, $previous);
    }
}