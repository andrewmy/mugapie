<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Dto;

use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class UpdateOrder
{
    private ShippingType $shippingType;

    private ShippingAddress $shippingAddress;

    /** @var Collection<int, CreateOrderItem> */
    private Collection $items;

    public function __construct(
        ShippingType $shippingType,
        ShippingAddress $shippingAddress
    ) {
        $this->shippingType    = $shippingType;
        $this->shippingAddress = $shippingAddress;

        $this->items = new ArrayCollection();
    }

    public function shippingType() : ShippingType
    {
        return $this->shippingType;
    }

    public function shippingAddress() : ShippingAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return Collection<int, CreateOrderItem>
     */
    public function items() : Collection
    {
        return $this->items;
    }

    public function addItem(CreateOrderItem $item) : void
    {
        if ($this->items()->contains($item)) {
            return;
        }

        $this->items()->add($item);
    }
}
