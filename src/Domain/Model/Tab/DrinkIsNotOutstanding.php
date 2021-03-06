<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class DrinkIsNotOutstanding extends \Exception
{

    const MESSAGE = 'Drinks is not Outstanding';

    public static function create() {
        return new static(self::MESSAGE);
    }

}