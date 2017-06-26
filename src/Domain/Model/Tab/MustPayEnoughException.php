<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class MustPayEnoughException extends \Exception
{
    const MESSAGE = 'Must pay enough money';

    public static function create() {
        return new static(self::MESSAGE);
    }

}