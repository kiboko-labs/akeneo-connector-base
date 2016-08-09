<?php

namespace Kiboko\Component\Connector\Step;

use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\Batch\Model\StepExecution;
use Akeneo\Component\Batch\Step\ItemStep as AkeneoItemStep;
use Kiboko\Component\Connector\ItemMapperInterface;

class ItemStep extends AkeneoItemStep
{
    /** @var ItemMapperInterface */
    protected $processor = null;

    /**
     * Set processor
     * @param ItemMapperInterface $processor
     */
    public function setMapper(ItemMapperInterface $processor)
    {
        $this->processor = $processor;
    }

    /**
     * Get processor
     * @return ItemMapperInterface|null
     */
    public function getMapper()
    {
        return $this->processor;
    }

    /**
     * Get the configurable step elements
     *
     * @return array
     */
    public function getConfigurableStepElements()
    {
        return array(
            'reader'    => $this->getReader(),
            'mapper'    => $this->getMapper(),
            'processor' => $this->getMapper(),
            'writer'    => $this->getWriter(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function doExecute(StepExecution $stepExecution)
    {
        $itemsToWrite  = array();
        $writeCount    = 0;

        $this->initializeStepElements($stepExecution);

        $stopExecution = false;
        while (!$stopExecution) {
            try {
                $readItem = $this->reader->read();
                if (null === $readItem) {
                    $stopExecution = true;
                    continue;
                }
            } catch (InvalidItemException $e) {
                $this->handleStepExecutionWarning($this->stepExecution, $this->reader, $e);

                continue;
            }

            foreach ($this->map($readItem) as $mappedItem) {
                $processedItem = $this->process($mappedItem);
                if (null !== $processedItem) {
                    $itemsToWrite[] = $processedItem;
                    $writeCount++;
                    if (0 === $writeCount % $this->batchSize) {
                        $this->write($itemsToWrite);
                        $itemsToWrite = array();
                        $this->getJobRepository()->updateStepExecution($stepExecution);
                    }
                }
            }
        }

        if (count($itemsToWrite) > 0) {
            $this->write($itemsToWrite);
        }
        $this->flushStepElements();
    }

    /**
     * @param mixed $readItem
     *
     * @return \Traversable processed items
     */
    protected function map($readItem)
    {
        try {
            $mappedItems = $this->processor->process($readItem);
            if (is_array($mappedItems)) {
                return new \ArrayIterator($mappedItems);
            } else if ($mappedItems instanceof \Traversable) {
                return $mappedItems;
            }
        } catch (InvalidItemException $e) {
            $this->handleStepExecutionWarning($this->stepExecution, $this->processor, $e);
        }

        return new \ArrayIterator([]);
    }
}
