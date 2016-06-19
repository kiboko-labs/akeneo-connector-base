<?php

namespace Luni\Component\Connector\Manager;

use Pim\Bundle\CatalogBundle\Doctrine\ORM\Repository\AttributeRepository;
use Pim\Component\Catalog\Model\AttributeInterface;

class AttributeManager
{
    /**
     * @var AttributeRepository
     */
    private $repository;

    public function __construct(
        AttributeRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Get a list of available attributes
     *
     * @param string $typeCode
     * @return AttributeInterface[]
     */
    public function getAttributes($typeCode)
    {
        $attributeCodes = $this->repository->getAttributeCodesByType($typeCode);
        $attributeList = [];
        foreach ($attributeCodes as $attributeCode) {
            $attributeList[] = $this->repository->findOneByIdentifier($attributeCode);
        }
        return $attributeList;
    }

    /**
     * Get channel choices
     * Allow to list channels in an array like array[<code>] = <label>
     *
     * @param string $typeCode
     * @return string[]
     */
    public function getAttributeChoices($typeCode)
    {
        $attributes = $this->getAttributes($typeCode);

        $choices = [];
        foreach ($attributes as $attribute) {
            $choices[$attribute->getCode()] = $attribute->getLabel();
        }

        return $choices;
    }
}