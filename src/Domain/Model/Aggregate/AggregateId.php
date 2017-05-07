<?php

namespace malotor\EventsCafe\Domain\Model\Aggregate;

use Buttercup\Protects\IdentifiesAggregate;
use Ramsey\Uuid\Uuid;

class AggregateId implements IdentifiesAggregate
{

    private $value;

    private function __construct(Uuid $uuid = null)
    {
        if ($uuid === null) {
            $uuid = Uuid::uuid4();
        }
        $this->value = $uuid;
    }

    public static function fromString($string)
    {
        return new static(Uuid::fromString($string));
    }

    public static function create()
    {
        return new static();
    }

    public function __toString()
    {
        return (string)$this->value;
    }

    public function equals(IdentifiesAggregate $other)
    {
        return (string)$this->value == (string)$other;
    }
}