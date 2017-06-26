<?php

namespace malotor\EventsCafe\Domain\Model\Tab;

class TabNotOpen extends \Exception
{
    const MESSAGE = 'The tab is not open';

    public static function create() {
        return new static(self::MESSAGE);
    }
}