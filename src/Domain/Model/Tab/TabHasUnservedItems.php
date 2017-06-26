<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class TabHasUnservedItems extends \Exception
{

    const MESSAGE = 'Tab has unserved items';

    public static function create() {
        return new static(self::MESSAGE);
    }
}