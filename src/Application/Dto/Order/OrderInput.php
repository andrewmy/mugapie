<?php

declare(strict_types=1);

namespace App\Application\Dto\Order;

use App\Application\Dto\OrderItem\OrderItemInput;
use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\Dto\UpdateOrder;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\User\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ZipCodeValidator\Constraints\ZipCode;
use function assert;

final class OrderInput
{
    /**
     * @var User
     * @Assert\NotNull(groups={"post"})
     * @Groups({"order:post"})
     */
    public ?User $user = null;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home", "post_world", "put_world"})
     * @Assert\Choice(
     *     callback={"App\Domain\Model\Order\ShippingType", "validValues"},
     *     groups={"post_home", "put_home"}
     * )
     * @Assert\EqualTo(
     *     value=App\Domain\Model\Order\ShippingType::STANDARD,
     *     groups={"post_world", "put_world"}
     * )
     * @Groups({"order:post", "order:put"})
     */
    public string $shippingType;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home", "post_world", "put_world"})
     * @Assert\Country(groups={"post_home", "put_home", "post_world", "put_world"})
     * @Groups({"order:post", "order:put"})
     */
    public string $countryCode;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home"})
     * @Assert\Choice(
     *     choices=App\Domain\Model\Order\ShippingAddress::DOMESTIC_REGIONS,
     *     groups={"post_home", "put_home"}
     * )
     * @Groups({"order:post", "order:put"})
     */
    public string $region;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home", "post_world", "put_world"})
     * @Groups({"order:post", "order:put"})
     */
    public string $city;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home"})
     * @Groups({"order:post", "order:put"})
     */
    public string $street;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_world", "put_world"})
     * @Groups({"order:post", "order:put"})
     */
    public string $address;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home"})
     * @ZipCode(
     *     getter="countryCode",
     *     strict=false,
     *     ignoreEmpty=true,
     *     groups={"post_home", "put_home", "post_world", "put_world"}
     * )
     * @Groups({"order:post", "order:put"})
     */
    public string $zip;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home", "post_world", "put_world"})
     * @Groups({"order:post", "order:put"})
     */
    public string $phone;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_home", "put_home", "post_world", "put_world"})
     * @Groups({"order:post", "order:put"})
     */
    public string $fullName;

    /**
     * @var OrderItemInput[]
     * @Assert\Count(
     *     min=1,
     *     groups={"post_home", "put_home", "post_world", "put_world"}
     * )
     * @Assert\Valid(
     *     groups={"post_home", "put_home", "post_world", "put_world"}
     * )
     * @Groups({"order:post", "order:put"})
     */
    public array $items;

    public function toDomainCreate() : CreateOrder
    {
        assert($this->user !== null);

        $data = new CreateOrder(
            $this->user,
            new ShippingType($this->shippingType),
            $this->shippingAddress(),
        );

        foreach ($this->items as $item) {
            $data->addItem($item->toDomainCreate());
        }

        return $data;
    }

    public function toDomainUpdate() : UpdateOrder
    {
        $data = new UpdateOrder(
            new ShippingType($this->shippingType),
            $this->shippingAddress(),
        );

        foreach ($this->items as $item) {
            $data->addItem($item->toDomainCreate());
        }

        return $data;
    }

    private function shippingAddress() : ShippingAddress
    {
        return ShippingAddress::isDomesticCountry($this->countryCode)
            ? ShippingAddress::createDomestic(
                $this->region,
                $this->city,
                $this->street,
                $this->zip,
                $this->phone,
                $this->fullName,
            )
            : ShippingAddress::createInternational(
                $this->countryCode,
                $this->region,
                $this->city,
                $this->address,
                $this->zip,
                $this->phone,
                $this->fullName,
            );
    }

    public function countryCode() : string
    {
        return $this->countryCode;
    }
}
