<?php

declare(strict_types=1);

namespace App\Domain\Model\Common;

use Ramsey\Uuid\UuidInterface;

abstract class BaseId
{
    private UuidInterface $value;

    public function __construct(UuidInterface $value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value()->toString();
    }

    public function value(): UuidInterface
    {
        return $this->value;
    }
}
