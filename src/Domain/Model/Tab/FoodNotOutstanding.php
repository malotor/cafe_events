<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class FoodNotOutstanding extends \Exception
{

    const MESSAGE = 'Food is not outstanding';

    public static function create() {
        return new static(self::MESSAGE);
    }
}