<?php

declare(strict_types=1);

namespace App\Infrastructure\Calculator;

use App\Domain\Calculator\Exceptions\ShippingCalculationFailed;
use App\Domain\Calculator\Interfaces\OrderCostCalculator;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Interfaces\ProductUnits;
use App\Domain\Model\Product\ProductType;
use Money\Currency;
use Money\Money;
use function array_reduce;

final class ConfigOrderCostCalculator implements OrderCostCalculator
{
    /** @var array<string, array<string, array<string, array<string, int>>>> */
    private array $shippingCosts;

    private Currency $currency;

    /**
     * @param array<string, array<string, array<string, array<string, int>>>> $shippingCosts
     */
    public function __construct(array $shippingCosts, string $currency)
    {
        $this->shippingCosts = $shippingCosts;
        $this->currency      = new Currency($currency);
    }

    /**
     * @param ProductUnits[] $items
     */
    public function calculate(
        ShippingType $shippingType,
        ShippingAddress $address,
        array $items
    ) : Money {
        $shippingCosts = $this->shippingCosts[$shippingType->value()][$address->isDomestic() ? 'domestic' : 'international'] ?? null;

        if ($shippingCosts === null) {
            throw ShippingCalculationFailed::invalidShippingType(
                $shippingType->value(),
            );
        }

        $hasItems = [];
        foreach (ProductType::validValues() as $value) {
            $hasItems[$value] = false;
        }

        return array_reduce(
            $items,
            function (Money $carry, ProductUnits $item) use ($shippingCosts, &$hasItems) : Money {
                $type            = $item->product()->type()->value();
                $shippingFirst   = $shippingCosts[$type][$hasItems[$type] ? 'next' : 'first'];
                $hasItems[$type] = true;

                return $carry->add(
                    $item->product()->cost()->multiply($item->units()),
                )->add(new Money($shippingFirst, $this->currency))
                    ->add(
                        (new Money($shippingCosts[$type]['next'], $this->currency))
                            ->multiply($item->units() - 1)
                    );
            },
            new Money(0, $this->currency),
        );
    }
}
