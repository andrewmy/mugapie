<?php

declare(strict_types=1);

namespace App\Domain\Model\Order;

use App\Domain\Model\Common\Interfaces\RecordsEvents;
use App\Domain\Model\Common\Traits\EventRecorder;
use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\Dto\UpdateOrder;
use App\Domain\Model\Order\Events\OrderSentToProduction;
use App\Domain\Model\OrderItem\OrderItem;
use App\Domain\Model\User\User;
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
class Order implements RecordsEvents
{
    use EventRecorder;

    /** @var UuidInterface */
    private $id;

    /** @var int|null */
    private $incrementalId = null;

    /** @var User */
    private $user;

    /** @var DateTimeInterface */
    private $createdAt;

    /** @var DateTimeInterface */
    private $updatedAt;

    /** @var ShippingType */
    private $shippingType;

    /** @var ShippingAddress */
    private $shippingAddress;

    /** @var OrderStatus */
    private $status;

    /** @var Money */
    private $orderCost;

    /** @var Collection<int, OrderItem> */
    private $items;

    private function __construct()
    {
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
        $this->status    = new OrderStatus(OrderStatus::PENDING);
        $this->items     = new ArrayCollection();
    }

    public static function create(
        OrderId $id,
        CreateOrder $data,
        Money $orderCost
    ) : self {
        $obj     = new self();
        $obj->id = $id->value();
        $data->user()->addOrder($obj);
        $obj->shippingType    = $data->shippingType();
        $obj->shippingAddress = $data->shippingAddress();
        $obj->orderCost       = $orderCost;

        return $obj;
    }

    public function update(UpdateOrder $data, Money $orderCost) : void
    {
        $this->shippingType    = $data->shippingType();
        $this->shippingAddress = $data->shippingAddress();
        $this->updateOrderCost($orderCost);
        foreach ($this->items as $item) {
            $this->removeItem($item);
        }

        $this->updatedAt = Carbon::now();
    }

    public function id() : OrderId
    {
        return new OrderId($this->id);
    }

    public function incrementalId() : ?int
    {
        return $this->incrementalId;
    }

    public function user() : User
    {
        return $this->user;
    }

    public function linkUser(User $user) : void
    {
        $this->user = $user;

        $this->updatedAt = Carbon::now();
    }

    public function createdAt() : DateTimeInterface
    {
        return $this->createdAt;
    }

    public function updatedAt() : DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function shippingType() : ShippingType
    {
        return $this->shippingType;
    }

    public function shippingAddress() : ShippingAddress
    {
        return $this->shippingAddress;
    }

    public function status() : OrderStatus
    {
        return $this->status;
    }

    public function isEditable() : bool
    {
        return $this->status->value() !== OrderStatus::PRODUCTION;
    }

    public function sendToProduction() : void
    {
        if ($this->status->value() === OrderStatus::PRODUCTION) {
            return;
        }

        $this->status = new OrderStatus(OrderStatus::PRODUCTION);

        $this->recordThat(new OrderSentToProduction($this));
    }

    public function orderCost() : Money
    {
        return $this->orderCost;
    }

    public function updateOrderCost(Money $orderCost) : void
    {
        $this->orderCost = $orderCost;

        $this->updatedAt = Carbon::now();
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function items() : Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item) : void
    {
        if ($this->items()->contains($item)) {
            return;
        }

        $this->items()->add($item);
        $item->linkOrder($this);

        $this->updatedAt = Carbon::now();
    }

    public function removeItem(OrderItem $item) : void
    {
        $this->items()->removeElement($item);
        $item->linkOrder(null);

        $this->updatedAt = Carbon::now();
    }
}
