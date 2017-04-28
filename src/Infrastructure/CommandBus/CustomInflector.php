<?php

namespace malotor\EventsCafe\Infrastructure\CommandBus;

use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;

class CustomInflector implements MethodNameInflector
{
    // You can use the command and commandHandler to generate any name you
    // prefer but here, we'll always return the same one.
    public function inflect($command, $commandHandler)
    {
        return 'handle';
    }
}