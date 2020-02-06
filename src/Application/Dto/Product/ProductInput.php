<?php

declare(strict_types=1);

namespace App\Application\Dto\Product;

use App\Application\Validator\Constraints\UniqueEntityDto;
use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Dto\UpdateProduct;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\User;
use Money\Currency;
use Money\Money;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints;
use function assert;

/**
 * @UniqueEntityDto(
 *     field="sku",
 *     entityClass="App\Domain\Model\Product\Product",
 *     referenceEntityField="referenceEntity",
 *     groups={"post"}
 * )
 */
final class ProductInput
{
    /**
     * Used for the uniqueness validation
     */
    public ?Product $referenceEntity;

    /**
     * @var User
     * @Constraints\NotNull(groups={"post"})
     * @Groups({"product:post"})
     */
    public ?User $user = null;

    /**
     * @var string
     * @Constraints\NotBlank(groups={"post", "put"})
     * @Constraints\Choice(
     *     callback={"App\Domain\Model\Product\ProductType", "validValues"},
     *     groups={"post", "put"}
     * )
     * @Groups({"product:post", "product:put"})
     */
    public string $type;

    /**
     * @var string
     * @Constraints\NotBlank(groups={"post", "put"})
     * @Groups({"product:post", "product:put"})
     */
    public string $title;

    /**
     * @var string
     * @Constraints\NotBlank(groups={"post", "put"})
     * @Groups({"product:post", "product:put"})
     */
    public string $sku = '';

    /**
     * @var int
     * @Constraints\NotBlank(groups={"post", "put"})
     * @Groups({"product:post", "product:put"})
     */
    public int $cost = 0;

    public function toDomainCreate(string $currency) : CreateProduct
    {
        assert($this->user !== null);

        return new CreateProduct(
            $this->user,
            new ProductType($this->type),
            $this->title,
            $this->sku,
            new Money($this->cost, new Currency($currency)),
        );
    }

    public function toDomainUpdate(string $currency) : UpdateProduct
    {
        return new UpdateProduct(
            new ProductType($this->type),
            $this->title,
            $this->sku,
            new Money($this->cost, new Currency($currency)),
        );
    }
}
