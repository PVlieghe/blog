<?php
// src/Validator/UniqueStepNumberValidator.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use App\Entity\Recipe;

class UniqueStepNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueStepNumber) {
            throw new UnexpectedTypeException($constraint, UniqueStepNumber::class);
        }

        if (!$value instanceof Recipe) {
            throw new UnexpectedValueException($value, Recipe::class);
        }

        $stepNumbers = [];
        foreach ($value->getSteps() as $step) {
            if (in_array($step->getNumber(), $stepNumbers)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $step->getNumber())
                    ->addViolation();

                return; // Stop after the first violation is found
            }
            $stepNumbers[] = $step->getNumber();
        }
    }
}
