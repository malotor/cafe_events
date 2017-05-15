<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class MustPayEnoughException extends \Exception
{
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Mus pay enough money';
        parent::__construct($message, $code, $previous);
    }
}