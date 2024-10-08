<?php

declare(strict_types=1);

namespace App\Domain\Model\Order;

use App\Domain\Model\Common\EnumString;

final class OrderStatus extends EnumString
{
    public const PENDING    = 'pending';
    public const PRODUCTION = 'production';

    /** @return string[] */
    public static function validValues(): array
    {
        return [
            self::PENDING,
            self::PRODUCTION,
        ];
    }
}
