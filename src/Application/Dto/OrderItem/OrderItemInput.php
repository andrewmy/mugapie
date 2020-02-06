<?php

declare(strict_types=1);

namespace App\Application\Dto\OrderItem;

use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\Product\Product;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class OrderItemInput
{
    /**
     * @var Product
     * @Assert\NotNull()
     * @Groups({"order:post", "order:put"})
     */
    public Product $product;

    /**
     * @var int
     * @Assert\Positive()
     * @Groups({"order:post", "order:put"})
     */
    public int $units;

    public function toDomainCreate() : CreateOrderItem
    {
        return new CreateOrderItem(
            $this->product,
            $this->units,
        );
    }
}
