<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Interfaces;

use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Product\Product;

interface OrderRepository
{
    /**
     * @throws OrderPersistenceFailed
     */
    public function save(Order $order) : void;

    /**
     * @return Order[]
     */
    public function findAllPendingHavingProduct(Product $product) : array;
}
