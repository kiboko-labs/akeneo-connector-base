<?php

namespace Kiboko\Component\Connector\Processor;

use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Kiboko\Component\Connector\StepExecutionAwareTrait;

class DummyProcessor
    implements ItemProcessorInterface, StepExecutionAwareInterface
{
    use StepExecutionAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        return null;
    }
}
