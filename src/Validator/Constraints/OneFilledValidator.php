<?php

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class OneFilledValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (null === $value->getCategory() && null === $value->getArticle()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}