<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Validator\Constraints;

use Kiboko\Component\Connector\Manager\AttributeManager;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ImageAttributeListValidator extends ConstraintValidator
{
    /**
     * @var AttributeManager
     */
    private $attributeManager;

    /**
     * ImageAttributeValidator constructor.
     *
     * @param AttributeManager $attributeManager
     */
    public function __construct(
        AttributeManager $attributeManager
    ) {
        $this->attributeManager = $attributeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }
        
        if (!is_array($value) || !$value instanceof \Traversable) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->messageList)
                    ->setParameter('{{ type }}', is_object($value) ? get_class($value) : gettype($value))
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->buildViolation($constraint->messageList)
                    ->setParameter('{{ type }}', is_object($value) ? get_class($value) : gettype($value))
                    ->setInvalidValue($value)
                    ->addViolation();
            }
        }

        $validCodes = [];
        foreach ($this->attributeManager->getAttributes('pim_catalog_image') as $attribute) {
            $validCodes[] = $attribute->getCode();
        }

        foreach ($value as $code) {
            if (!in_array($code, $validCodes)) {
                if ($this->context instanceof ExecutionContextInterface) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ code }}', $code)
                        ->setInvalidValue($code)
                        ->addViolation();
                } else {
                    $this->buildViolation($constraint->message)
                        ->setParameter('{{ code }}', $code)
                        ->setInvalidValue($code)
                        ->addViolation();
                }

                return;
            }
        }
    }
}
