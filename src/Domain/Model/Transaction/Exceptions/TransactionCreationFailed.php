<?php

declare(strict_types=1);

namespace App\Domain\Model\Transaction\Exceptions;

use RuntimeException;
use Throwable;
use function sprintf;

final class TransactionCreationFailed extends RuntimeException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function wrap(Throwable $previous) : self
    {
        return new self(
            sprintf('Transaction creation failed: %s', $previous->getMessage()),
            $previous,
        );
    }
}
