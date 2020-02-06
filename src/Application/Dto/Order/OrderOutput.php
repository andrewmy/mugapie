<?php

declare(strict_types=1);

namespace App\Application\Dto\Order;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Duplicating typehints for the serializer
 */
final class OrderOutput
{
    /** @var UuidInterface */
    public UuidInterface $id;

    /** @var DateTimeInterface */
    public DateTimeInterface $createdAt;

    /** @var DateTimeInterface */
    public DateTimeInterface $updatedAt;

    /** @var string */
    public string $shippingType;

    /** @var string */
    public string $countryCode;

    /** @var string|null */
    public ?string $region;

    /** @var string */
    public string $city;

    /** @var string|null */
    public ?string $street;

    /** @var string|null */
    public ?string $address;

    /** @var string|null */
    public ?string $zip;

    /** @var string */
    public string $phone;

    /** @var string */
    public string $fullName;

    /** @var string */
    public string $status;

    /** @var int */
    public int $orderCost;
}
