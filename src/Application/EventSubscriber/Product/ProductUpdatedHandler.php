<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\Product;

use App\Domain\Calculator\Interfaces\OrderCostCalculator;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\OrderItem\Interfaces\OrderItemFactory;
use App\Domain\Model\OrderItem\OrderItem;
use App\Domain\Model\Product\Events\ProductUpdated;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ProductUpdatedHandler implements EventSubscriberInterface
{
    private OrderRepository $orderRepository;

    private OrderCostCalculator $orderCostCalculator;

    private OrderItemFactory $orderItemFactory;

    private LoggerInterface $logger;

    public function __construct(
        OrderRepository $orderRepository,
        OrderCostCalculator $orderCostCalculator,
        OrderItemFactory $orderItemFactory,
        LoggerInterface $logger
    ) {
        $this->orderRepository     = $orderRepository;
        $this->orderCostCalculator = $orderCostCalculator;
        $this->orderItemFactory    = $orderItemFactory;
        $this->logger              = $logger;
    }

    /**
     * @return array<string, string>
     *
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents() : array
    {
        return [ProductUpdated::class => 'handle'];
    }

    public function handle(ProductUpdated $event) : void
    {
        $product = $event->product();
        $orders  = $this->orderRepository->findAllPendingHavingProduct($product);
        foreach ($orders as $order) {
            $oldItem = $order->items()->filter(
                static function (OrderItem $item) use ($product) : bool {
                    return $item->product() === $product;
                },
            )->first();
            if (! $oldItem instanceof OrderItem) {
                continue;
            }

            $order->removeItem($oldItem);

            $this->orderItemFactory->create(
                $order,
                new CreateOrderItem($product, $oldItem->units()),
            );

            $order->updateOrderCost(
                $this->orderCostCalculator->calculate(
                    $order->shippingType(),
                    $order->shippingAddress(),
                    $order->items()->toArray(),
                ),
            );

            $this->orderRepository->save($order);

            $this->logger->info('Updated order cost', [
                'order_id' => (string) $order->id(),
                'cost' => $order->orderCost(),
            ]);
        }
    }
}
