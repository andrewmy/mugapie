<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Dto;

use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\User;
use Money\Money;

final class CreateProduct
{
    private User $user;

    private ProductType $type;

    private string $title;

    private string $sku;

    private Money $cost;

    public function __construct(
        User $user,
        ProductType $type,
        string $title,
        string $sku,
        Money $cost
    ) {
        $this->user  = $user;
        $this->type  = $type;
        $this->title = $title;
        $this->sku   = $sku;
        $this->cost  = $cost;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function type(): ProductType
    {
        return $this->type;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function cost(): Money
    {
        return $this->cost;
    }
}
