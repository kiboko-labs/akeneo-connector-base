<?php

namespace Kiboko\Component\Connector\JobParameters\ConstraintCollectionProvider;

use Akeneo\Component\Batch\Job\JobInterface;
use Akeneo\Component\Batch\Job\JobParameters\ConstraintCollectionProviderInterface;
use Kiboko\Component\Connector\JobParameters\Constraint\ImageAttribute;
use Kiboko\Component\Connector\JobParameters\Constraint\ImageAttributeList;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductAssetsConstraintCollectionProvider implements ConstraintCollectionProviderInterface
{
    /**
     * @var array
     */
    protected $supportedJobNames;

    /**
     * ProductAssetsConstraintCollectionProvider constructor.
     *
     * @param array $supportedJobNames
     */
    public function __construct(array $supportedJobNames)
    {
        $this->supportedJobNames = $supportedJobNames;
    }

    public function getConstraintCollection()
    {
        return new Collection(
            [
                'fields' => [
                    'imageAttribute' => [
                        new NotBlank(['groups' => 'Execution']),
                        new ImageAttribute(['groups' => 'Execution']),
                    ],
                    'thumbnailAttribute' => [
                        new NotBlank(['groups' => 'Execution']),
                        new ImageAttribute(['groups' => 'Execution']),
                    ],
                    'smallImageAttribute' => [
                        new NotBlank(['groups' => 'Execution']),
                        new ImageAttribute(['groups' => 'Execution']),
                    ],
                    'galleryAttributes' => [
                        new Count(['groups' => 'Execution', 'min' => 1]),
                        new ImageAttributeList(['groups' => 'Execution']),
                    ],
                ]
            ]
        );
    }

    public function supports(JobInterface $job)
    {
        // TODO: Implement supports() method.
    }
}
