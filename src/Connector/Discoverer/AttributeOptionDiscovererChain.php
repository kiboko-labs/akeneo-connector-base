<?php

namespace Kiboko\Component\Connector\Discoverer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class AttributeOptionDiscovererChain
{
    /**
     * @var Collection
     */
    private $optionMappers;

    /**
     * @var Collection[]
     */
    private $optionMapperCategories;

    public function __construct()
    {
        $this->optionMapperCategories = [];
        $this->optionMappers = new ArrayCollection();
    }

    /**
     * @param OptionDiscovererInterface $optionMapper
     * @param string $category
     * @param string $alias
     */
    public function addMapper(OptionDiscovererInterface $optionMapper, $category, $alias)
    {
        if (!isset($this->optionMapperCategories[$category])) {
            $this->optionMapperCategories[$category] = new ArrayCollection();
        }

        $this->optionMapperCategories[$category]->add($optionMapper);
        $this->optionMappers[$alias] = $optionMapper;
    }

    /**
     * @param string $alias
     * @return AttributeOptionDiscoverer
     */
    public function getMapper($alias)
    {
        return $this->optionMappers->get($alias);
    }

    /**
     * @param string $category
     * @return Collection|AttributeOptionDiscoverer[]
     */
    public function getMapperCategory($category)
    {
        return $this->optionMappers->get($category);
    }
}