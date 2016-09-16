<?php

namespace Kiboko\Component\Connector\JobParameters\FormConfigurationProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Model\JobInstance;
use Kiboko\Component\Connector\Manager\AttributeManager;

class ProductAssetsExport
{
    /**
     * @var array
     */
    protected $supportedJobNames;

    /**
     * @var AttributeManager
     */
    private $attributeManager;

    /**
     * ProductAssetsExport constructor.
     *
     * @param array $supportedJobNames
     * @param AttributeManager $attributeManager
     */
    public function __construct(
        array $supportedJobNames,
        AttributeManager $attributeManager
    ) {
        $this->supportedJobNames = $supportedJobNames;
        $this->attributeManager = $attributeManager;
    }

    /**
     * @param JobInstance $jobInstance
     * @return array
     */
    public function getFormConfiguration(JobInstance $jobInstance)
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

    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
