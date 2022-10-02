<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Application\Interfaces\TransactionalExecutor;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTransactionalExecutor implements TransactionalExecutor
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(callable $operation): void
    {
        $this->entityManager->wrapInTransaction($operation);
    }
}
