<?php

// src/Validator/UniqueStepNumber.php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueStepNumber extends Constraint
{
    public $message = 'Deux étapes possèdent le même ordre: {{value}}';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
