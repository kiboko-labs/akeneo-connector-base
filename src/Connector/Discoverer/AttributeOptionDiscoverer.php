<?php

namespace Kiboko\Component\Connector\Discoverer;

use Doctrine\Common\Collections\ArrayCollection;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\AttributeOptionInterface;
use Pim\Component\Catalog\Model\AttributeOptionValueInterface;
use Pim\Bundle\CatalogBundle\Repository\AttributeOptionRepositoryInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Symfony\Component\Config\Loader\FileLoader;

class AttributeOptionDiscoverer
    implements OptionDiscovererInterface
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
     * @var AttributeInterface
     */
    private $attribute;

    /**
     * @var AttributeOptionInterface[]
     */
    private $attributeOptions;

    /**
     * @var array
     */
    private $mapping;

    /**
     * @param AttributeRepositoryInterface $attributeRepository
     * @param AttributeOptionRepositoryInterface $attributeOptionRepository
     * @param string $attributeCode
     * @param FileLoader $loader
     * @param string $mappingFile
     */
    public function __construct(
        AttributeRepositoryInterface $attributeRepository,
        AttributeOptionRepositoryInterface $attributeOptionRepository,
        $attributeCode,
        FileLoader $loader = null,
        $mappingFile = null
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionRepository = $attributeOptionRepository;
        $this->mapping = [];

        $this->attribute = $this->attributeRepository->findOneByIdentifier($attributeCode);
        if ($this->attribute) {
            $this->attributeOptions = $this->attribute->getOptions();
        } else {
            $this->attributeOptions = new ArrayCollection();
        }

        if ($mappingFile !== null && $loader !== null) {
            foreach ($loader->load($mappingFile) as $rawValue => $optionCode) {
                $this->mapTo($rawValue, $optionCode);
            }
        }
    }

    /**
     * @param string $rawValue
     * @param string $cleanValue
     * @return array
     */
    public function mapTo($rawValue, $cleanValue)
    {
        $this->mapping[$rawValue] = $cleanValue;

        return $this->mapping;
    }

    /**
     * @param string $value
     * @param string $locale
     * @return array
     */
    public function find($value, $locale = null)
    {
        if (isset($this->mapping[$value])) {
            $value = $this->mapping[$value];
        }

        /** @var AttributeOptionInterface $option */
        foreach ($this->attributeOptions as $option) {
            if ($locale !== null) {
                $option->setLocale($locale);
            }

            if ($option->getOptionValue() instanceof AttributeOptionValueInterface &&
                $option->getOptionValue()->getLabel() === $value
            ) {
                return $option->getCode();
            }
        }

        return null;
    }
}