<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Exceptions;

use RuntimeException;
use Throwable;

use function sprintf;

final class UserPersistenceFailed extends RuntimeException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function saveFailed(Throwable $previous): self
    {
        return new self(
            sprintf('User save failed: %s', $previous->getMessage()),
            $previous,
        );
    }

    public static function deleteFailed(Throwable $previous): self
    {
        return new self(
            sprintf('User delete failed: %s', $previous->getMessage()),
            $previous,
        );
    }
}
