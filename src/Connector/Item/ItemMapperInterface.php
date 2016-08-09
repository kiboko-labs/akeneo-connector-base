<?php

namespace Kiboko\Component\Connector;

use Akeneo\Component\Batch\Item\InvalidItemException;

interface ItemMapperInterface
{
    /**
     * @param $item
     * @return mixed
     * @throws InvalidItemException
     */
    public function map($item);
}
