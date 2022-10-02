<?php

declare(strict_types=1);

namespace App\Application\Interfaces;

interface TransactionalExecutor
{
    public function execute(callable $operation): void;
}
