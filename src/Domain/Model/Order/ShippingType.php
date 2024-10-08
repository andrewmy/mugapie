<?php

declare(strict_types=1);

namespace App\Domain\Model\Order;

use App\Domain\Model\Common\EnumString;

final class ShippingType extends EnumString
{
    public const STANDARD = 'standard';
    public const EXPRESS  = 'express';

    /** @return string[] */
    public static function validValues(): array
    {
        return [
            self::STANDARD,
            self::EXPRESS,
        ];
    }
}
