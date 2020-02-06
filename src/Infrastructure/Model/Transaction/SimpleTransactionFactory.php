<?php

declare(strict_types=1);

namespace App\Infrastructure\Model\Transaction;

use App\Domain\Model\Order\Order;
use App\Domain\Model\Transaction\Exceptions\TransactionCreationFailed;
use App\Domain\Model\Transaction\Interfaces\TransactionFactory;
use App\Domain\Model\Transaction\Transaction;
use App\Domain\Model\Transaction\TransactionId;
use App\Domain\Model\User\User;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Throwable;

final class SimpleTransactionFactory implements TransactionFactory
{
    public function createForUser(User $user, Money $amount) : Transaction
    {
        try {
            return Transaction::createForUser(
                new TransactionId(Uuid::uuid4()),
                $user,
                $amount,
            );
        } catch (Throwable $exception) {
            throw TransactionCreationFailed::wrap($exception);
        }
    }

    public function createForOrder(Order $order, Money $amount) : Transaction
    {
        try {
            return Transaction::createForOrder(
                new TransactionId(Uuid::uuid4()),
                $order,
                $amount,
            );
        } catch (Throwable $exception) {
            throw TransactionCreationFailed::wrap($exception);
        }
    }
}
