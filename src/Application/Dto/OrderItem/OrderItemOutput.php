<?php

declare(strict_types=1);

namespace App\Application\Dto\OrderItem;

use App\Domain\Model\Product\Product;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Duplicating typehints for the serializer
 */
final class OrderItemOutput
{
    /** @var UuidInterface */
    public UuidInterface $id;

    /** @var DateTimeInterface */
    public DateTimeInterface $createdAt;

    /** @var Product */
    public Product $product;

    /** @var string */
    public string $productType;

    /** @var string */
    public string $title;

    /** @var string */
    public string $sku;

    /** @var int */
    public int $pricePerUnit;

    /** @var int */
    public int $units;
}
