<?php

declare(strict_types=1);

namespace App\Domain\Model\OrderItem\Dto;

use App\Domain\Model\OrderItem\Interfaces\ProductUnits;
use App\Domain\Model\Product\Product;

final class CreateOrderItem implements ProductUnits
{
    private Product $product;

    private int $units;

    public function __construct(
        Product $product,
        int $units
    ) {
        $this->product = $product;
        $this->units   = $units;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function units(): int
    {
        return $this->units;
    }
}
