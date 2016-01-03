<?php

namespace Luni\Component\Connector\Writer;

use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemWriterInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Luni\Component\Connector\ConfigurationAwareTrait;
use Luni\Component\Connector\NameAwareTrait;
use Luni\Component\Connector\StepExecutionAwareTrait;

class DummyWriter
    extends AbstractConfigurableStepElement
    implements ItemWriterInterface, StepExecutionAwareInterface
{
    use StepExecutionAwareTrait;
    use ConfigurationAwareTrait;
    use NameAwareTrait;

    public function getConfigurationFields()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $items)
    {
        return null;
    }
}