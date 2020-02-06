<?php

declare(strict_types=1);

namespace App\Domain\Model\Common;

use Assert\Assert;

abstract class EnumString
{
    protected string $value;

    public function __construct(string $value)
    {
        Assert::that($value)->inArray(static::validValues());

        $this->value = $value;
    }

    public function __toString() : string
    {
        return $this->value();
    }

    public function value() : string
    {
        return $this->value;
    }

    /**
     * @return string[]
     */
    abstract public static function validValues() : array;
}
