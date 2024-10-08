<?php

declare(strict_types=1);

namespace App\Tests\Mocks;

use App\Application\Interfaces\TransactionalExecutor;

final class NullTransactionalExecutor implements TransactionalExecutor
{
    public function execute(callable $operation): void
    {
        $operation();
    }
}
