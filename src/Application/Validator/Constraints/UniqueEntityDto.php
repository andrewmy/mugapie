<?php

declare(strict_types=1);

namespace App\Application\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
final class UniqueEntityDto extends Constraint
{
    public string $message = 'This value is already used.';

    public string $entityClass = '';

    public string $referenceEntityField = '';

    public string $field = '';

    public function validatedBy(): string
    {
        return UniqueEntityDtoValidator::class;
    }

    /** @codeCoverageIgnore */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    /**
     * @return string[]
     *
     * @codeCoverageIgnore
     */
    public function getRequiredOptions(): array
    {
        return ['field', 'entityClass', 'referenceEntityField'];
    }
}
