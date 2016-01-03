<?php

namespace Luni\Component\Connector\Adapter;

interface AdapterInterface
    extends \IteratorAggregate, \Countable
{
    /**
     * @param resource $stream
     */
    public function loadFromStream($stream);

    /**
     * @param string $path
     */
    public function loadFromPath($path);

    /**
     * @param string $content
     */
    public function loadFromString($content);

    /**
     * @return int
     */
    public function size();
}