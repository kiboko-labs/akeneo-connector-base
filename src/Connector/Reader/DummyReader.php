<?php

namespace Kiboko\Component\Connector\Reader;

use Akeneo\Component\Batch\Item\ItemReaderInterface;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Kiboko\Component\Connector\StepExecutionAwareTrait;

class DummyReader
    implements ItemReaderInterface, StepExecutionAwareInterface
{
    use StepExecutionAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        return null;
    }
}
