<?php

declare(strict_types=1);

namespace App\Application\Dto\Product;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Duplicating typehints for the serializer
 */
final class ProductOutput
{
    /** @var UuidInterface */
    public UuidInterface $id;

    /** @var DateTimeInterface */
    public DateTimeInterface $createdAt;

    /** @var DateTimeInterface */
    public DateTimeInterface $updatedAt;

    /** @var string */
    public string $type;

    /** @var string */
    public string $title;

    /** @var string */
    public string $sku;

    /** @var int */
    public int $cost;
}
