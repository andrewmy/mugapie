<?php

declare(strict_types=1);

namespace App\Application\Transformer\OrderItem;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Application\Dto\OrderItem\OrderItemOutput;
use App\Domain\Model\OrderItem\OrderItem;
use function assert;

final class OrderItemOutputTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []) : OrderItemOutput
    {
        assert($object instanceof OrderItem);

        $output               = new OrderItemOutput();
        $output->id           = $object->id()->value();
        $output->createdAt    = $object->createdAt();
        $output->product      = $object->product();
        $output->productType  = (string) $object->productType();
        $output->title        = $object->title();
        $output->sku          = $object->sku();
        $output->pricePerUnit = (int) $object->pricePerUnit()->getAmount();
        $output->units        = $object->units();

        return $output;
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []) : bool
    {
        return $to === OrderItemOutput::class && $data instanceof OrderItem;
    }
}
