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
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ImageAttributeValidator extends ConstraintValidator
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
        
        foreach ($this->attributeManager->getAttributes('pim_catalog_image') as $attribute) {
            if ($attribute->getCode() === $value) {
                return;
            }
        }

        if ($this->context instanceof ExecutionContextInterface) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        } else {
            $this->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
