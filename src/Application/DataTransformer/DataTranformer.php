<?php

namespace malotor\EventsCafe\Application\DataTransformer;

interface DataTranformer
{
    public function write($input);

    public function read();
}