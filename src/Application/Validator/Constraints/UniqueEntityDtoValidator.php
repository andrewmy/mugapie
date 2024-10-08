<?php

declare(strict_types=1);

namespace App\Application\Validator\Constraints;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use function assert;
use function class_exists;
use function count;
use function is_object;
use function property_exists;
use function sprintf;

/**
 * The default UniqueEntityValidator can't deal with DTOs.
 * Limiting this implementation to a single non-null non-association field.
 */
final class UniqueEntityDtoValidator extends ConstraintValidator
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed      $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (! $constraint instanceof UniqueEntityDto) {
            throw new UnexpectedTypeException($constraint, UniqueEntityDto::class);
        }

        if ($value === null) {
            return;
        }

        assert(is_object($value));

        if (! property_exists($value, $constraint->field)) {
            throw new ConstraintDefinitionException('Specify a valid readable field.');
        }

        if ($constraint->entityClass === '' || ! class_exists($constraint->entityClass)) {
            throw new ConstraintDefinitionException('Specify a valid entity class.');
        }

        if (! property_exists($value, $constraint->referenceEntityField)) {
            throw new ConstraintDefinitionException(
                'Specify a valid reference entity field.',
            );
        }

        $reference = $value->{$constraint->referenceEntityField};

        $classMeta = $this->entityManager->getClassMetadata($constraint->entityClass);

        $criteria = [];

        if (! $classMeta->hasField($constraint->field)) {
            throw new ConstraintDefinitionException(
                sprintf(
                    'The field "%s" is not mapped by Doctrine, so it cannot be validated for uniqueness.',
                    $constraint->field,
                ),
            );
        }

        $criteria[$constraint->field] = $value->{$constraint->field};

        /** @var object[] $result */
        $result = $this->entityManager
            ->getRepository($constraint->entityClass)
            ->findBy($criteria);

        if (
            count($result) === 0 ||
            (count($result) === 1 && $result[0] === $reference)
        ) {
            return;
        }

        $invalidValue = $criteria[$constraint->field];

        $this->context->buildViolation($constraint->message)
            ->atPath($constraint->field)
            ->setParameter(
                '{{ value }}',
                $this->formatValue($invalidValue),
            )
            ->setInvalidValue($invalidValue)
            ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($result)
            ->addViolation();
    }
}
