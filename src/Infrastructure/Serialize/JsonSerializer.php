<?php

namespace malotor\EventsCafe\Infrastructure\Serialize;

use JMS\Serializer\SerializerBuilder;

class JsonSerializer implements Serializer
{
    private $builder;

    public function __construct($path)
    {
        $this->builder = SerializerBuilder::create()
            ->addMetadataDir($path)
            ->build();
    }

    public function serialize($data)
    {
        return $this->builder->serialize($data, 'json');
    }

    public function deserialize($data, $type)
    {
        return $this->builder->deserialize($data,$type,'json');
    }
}