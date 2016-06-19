<?php

namespace Kiboko\Component\Connector;


use Doctrine\Common\Inflector\Inflector;

trait NameAwareTrait
{
    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        $classname = get_class($this);

        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }

        return Inflector::tableize($classname);
    }
}