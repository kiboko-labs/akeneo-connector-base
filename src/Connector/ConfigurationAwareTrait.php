<?php

namespace Kiboko\Component\Connector;

use Symfony\Component\PropertyAccess\PropertyAccess;

trait ConfigurationAwareTrait
{
    /**
     * @return array
     */
    abstract public function getConfigurationFields();

    /**
     * Get the step element configuration (based on its properties)
     *
     * @return array
     */
    public function getConfiguration()
    {
        $result = [];
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach (array_keys($this->getConfigurationFields()) as $field) {
            $result[$field] = $accessor->getValue($this, $field);
        }

        return $result;
    }

    /**
     * Set the step element configuration
     *
     * @param array $config
     */
    public function setConfiguration(array $config)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        foreach ($config as $key => $value) {
            if (array_key_exists($key, $this->getConfigurationFields())) {
                $accessor->setValue($this, $key, $value);
            }
        }
    }
}