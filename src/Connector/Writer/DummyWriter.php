<?php

namespace Kiboko\Component\Connector\Writer;

use Akeneo\Component\Batch\Item\ItemWriterInterface;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Kiboko\Component\Connector\StepExecutionAwareTrait;

class DummyWriter
    implements ItemWriterInterface, StepExecutionAwareInterface
{
    use StepExecutionAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {
        return null;
    }
}
