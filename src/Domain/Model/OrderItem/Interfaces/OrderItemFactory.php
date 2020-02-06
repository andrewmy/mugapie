<?php

declare(strict_types=1);

namespace App\Domain\Model\OrderItem\Interfaces;

use App\Domain\Model\Order\Order;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\OrderItem\Exceptions\OrderItemCreationFailed;
use App\Domain\Model\OrderItem\OrderItem;

interface OrderItemFactory
{
    /**
     * @throws OrderItemCreationFailed
     */
    public function create(Order $order, CreateOrderItem $data) : OrderItem;
}
