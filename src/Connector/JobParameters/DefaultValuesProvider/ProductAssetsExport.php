<?php

namespace Kiboko\Component\Connector\JobParameters\DefaultValuesProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;

class ProductAssetsValuesProvider implements DefaultValuesProviderInterface
{
    /**
     * @var array
     */
    protected $supportedJobNames;

    /**
     * ProductAssetsValuesProvider constructor.
     *
     * @param array $supportedJobNames
     */
    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [
            'imageAttribute'      => 'image',
            'thumbnailAttribute'  => 'thumbnail',
            'smallImageAttribute' => 'small_image',
            'galleryAttributes'   => [],
        ];
    }

    /**
     * @param JobInterface $job
     * @return bool
     */
    public function supports(JobInterface $job)
    {
        return in_array($job->getName(), $this->supportedJobNames);
    }
}
