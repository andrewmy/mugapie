<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Events;

use App\Domain\Model\Common\Interfaces\Event;
use App\Domain\Model\Product\Product;

final class ProductUpdated implements Event
{
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function product() : Product
    {
        return $this->product;
    }
}
