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

    /**
     * @var string
     */
    private $thumbnailAttribute;

    /**
     * @var string
     */
    private $smallImageAttribute;

    /**
     * @var string
     */
    private $galleryAttributes;

    /**
     * VariantGroupAssetsProcessor constructor.
     * @param AttributeManager $attributeManager
     * @param Serializer $serializer
     */
    public function __construct(
        AttributeManager $attributeManager,
        Serializer $serializer
    ) {
        $this->attributeManager = $attributeManager;
        $this->serializer = $serializer;

        $this->imageAttribute      = 'image';
        $this->thumbnailAttribute  = 'thumbnail';
        $this->smallImageAttribute = 'small_image';
        $this->galleryAttributes   = [];
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
            'thumbnailAttribute' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->attributeManager->getAttributeChoices('pim_catalog_image'),
                    'required' => true,
                    'select2'  => true,
                    'label' => 'luni_assets.steps.gallery.processor.thumbnailAttribute.label',
                    'help'  => 'luni_assets.steps.gallery.processor.thumbnailAttribute.help',
                ],
            ],
            'smallImageAttribute' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->attributeManager->getAttributeChoices('pim_catalog_image'),
                    'required' => true,
                    'select2'  => true,
                    'label' => 'luni_assets.steps.gallery.processor.smallImageAttribute.label',
                    'help'  => 'luni_assets.steps.gallery.processor.smallImageAttribute.help',
                ],
            ],
            'galleryAttributes' => [
                'type'    => 'choice',
                'options' => [
                    'choices'  => $this->attributeManager->getAttributeChoices('pim_catalog_image'),
                    'required' => true,
                    'select2'  => true,
                    'multiple' => true,
                    'label' => 'luni_assets.steps.gallery.processor.galleryAttributes.label',
                    'help'  => 'luni_assets.steps.gallery.processor.galleryAttributes.help',
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
     * @return mixed
     */
    public function getSmallImageAttribute()
    {
        return $this->smallImageAttribute;
    }

    /**
     * @param mixed $smallImageAttribute
     */
    public function setSmallImageAttribute($smallImageAttribute)
    {
        $this->smallImageAttribute = $smallImageAttribute;
    }

    /**
     * @return mixed
     */
    public function getThumbnailAttribute()
    {
        return $this->thumbnailAttribute;
    }

    /**
     * @param mixed $thumbnailAttribute
     */
    public function setThumbnailAttribute($thumbnailAttribute)
    {
        $this->thumbnailAttribute = $thumbnailAttribute;
    }

    /**
     * @return mixed
     */
    public function getGalleryAttributes()
    {
        return $this->galleryAttributes;
    }

    /**
     * @param mixed $galleryAttributes
     */
    public function setGalleryAttributes($galleryAttributes)
    {
        $this->galleryAttributes = $galleryAttributes;
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
            'thumbnail'   => $this->getMediaAttribute($this->getThumbnailAttribute(), $normalized),
            'small_image' => $this->getMediaAttribute($this->getSmallImageAttribute(), $normalized),
            'gallery'     => $this->getGalleryAttribute($this->getGalleryAttributes(), $normalized),
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

    /**
     * @param array $attributeCodes
     * @param array $normalized
     * @return string
     */
    private function getGalleryAttribute(array $attributeCodes, array $normalized)
    {
        $values = [];
        foreach ($attributeCodes as $attributeCode) {
            $value = $this->getMediaAttribute($attributeCode, $normalized);
            if ($value === null) {
                continue;
            }
            $values[] = $value;
        }

        return implode(',', $values);
    }
}