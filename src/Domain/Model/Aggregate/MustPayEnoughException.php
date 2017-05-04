<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

class MustPayEnoughException extends \Exception {
    public function __construct($message = "", $code = 0, Throwable $previous = NULL)
    {
        $message = 'Mus pay enough money';
        parent::__construct($message, $code, $previous);
    }
}