<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Order\Dto;

use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use function assert;

final class CreateOrderTest extends TestCase
{
    public function testAddItemNoDuplicates() : void
    {
        $user = User::create(
            new UserId(Uuid::uuid4()),
            new CreateUser(
                'ghi',
                new Money(0, new Currency('USD')),
            ),
        );

        $dto = new CreateOrder(
            $user,
            new ShippingType(ShippingType::STANDARD),
            ShippingAddress::createInternational(
                'LV',
                '-',
                'Riga',
                'Z1',
                'LV-1001',
                '1234',
                'A B',
            ),
        );

        $product = Product::create(
            new ProductId(Uuid::uuid4()),
            new CreateProduct(
                $user,
                new ProductType(ProductType::MUG),
                'prod abc',
                'abc',
                new Money(1234, new Currency('USD')),
            ),
        );

        $itemDto1 = new CreateOrderItem($product, 1);
        $dto->addItem($itemDto1);
        $dto->addItem($itemDto1);

        self::assertSame(1, $dto->items()->count());

        $itemDto2 = new CreateOrderItem($product, 2);
        $dto->addItem($itemDto2);

        self::assertSame(1, $dto->items()->count());
        $item = $dto->items()->first();
        assert($item instanceof CreateOrderItem);
        self::assertSame(3, $item->units());
    }
}
