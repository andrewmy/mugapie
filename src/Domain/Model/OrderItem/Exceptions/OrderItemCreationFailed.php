<?php

declare(strict_types=1);

namespace App\Domain\Model\OrderItem\Exceptions;

use RuntimeException;
use Throwable;

use function sprintf;

final class OrderItemCreationFailed extends RuntimeException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function wrap(Throwable $previous): self
    {
        return new self(
            sprintf('Order item creation failed: %s', $previous->getMessage()),
            $previous,
        );
    }
}
