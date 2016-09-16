<?php

namespace Kiboko\Component\Connector\Discoverer;

use Akeneo\Component\Batch\Model\StepExecution;
use Pim\Component\Catalog\Repository\AttributeOptionRepositoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;

trait OptionsAttributeDiscovererTrait
{
    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var AttributeOptionRepositoryInterface
     */
    private $attributeOptionRepository;

    /**
     * @var string
     */
    private $field;

    /**
     * @param array $data
     * @return bool
     */
    public function match(array $data)
    {
        if (!isset($data[$this->field]) || empty($data[$this->field])) {
            return false;
        }

        return $this->matchValue($data[$this->field]);
    }

    /**
     * @param string $data
     * @return bool
     */
    abstract protected function matchValue($data);

    /**
     * @param array $data
     * @return array
     */
    abstract public function discover(array $data);

    /**
     * @param StepExecution $stepExecution
     */
    abstract public function setStepExecution(StepExecution $stepExecution);
}
