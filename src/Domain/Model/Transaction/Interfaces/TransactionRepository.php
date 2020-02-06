<?php

declare(strict_types=1);

namespace App\Domain\Model\Transaction\Interfaces;

use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\Transaction\Transaction;

interface TransactionRepository
{
    /**
     * @throws TransactionPersistenceFailed
     */
    public function save(Transaction $transaction) : void;
}
