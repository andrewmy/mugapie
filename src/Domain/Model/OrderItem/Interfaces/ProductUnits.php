<?php

declare(strict_types=1);

namespace App\Domain\Model\OrderItem\Interfaces;

use App\Domain\Model\Product\Product;

interface ProductUnits
{
    public function product(): Product;

    public function units(): int;
}
