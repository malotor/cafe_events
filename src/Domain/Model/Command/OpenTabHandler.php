<?php

namespace malotor\EventsCafe\Domain\Model\Command;

use Ramsey\Uuid\Uuid;
use malotor\EventsCafe\Domain\Model\Aggregate\Tab;

class OpenTabHandler
{

    public function handle(OpenTabCommand $command)
    {
        return new Tab(
            $command->id,
            $command->tableNumber,
            $command->waiterId
        );
    }

}