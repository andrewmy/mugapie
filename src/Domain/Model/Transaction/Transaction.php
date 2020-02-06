<?php

declare(strict_types=1);

namespace App\Domain\Model\Transaction;

use App\Domain\Model\Common\Interfaces\RecordsEvents;
use App\Domain\Model\Common\Traits\EventRecorder;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Transaction\Events\TransactionCreated;
use App\Domain\Model\User\User;
use Carbon\Carbon;
use DateTimeInterface;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class Transaction implements RecordsEvents
{
    use EventRecorder;

    private UuidInterface $id;

    private ?int $incrementalId;

    private DateTimeInterface $createdAt;

    private User $user;

    private ?Order $order;

    private Money $amount;

    private function __construct()
    {
        $this->createdAt = Carbon::now();

        $this->recordThat(new TransactionCreated($this));
    }

    public static function createForUser(
        TransactionId $id,
        User $user,
        Money $amount
    ) : self {
        $obj     = new self();
        $obj->id = $id->value();
        $user->addTransaction($obj);
        $obj->amount = $amount;

        return $obj;
    }

    public static function createForOrder(
        TransactionId $id,
        Order $order,
        Money $amount
    ) : self {
        $obj     = new self();
        $obj->id = $id->value();
        $order->user()->addTransaction($obj);
        $obj->order  = $order;
        $obj->amount = $amount;

        return $obj;
    }

    public function id() : TransactionId
    {
        return new TransactionId($this->id);
    }

    public function incrementalId() : ?int
    {
        return $this->incrementalId;
    }

    public function createdAt() : DateTimeInterface
    {
        return $this->createdAt;
    }

    public function user() : User
    {
        return $this->user;
    }

    public function linkUser(User $user) : void
    {
        $this->user = $user;
    }

    public function order() : ?Order
    {
        return $this->order;
    }

    public function amount() : Money
    {
        return $this->amount;
    }
}
