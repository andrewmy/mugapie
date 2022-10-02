<?php

declare(strict_types=1);

namespace App\Domain\Model\Transaction\Events;

use App\Domain\Model\Common\Interfaces\Event;
use App\Domain\Model\Transaction\Transaction;

final class TransactionCreated implements Event
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function transaction(): Transaction
    {
        return $this->transaction;
    }
}
