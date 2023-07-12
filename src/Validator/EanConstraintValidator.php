<?php

namespace App\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[\Attribute]
class EanConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EanConstraint) {
            throw new UnexpectedTypeException($constraint, EanConstraint::class);
        }

        if (!preg_match("/^[0-9]{13}$/", $value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
            return;
        }

        $digits = $value;

        $even_sum = $digits[1] + $digits[3] + $digits[5] + $digits[7] + $digits[9] + $digits[11];

        $even_sum_three = $even_sum * 3;

        $odd_sum = $digits[0] + $digits[2] + $digits[4] + $digits[6] + $digits[8] + $digits[10];

        $total_sum = $even_sum_three + $odd_sum;

        $next_ten = (ceil($total_sum / 10)) * 10;
        $check_digit = $next_ten - $total_sum;


        if ($check_digit == $digits[12]) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->addViolation();
    }
}
