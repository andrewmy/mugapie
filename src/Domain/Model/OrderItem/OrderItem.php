<?php

declare(strict_types=1);

namespace App\Domain\Model\OrderItem;

use App\Domain\Model\Order\Order;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\OrderItem\Interfaces\ProductUnits;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductType;
use Carbon\Carbon;
use DateTimeInterface;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class OrderItem implements ProductUnits
{
    private UuidInterface $id;

    private ?int $incrementalId;

    private ?Order $order;

    private DateTimeInterface $createdAt;

    private Product $product;

    private ProductType $productType;

    private string $title;

    private string $sku;

    private Money $pricePerUnit;

    private int $units;

    private function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public static function create(
        OrderItemId $id,
        Order $order,
        CreateOrderItem $data
    ): self {
        $obj     = new self();
        $obj->id = $id->value();
        $order->addItem($obj);
        $obj->product      = $data->product();
        $obj->productType  = $data->product()->type();
        $obj->title        = $data->product()->title();
        $obj->sku          = $data->product()->sku();
        $obj->pricePerUnit = $data->product()->cost();
        $obj->units        = $data->units();

        return $obj;
    }

    public function id(): OrderItemId
    {
        return new OrderItemId($this->id);
    }

    public function incrementalId(): ?int
    {
        return $this->incrementalId;
    }

    public function order(): ?Order
    {
        return $this->order;
    }

    public function linkOrder(?Order $order): void
    {
        $this->order = $order;
    }

    public function createdAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function product(): Product
    {
        return $this->product;
    }

    public function productType(): ProductType
    {
        return $this->productType;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function sku(): string
    {
        return $this->sku;
    }

    public function pricePerUnit(): Money
    {
        return $this->pricePerUnit;
    }

    public function units(): int
    {
        return $this->units;
    }
}
