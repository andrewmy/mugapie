<?php

declare(strict_types=1);

namespace App\Infrastructure\Model\OrderItem;

use App\Domain\Model\Order\Order;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\OrderItem\Exceptions\OrderItemCreationFailed;
use App\Domain\Model\OrderItem\Interfaces\OrderItemFactory;
use App\Domain\Model\OrderItem\OrderItem;
use App\Domain\Model\OrderItem\OrderItemId;
use Ramsey\Uuid\Uuid;
use Throwable;

class SimpleOrderItemFactory implements OrderItemFactory
{
    public function create(Order $order, CreateOrderItem $data) : OrderItem
    {
        try {
            return OrderItem::create(
                new OrderItemId(Uuid::uuid4()),
                $order,
                $data,
            );
        } catch (Throwable $exception) {
            throw OrderItemCreationFailed::wrap($exception);
        }
    }
}
