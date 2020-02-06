<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

use App\Domain\Model\Common\Interfaces\RecordsEvents;
use App\Domain\Model\Common\Traits\EventRecorder;
use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Dto\UpdateProduct;
use App\Domain\Model\Product\Events\ProductUpdated;
use App\Domain\Model\User\User;
use Carbon\Carbon;
use DateTimeInterface;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

/**
 * Can't use 7.4 typed properties here because Sf Serializer tries to build a
 * cache key from its Doctrine proxy regardless of serialization groups or anything.
 */
class Product implements RecordsEvents
{
    use EventRecorder;

    /** @var UuidInterface */
    private $id;

    /** @var int|null */
    private $incrementalId;

    /** @var User */
    private $user;

    /** @var DateTimeInterface */
    private $createdAt;

    /** @var DateTimeInterface */
    private $updatedAt;

    /** @var ProductType */
    private $type;

    /** @var string */
    private $title;

    /** @var string */
    private $sku;

    /** @var Money */
    private $cost;

    private function __construct()
    {
        $this->createdAt = Carbon::now();
        $this->updatedAt = Carbon::now();
    }

    public static function create(
        ProductId $id,
        CreateProduct $data
    ) : self {
        $obj        = new self();
        $obj->id    = $id->value();
        $obj->type  = $data->type();
        $obj->title = $data->title();
        $obj->sku   = $data->sku();
        $obj->cost  = $data->cost();

        $data->user()->addProduct($obj);

        return $obj;
    }

    public function update(UpdateProduct $data) : void
    {
        $this->type  = $data->type();
        $this->title = $data->title();
        $this->sku   = $data->sku();
        $this->cost  = $data->cost();

        $this->updatedAt = Carbon::now();

        $this->recordThat(new ProductUpdated($this));
    }

    public function id() : ProductId
    {
        return new ProductId($this->id);
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

    public function type() : ProductType
    {
        return $this->type;
    }

    public function title() : string
    {
        return $this->title;
    }

    public function sku() : string
    {
        return $this->sku;
    }

    public function cost() : Money
    {
        return $this->cost;
    }
}
