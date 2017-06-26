<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class TabNotExists extends \Exception
{
    const MESSAGE = 'The tab does not exists';

    public static function create() {
        return new static(self::MESSAGE);
    }

}