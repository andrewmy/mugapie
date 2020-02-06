<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Interfaces;

use App\Domain\Model\Product\Exceptions\ProductPersistenceFailed;
use App\Domain\Model\Product\Product;

interface ProductRepository
{
    /**
     * @throws ProductPersistenceFailed
     */
    public function save(Product $product) : void;

    /**
     * @throws ProductPersistenceFailed
     */
    public function delete(Product $product) : void;
}
