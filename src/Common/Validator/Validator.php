<?php

declare(strict_types=1);

namespace App\Common\Validator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface as ValidationErrors;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

class Validator implements ValidatorInterface
{
    private SymfonyValidatorInterface $validator;

    public function __construct(SymfonyValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @inheritDoc
     **/
    public function validate($value, $constraints = null, $groups = null): void
    {
        $validationErrors = $this->validator->validate($value, $constraints, $groups);
        if ($validationErrors->count()) {
            $this->createBadRequestHttpException($validationErrors);
        }
    }

    /**
     * @param ValidationErrors $validationErrors
     */
    private function createBadRequestHttpException(ValidationErrors $validationErrors): void
    {
        foreach ($validationErrors as $error) {
            throw new BadRequestHttpException($error->getMessage());
        }
    }
}
