<?php

declare(strict_types=1);

namespace App\Application\Handler\Order;

use App\Application\Exceptions\OrderOperationFailed;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;

class SendToProductionHandler
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function handle(Order $order) : Order
    {
        if (! $order->isEditable()) {
            throw OrderOperationFailed::notEditable();
        }

        if ($order->orderCost()->greaterThan($order->user()->balance())) {
            throw OrderOperationFailed::costTooHigh(
                $order->orderCost(),
                $order->user()->balance(),
            );
        }

        $order->sendToProduction();

        $this->orderRepository->save($order);

        return $order;
    }
}
