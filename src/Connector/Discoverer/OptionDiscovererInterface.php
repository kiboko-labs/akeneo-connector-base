<?php

namespace Kiboko\Component\Connector\Discoverer;

interface OptionDiscovererInterface
{
    /**
     * @param string $rawValue
     * @param string $cleanValue
     * @return mixed
     */
    public function mapTo($rawValue, $cleanValue);

    /**
     * @param string $value
     * @param string $locale
     * @return array
     */
    public function find($value, $locale = null);
}