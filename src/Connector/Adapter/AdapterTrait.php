<?php

namespace Kiboko\Component\Connector\Adapter;

trait AdapterTrait
{
    /**
     * @param resource $stream
     */
    public function loadFromStream($stream)
    {
        if ($stream === null) {
            throw new \RuntimeException('Invalid or closed stream.');
        }

        // FIXME: parse a possibly unfinished stream
        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadXML(stream_get_contents($stream));

        $this->load($document);
    }

    /**
     * @param string $path
     */
    public function loadFromPath($path)
    {
        if ($path === null || empty($path) || (stream_is_local($path) && !file_exists($path))) {
            throw new \RuntimeException('Invalid path or stream handler is not supported.');
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->load($path);

        $this->load($document);
    }

    /**
     * @param string $content
     */
    public function loadFromString($content)
    {
        if ($content === null || empty($content)) {
            throw new \RuntimeException('Empty XML content.');
        }

        $document = new \DOMDocument('1.0', 'UTF-8');
        $document->loadXML($content);

        $this->load($document);
    }

    abstract public function load(\DOMDocument $document);
}