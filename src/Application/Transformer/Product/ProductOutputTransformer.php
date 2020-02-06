<?php

declare(strict_types=1);

namespace App\Application\Transformer\Product;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Application\Dto\Product\ProductOutput;
use App\Domain\Model\Product\Product;
use function assert;

final class ProductOutputTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []) : ProductOutput
    {
        assert($object instanceof Product);

        $output            = new ProductOutput();
        $output->id        = $object->id()->value();
        $output->createdAt = $object->createdAt();
        $output->updatedAt = $object->updatedAt();
        $output->type      = (string) $object->type();
        $output->title     = $object->title();
        $output->sku       = $object->sku();
        $output->cost      = (int) $object->cost()->getAmount();

        return $output;
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []) : bool
    {
        return $to === ProductOutput::class && $data instanceof Product;
    }
}
