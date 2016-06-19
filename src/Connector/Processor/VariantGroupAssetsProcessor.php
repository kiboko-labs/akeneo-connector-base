<?php

namespace Kiboko\Component\Connector\Processor;

use Akeneo\Component\Batch\Item\AbstractConfigurableStepElement;
use Akeneo\Component\Batch\Item\InvalidItemException;
use Akeneo\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Component\Batch\Step\StepExecutionAwareInterface;
use Kiboko\Component\Connector\ConfigurationAwareTrait;
use Kiboko\Component\Connector\Manager\AttributeManager;
use Kiboko\Component\Connector\NameAwareTrait;
use Kiboko\Component\Connector\StepExecutionAwareTrait;
use Pim\Component\Catalog\Model\GroupInterface;
use Symfony\Component\Serializer\Serializer;

class VariantGroupAssetsProcessor
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
     * @var string
     */
    private $mediaFilePath;

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
            'mediaFilePath' => [
                'options' => [
                    'label' => 'luni_tft_connector.steps.reader.mediaFilePath.label',
                    'help'  => 'luni_tft_connector.steps.reader.mediaFilePath.help',
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
     * @return string
     */
    public function getMediaFilePath()
    {
        return $this->mediaFilePath;
    }

    /**
     * @param string $mediaFilePath
     */
    public function setMediaFilePath($mediaFilePath)
    {
        $this->mediaFilePath = $mediaFilePath;
    }

    /**
     * {@inheritdoc}
     */
    public function process($item)
    {
        if (!$item instanceof GroupInterface || !$item->getType()->isVariant()) {
            throw new InvalidItemException(sprintf('Item should be an instance of %s and be a variant type.', GroupInterface::class), $item);
        }

        if ($item->getProductTemplate() === null) {
            return null;
        }

        $normalized = $this->serializer->normalize($item->getProductTemplate()->getValuesData());

        $data = [
            'sku'         => $item->getCode(),
            'image'       => $this->getMediaAttribute($this->getImageAttribute(), $normalized, $item),
            'thumbnail'   => $this->getMediaAttribute($this->getThumbnailAttribute(), $normalized, $item),
            'small_image' => $this->getMediaAttribute($this->getSmallImageAttribute(), $normalized, $item),
            'gallery'     => $this->getGalleryAttribute($this->getGalleryAttributes(), $normalized, $item),
        ];

        return $data;
    }

    /**
     * @param string $attributeCode
     * @param array $normalized
     * @param GroupInterface $item
     * @return string
     */
    private function getMediaAttribute($attributeCode, array $normalized, GroupInterface $item)
    {
        if (!isset($normalized[$attributeCode]) ||
            empty($normalized[$attributeCode])
        ) {
            return null;
        }

        $payload = current($normalized[$attributeCode]);
        if (!isset($payload['data']['originalFilename']) || empty($payload['data']['originalFilename'])) {
            return null;
        }

        $newPath = sprintf('files/%s/%s/%s',
            $item->getCode(),
            $attributeCode,
            $payload['data']['originalFilename']
        );

        return $newPath;
    }

    /**
     * @param array          $attributeCodes
     * @param array          $normalized
     * @param GroupInterface $item
     *
     * @return string
     */
    private function getGalleryAttribute(array $attributeCodes, array $normalized, GroupInterface $item)
    {
        $values = [];
        foreach ($attributeCodes as $attributeCode) {
            $value = $this->getMediaAttribute($attributeCode, $normalized, $item);
            if ($value === null) {
                continue;
            }
            $values[] = $value;
        }

        return implode(',', $values);
    }
}
