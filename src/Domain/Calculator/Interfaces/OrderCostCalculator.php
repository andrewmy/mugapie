<?php

declare(strict_types=1);

namespace App\Domain\Calculator\Interfaces;

use App\Domain\Calculator\Exceptions\ShippingCalculationFailed;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Interfaces\ProductUnits;
use Money\Money;

interface OrderCostCalculator
{
    /**
     * @param ProductUnits[] $items
     *
     * @throws ShippingCalculationFailed
     */
    public function calculate(
        ShippingType $shippingType,
        ShippingAddress $address,
        array $items
    ) : Money;
}
