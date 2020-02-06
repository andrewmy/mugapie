<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Dto;

use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\User\User;
use Doctrine\Common\Collections\ArrayCollection;

final class CreateOrder
{
    use HasOrderDtoFields;

    private User $user;

    public function __construct(
        User $user,
        ShippingType $shippingType,
        ShippingAddress $shippingAddress
    ) {
        $this->user            = $user;
        $this->shippingType    = $shippingType;
        $this->shippingAddress = $shippingAddress;

        $this->items = new ArrayCollection();
    }

    public function user() : User
    {
        return $this->user;
    }
}
