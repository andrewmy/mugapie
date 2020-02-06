<?php

declare(strict_types=1);

namespace App\Application\Dto\User;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Duplicating typehints for the serializer
 */
final class UserOutput
{
    /** @var UuidInterface */
    public UuidInterface $id;

    /** @var DateTimeInterface */
    public DateTimeInterface $createdAt;

    /** @var DateTimeInterface */
    public DateTimeInterface $updatedAt;

    /** @var int */
    public int $balance;

    /** @var string|null */
    public ?string $nickname;
}
