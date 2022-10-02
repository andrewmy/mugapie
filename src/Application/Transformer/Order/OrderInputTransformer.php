<?php

declare(strict_types=1);

namespace App\Application\Transformer\Order;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use ApiPlatform\Validator\ValidatorInterface;
use App\Application\Dto\Order\OrderInput;
use App\Application\Exceptions\OrderOperationBadRequest;
use App\Application\Exceptions\OrderOperationFailed;
use App\Domain\Calculator\Exceptions\ShippingCalculationFailed;
use App\Domain\Calculator\Interfaces\OrderCostCalculator;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Order\OrderId;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\OrderItem\Exceptions\OrderItemCreationFailed;
use App\Domain\Model\OrderItem\Interfaces\OrderItemFactory;
use App\Domain\Model\User\User;
use Ramsey\Uuid\Uuid;
use function assert;
use function count;

final class OrderInputTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    private OrderCostCalculator $orderCostCalculator;

    private OrderItemFactory $orderItemFactory;

    public function __construct(
        ValidatorInterface $validator,
        OrderCostCalculator $orderCostCalculator,
        OrderItemFactory $orderItemFactory
    ) {
        $this->validator           = $validator;
        $this->orderCostCalculator = $orderCostCalculator;
        $this->orderItemFactory    = $orderItemFactory;
    }

    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []) : Order
    {
        assert($object instanceof OrderInput);

        $order = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        if ($order instanceof Order) {
            if (($context['item_operation_name'] ?? null) === 'send_to_production') {
                return $order;
            }

            return $this->transformToUpdated($object, $order);
        }

        return $this->transformToCreated($object);
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []) : bool
    {
        if ($data instanceof Order) {
            return false;
        }

        return $to === Order::class && ($context['input']['class'] ?? null) !== null;
    }

    private function transformToUpdated(OrderInput $object, Order $order) : Order
    {
        $this->validator->validate(
            $object,
            [
                'groups' => ShippingAddress::isDomesticCountry($object->countryCode)
                    ? 'put_home'
                    : 'put_world',
            ],
        );

        if (! $order->isEditable()) {
            throw OrderOperationBadRequest::notEditable();
        }

        $this->checkForForeignProducts($object, $order->user());

        $data = $object->toDomainUpdate();
        try {
            $orderCost = $this->orderCostCalculator->calculate(
                $data->shippingType(),
                $data->shippingAddress(),
                $data->items()->toArray(),
            );
        } catch (ShippingCalculationFailed $exception) {
            // passed validation but still failed => corrupted config

            throw OrderOperationFailed::wrap($exception);
        }

        if ($orderCost->greaterThan($order->user()->balance())) {
            throw OrderOperationBadRequest::costTooHigh(
                $orderCost,
                $order->user()->balance(),
            );
        }

        $order->update($data, $orderCost);
        try {
            foreach ($data->items() as $item) {
                $this->orderItemFactory->create($order, $item);
            }
        } catch (OrderItemCreationFailed $exception) {
            throw OrderOperationFailed::wrap($exception);
        }

        return $order;
    }

    private function transformToCreated(OrderInput $object) : Order
    {
        $this->validator->validate(
            $object,
            [
                'groups' => ShippingAddress::isDomesticCountry($object->countryCode)
                    ? 'post_home'
                    : 'post_world',
            ]
        );

        $user = $object->user;
        assert($user !== null);

        $this->checkForForeignProducts($object, $user);

        $data = $object->toDomainCreate();
        try {
            $orderCost = $this->orderCostCalculator->calculate(
                $data->shippingType(),
                $data->shippingAddress(),
                $data->items()->toArray(),
            );
        } catch (ShippingCalculationFailed $exception) {
            throw OrderOperationFailed::wrap($exception);
        }

        if ($orderCost->greaterThan($user->balance())) {
            throw OrderOperationBadRequest::costTooHigh(
                $orderCost,
                $user->balance(),
            );
        }

        $order = Order::create(new OrderId(Uuid::uuid4()), $data, $orderCost);
        try {
            foreach ($data->items() as $item) {
                $this->orderItemFactory->create($order, $item);
            }
        } catch (OrderItemCreationFailed $exception) {
            throw OrderOperationFailed::wrap($exception);
        }

        return $order;
    }

    private function checkForForeignProducts(OrderInput $input, User $user) : void
    {
        $foreignProducts = [];
        foreach ($input->items as $item) {
            if ($item->product->user()->id()->value()->equals($user->id()->value())) {
                continue;
            }

            $foreignProducts[] = $item->product->id();
        }

        if (count($foreignProducts) > 0) {
            throw OrderOperationBadRequest::hasForeignProducts($foreignProducts);
        }
    }
}
