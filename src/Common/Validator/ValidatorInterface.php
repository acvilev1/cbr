<?php

declare(strict_types=1);

namespace App\Common\Validator;

interface ValidatorInterface
{
    public function validate($value, $constraints = null, $groups = null): void;
}
