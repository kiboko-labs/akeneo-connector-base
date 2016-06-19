<?php

namespace Kiboko\Component\Connector\Discoverer;

interface DiscovererInterface
{
    /**
     * @param array $data
     * @return bool
     */
    public function match(array $data);

    /**
     * @param array $data
     * @return array
     */
    public function discover(array $data);
}