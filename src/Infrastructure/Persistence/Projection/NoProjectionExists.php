<?php

namespace malotor\EventsCafe\Infrastructure\Persistence\Projection;

class NoProjectionExists extends \Exception
{
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'No projection is resgistered for this event';
        parent::__construct($message, $code, $previous);
    }
}