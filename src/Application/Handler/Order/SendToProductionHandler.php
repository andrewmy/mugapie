<?php

declare(strict_types=1);

namespace App\Application\Handler\Order;

use App\Application\Exceptions\OrderOperationBadRequest;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;
use Psr\Log\LoggerInterface;

class SendToProductionHandler
{
    private OrderRepository $orderRepository;

    private LoggerInterface $logger;

    public function __construct(
        OrderRepository $orderRepository,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->logger          = $logger;
    }

    public function handle(Order $order): Order
    {
        if (! $order->isEditable()) {
            throw OrderOperationBadRequest::notEditable();
        }

        if ($order->orderCost()->greaterThan($order->user()->balance())) {
            throw OrderOperationBadRequest::costTooHigh(
                $order->orderCost(),
                $order->user()->balance(),
            );
        }

        $order->sendToProduction();

        $this->orderRepository->save($order);

        $this->logger->info('Order sent to production', [
            'order_id' => (string) $order->id(),
        ]);

        return $order;
    }
}
