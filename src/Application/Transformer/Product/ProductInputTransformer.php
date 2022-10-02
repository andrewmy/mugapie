<?php

declare(strict_types=1);

namespace App\Application\Transformer\Product;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use ApiPlatform\Validator\ValidatorInterface;
use App\Application\Dto\Product\ProductInput;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use Ramsey\Uuid\Uuid;

use function assert;

final class ProductInputTransformer implements DataTransformerInterface
{
    private string $currency;

    private ValidatorInterface $validator;

    public function __construct(string $currency, ValidatorInterface $validator)
    {
        $this->currency  = $currency;
        $this->validator = $validator;
    }

    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): Product
    {
        assert($object instanceof ProductInput);

        $product = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        assert($product instanceof Product || $product === null);
        $object->referenceEntity = $product;

        if ($product instanceof Product) {
            $this->validator->validate($object, ['groups' => 'put']);
            $product->update($object->toDomainUpdate($this->currency));

            return $product;
        }

        $this->validator->validate($object, ['groups' => 'post']);

        return Product::create(
            new ProductId(Uuid::uuid4()),
            $object->toDomainCreate($this->currency),
        );
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Product) {
            return false;
        }

        return $to === Product::class && ($context['input']['class'] ?? null) !== null;
    }
}
