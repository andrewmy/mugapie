<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Dto;

use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\Product\Product;
use Doctrine\Common\Collections\Collection;

trait HasOrderDtoFields
{
    private ShippingType $shippingType;

    private ShippingAddress $shippingAddress;

    /** @var Collection<int, CreateOrderItem> */
    private Collection $items;

    public function shippingType(): ShippingType
    {
        return $this->shippingType;
    }

    public function shippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }

    /** @return Collection<int, CreateOrderItem> */
    public function items(): Collection
    {
        return $this->items;
    }

    public function addItem(CreateOrderItem $item): void
    {
        if ($this->items()->contains($item)) {
            return;
        }

        $sameProductItem = $this->itemWithProduct($item->product());
        if ($sameProductItem === null) {
            $this->items->add($item);

            return;
        }

        $this->items->removeElement($sameProductItem);
        $this->items->add(
            new CreateOrderItem(
                $item->product(),
                $item->units() + $sameProductItem->units(),
            ),
        );
    }

    private function itemWithProduct(Product $product): ?CreateOrderItem
    {
        $item = $this->items()->filter(
            static function (CreateOrderItem $item) use ($product): bool {
                return $item->product() === $product;
            },
        )->first();

        if ($item === false) {
            return null;
        }

        return $item;
    }
}
