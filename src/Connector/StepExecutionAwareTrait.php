<?php

namespace Kiboko\Component\Connector;

use Akeneo\Component\Batch\Model\StepExecution;

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