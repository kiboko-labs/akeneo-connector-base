# Akeneo Connector Utils

## TL;DR

This component brings tools and utilities for connectors developers.

This adapter brings :

 * XML data source parsing tools
 * Attribute option value discovering
 * Attribute list input
 * Assets processor (images attributes)
 * Variant group assets export
 * Dummy reader/processor/writer
 * Utility traits for your connectors
 
## Utilities

### `ConfigurationAwareTrait`

This trait fixed features in `Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement`,
the `getConfiguration` method requires public attributes to be defined, but in lots of cases,
you may not want to expose your configuration handlers.

```php
<?php

use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\ItemReaderInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Luni\Component\Connector\ConfigurationAwareTrait;
use Luni\Component\Connector\NameAwareTrait;
use Luni\Component\Connector\StepExecutionAwareTrait;

class FooReader
    extends AbstractConfigurableStepElement
    implements ItemReaderInterface, StepExecutionAwareInterface
{
    use StepExecutionAwareTrait;
    use ConfigurationAwareTrait;
    use NameAwareTrait;

    public function getConfigurationFields()
    {
        // ...
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        // ...
    }
}
```

### `AttributeManager`

This helps you to create parametrable connectors, eg: for media assets exports

```php
<?php

namespace Luni\Component\Connector\Processor;

use Akeneo\Bundle\BatchBundle\Item\AbstractConfigurableStepElement;
use Akeneo\Bundle\BatchBundle\Item\InvalidItemException;
use Akeneo\Bundle\BatchBundle\Item\ItemProcessorInterface;
use Akeneo\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Luni\Component\Connector\ConfigurationAwareTrait;
use Luni\Component\Connector\Manager\AttributeManager;
use Luni\Component\Connector\NameAwareTrait;
use Luni\Component\Connector\StepExecutionAwareTrait;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Symfony\Component\Serializer\Serializer;

class ProductAssetsProcessor
    extends AbstractConfigurableStepElement
    implements ItemProcessorInterface, StepExecutionAwareInterface
{
    use StepExecutionAwareTrait;
    use ConfigurationAwareTrait;
    use NameAwareTrait;

    /**
     * @var AttributeManager
     */
    private $attributeManager;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var string
     */
    private $imageAttribute;

    public function __construct(
        AttributeManager $attributeManager,
        Serializer $serializer
    ) {
        $this->attributeManager = $attributeManager;
        $this->serializer = $serializer;

        $this->imageAttribute = 'image';
    }

    public function getConfigurationFields()
    {
        return [
            'imageAttribute' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->attributeManager->getAttributeChoices('pim_catalog_image'),
                    'required' => true,
                    'select2'  => true,
                    'label' => 'luni_assets.steps.gallery.processor.imageAttribute.label',
                    'help'  => 'luni_assets.steps.gallery.processor.imageAttribute.help',
                ],
            ],
        ];
    }

    /**
     * @return mixed
     */
    public function getImageAttribute()
    {
        return $this->imageAttribute;
    }

    /**
     * @param mixed $imageAttribute
     */
    public function setImageAttribute($imageAttribute)
    {
        $this->imageAttribute = $imageAttribute;
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        if (!$item instanceof ProductInterface) {
            throw new InvalidItemException(sprintf('Item should be an instance of %s', ProductInterface::class), $item);
        }

        $normalized = $this->serializer->normalize($item, 'flat');

        return [
            'sku'         => $normalized['sku'],
            'image'       => $this->getMediaAttribute($this->getImageAttribute(), $normalized),
        ];
    }

    /**
     * @param string $attributeCode
     * @param array $normalized
     * @return string
     */
    private function getMediaAttribute($attributeCode, array $normalized)
    {
        if (!isset($normalized[$attributeCode]) || empty($normalized[$attributeCode])) {
            return null;
        }

        return $normalized[$attributeCode];
    }
}
```
