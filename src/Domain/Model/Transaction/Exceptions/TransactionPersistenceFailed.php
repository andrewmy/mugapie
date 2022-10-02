<?php

declare(strict_types=1);

namespace App\Domain\Model\Transaction\Exceptions;

use RuntimeException;
use Throwable;

use function sprintf;

final class TransactionPersistenceFailed extends RuntimeException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function saveFailed(Throwable $previous): self
    {
        return new self(
            sprintf('Transaction save failed: %s', $previous->getMessage()),
            $previous,
        );
    }
}
