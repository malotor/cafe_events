<?php

namespace malotor\EventsCafe\Infrastructure\Serialize;

interface Serializer
{
    public function serialize($data);

    public function deserialize($data, $type);
}