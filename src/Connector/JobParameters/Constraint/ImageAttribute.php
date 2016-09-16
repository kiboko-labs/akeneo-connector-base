<?php

namespace Kiboko\Component\Connector\JobParameters\Constraint;

use Symfony\Component\Validator\Constraint;

class ImageAttribute extends Constraint
{
    /**
     * @var string
     */
    public $message = 'There is no image attribute {{ code }} in the PIM.';
}
