<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

use App\Domain\Model\Common\EnumString;

final class ProductType extends EnumString
{
    public const MUG    = 'mug';
    public const TSHIRT = 'tshirt';

    /**
     * @return string[]
     */
    public static function validValues() : array
    {
        return [
            self::MUG,
            self::TSHIRT,
        ];
    }
}
