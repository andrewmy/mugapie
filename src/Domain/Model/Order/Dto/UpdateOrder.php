<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Dto;

use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use Doctrine\Common\Collections\ArrayCollection;

final class UpdateOrder
{
    use HasOrderDtoFields;

    public function __construct(
        ShippingType $shippingType,
        ShippingAddress $shippingAddress
    ) {
        $this->shippingType    = $shippingType;
        $this->shippingAddress = $shippingAddress;

        $this->items = new ArrayCollection();
    }
}
