<?php

namespace Kiboko\Component\Connector\JobParameters\Constraint;

use Symfony\Component\Validator\Constraint;

class ImageAttributeList extends Constraint
{
    /**
     * @var string
     */
    public $message = 'There is no image attribute {{ code }} in the PIM.';

    /**
     * @var string
     */
    public $messageList = 'Value should be an array, {{ type }} provided.';
}
