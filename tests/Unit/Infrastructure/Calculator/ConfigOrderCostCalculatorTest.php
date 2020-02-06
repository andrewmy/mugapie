<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Calculator;

use App\Domain\Calculator\Exceptions\ShippingCalculationFailed;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Infrastructure\Calculator\ConfigOrderCostCalculator;
use PHPUnit\Framework\TestCase;

final class ConfigOrderCostCalculatorTest extends TestCase
{
    public function testCalculateFails() : void
    {
        $calculator = new ConfigOrderCostCalculator([], 'USD');

        $this->expectException(ShippingCalculationFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Invalid shipping type "standard" for the given address.',
        );

        $calculator->calculate(
            new ShippingType(ShippingType::STANDARD),
            ShippingAddress::createDomestic(
                '-',
                'Riga',
                'Z1',
                'LV-1001',
                '123',
                'A B',
            ),
            [],
        );
    }
}
