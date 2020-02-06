<?php

declare(strict_types=1);

namespace App\Infrastructure\Model\Transaction;

use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\Transaction\Interfaces\TransactionRepository;
use App\Domain\Model\Transaction\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

final class DoctrineTransactionRepository implements TransactionRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function save(Transaction $transaction) : void
    {
        try {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw TransactionPersistenceFailed::saveFailed($exception);
        }
    }
}
