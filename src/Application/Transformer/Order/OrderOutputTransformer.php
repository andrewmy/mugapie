<?php

declare(strict_types=1);

namespace App\Application\Transformer\Order;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Application\Dto\Order\OrderOutput;
use App\Domain\Model\Order\Order;
use function assert;

final class OrderOutputTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []) : OrderOutput
    {
        assert($object instanceof Order);

        $output               = new OrderOutput();
        $output->id           = $object->id()->value();
        $output->createdAt    = $object->createdAt();
        $output->updatedAt    = $object->updatedAt();
        $output->shippingType = (string) $object->shippingType();

        $output->countryCode = $object->shippingAddress()->countryCode();
        $output->region      = $object->shippingAddress()->region();
        $output->city        = $object->shippingAddress()->city();
        $output->zip         = $object->shippingAddress()->zip();
        $output->phone       = $object->shippingAddress()->phone();
        $output->fullName    = $object->shippingAddress()->fullName();

        if ($object->shippingAddress()->isDomestic()) {
            $output->street = $object->shippingAddress()->street();
        } else {
            $output->address = $object->shippingAddress()->address();
        }

        $output->status    = (string) $object->status();
        $output->orderCost = (int) $object->orderCost()->getAmount();

        return $output;
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []) : bool
    {
        return $to === OrderOutput::class && $data instanceof Order;
    }
}
