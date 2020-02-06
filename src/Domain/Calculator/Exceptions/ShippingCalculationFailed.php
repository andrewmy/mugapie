<?php

declare(strict_types=1);

namespace App\Domain\Calculator\Exceptions;

use RuntimeException;
use Throwable;
use function sprintf;

final class ShippingCalculationFailed extends RuntimeException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function invalidShippingType(string $type) : self
    {
        return new self(
            sprintf('Invalid shipping type "%s" for the given address.', $type),
        );
    }
}
