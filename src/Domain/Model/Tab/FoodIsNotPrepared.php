<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class FoodIsNotPrepared extends \Exception
{

    const MESSAGE = 'Food is not prepared';

    public static function create() {
        return new static(self::MESSAGE);
    }

}