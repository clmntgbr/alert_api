<?php

namespace App\Validator;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class EanConstraint extends Constraint
{
    public string $message = 'Ean is not valid.';

    #[HasNamedArguments]
    public function __construct(
        public string $mode = 'strict',
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}
