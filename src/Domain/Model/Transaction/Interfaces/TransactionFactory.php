<?php

declare(strict_types=1);

namespace App\Domain\Model\Transaction\Interfaces;

use App\Domain\Model\Order\Order;
use App\Domain\Model\Transaction\Exceptions\TransactionCreationFailed;
use App\Domain\Model\Transaction\Transaction;
use App\Domain\Model\User\User;
use Money\Money;

interface TransactionFactory
{
    /**
     * @throws TransactionCreationFailed
     */
    public function createForUser(User $user, Money $amount) : Transaction;

    /**
     * @throws TransactionCreationFailed
     */
    public function createForOrder(Order $order, Money $amount) : Transaction;
}
