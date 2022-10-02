<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Common\Interfaces\RecordsEvents;
use App\Domain\Model\Common\Traits\EventRecorder;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Transaction\Transaction;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\Dto\UpdateUser;
use App\Domain\Model\User\Events\UserCreated;
use Carbon\Carbon;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * Can't use 7.4 typed properties here because Sf Serializer tries to build a
 * cache key from its Doctrine proxy regardless of serialization groups or anything.
 */
class User implements RecordsEvents
{
    use EventRecorder;

    /** @var UuidInterface */
    private $id;

    /** @var DateTimeInterface */
    private $createdAt;

    /** @var DateTimeInterface */
    private $updatedAt;

    /** @var Money */
    private $balance;

    /** @var string|null */
    private $nickname = null;

    /** @var Collection<int, Product> */
    private $products;

    /** @var Collection<int, Order> */
    private $orders;

    /** @var Collection<int, Transaction> */
    private $transactions;

    private function __construct()
    {
        $this->createdAt    = Carbon::now();
        $this->updatedAt    = Carbon::now();
        $this->products     = new ArrayCollection();
        $this->orders       = new ArrayCollection();
        $this->transactions = new ArrayCollection();

        $this->recordThat(new UserCreated($this));
    }

    public static function create(UserId $id, CreateUser $data): self
    {
        $obj           = new self();
        $obj->id       = $id->value();
        $obj->balance  = $data->balance();
        $obj->nickname = $data->nickname();

        return $obj;
    }

    public function update(UpdateUser $data): void
    {
        $this->nickname = $data->nickname();

        $this->updatedAt = Carbon::now();
    }

    public function id(): UserId
    {
        return new UserId($this->id);
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function balance(): Money
    {
        return $this->balance;
    }

    public function adjustBalanceByAmount(int $amount): void
    {
        $this->balance = $this->balance->add(
            new Money($amount, $this->balance->getCurrency()),
        );

        $this->updatedAt = Carbon::now();
    }

    public function nickname(): ?string
    {
        return $this->nickname;
    }

    /** @return Collection<int, Product> */
    public function products(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): void
    {
        if ($this->products()->contains($product)) {
            return;
        }

        $this->products()->add($product);
        $product->linkUser($this);

        $this->updatedAt = Carbon::now();
    }

    /** @return Collection<int, Order> */
    public function orders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): void
    {
        if ($this->orders()->contains($order)) {
            return;
        }

        $this->orders()->add($order);
        $order->linkUser($this);

        $this->updatedAt = Carbon::now();
    }

    /** @return Collection<int, Transaction> */
    public function transactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): void
    {
        if ($this->transactions()->contains($transaction)) {
            return;
        }

        $this->transactions()->add($transaction);
        $transaction->linkUser($this);

        $this->updatedAt = Carbon::now();
    }
}
