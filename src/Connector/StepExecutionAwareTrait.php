<?php

namespace Luni\Component\Connector;

use Akeneo\Bundle\BatchBundle\Entity\StepExecution;

trait StepExecutionAwareTrait
{
    /**
     * @var StepExecution
     */
    private $stepExecution;

    /**
     * @param StepExecution $stepExecution
     */
    public function setStepExecution(StepExecution $stepExecution)
    {
        $this->stepExecution = $stepExecution;
    }
}