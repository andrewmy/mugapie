<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Dto\Order;

use App\Application\Dto\Order\OrderInput;
use App\Application\Dto\OrderItem\OrderItemInput;
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

final class OrderInputTest extends TestCase
{
    public function testDomestic(): void
    {
        $input               = new OrderInput();
        $input->shippingType = 'standard';
        $input->countryCode  = 'LV';
        $input->region       = '-';
        $input->city         = 'Riga';
        $input->address      = 'Z1';
        $input->zip          = 'LV-1001';
        $input->phone        = '123';
        $input->fullName     = 'A B';

        $itemInput          = new OrderItemInput();
        $itemInput->product = Product::create(
            new ProductId(Uuid::uuid4()),
            new CreateProduct(
                User::create(
                    new UserId(Uuid::uuid4()),
                    new CreateUser(
                        'ghi',
                        new Money(0, new Currency('USD')),
                    ),
                ),
                new ProductType(ProductType::MUG),
                'abc',
                'def',
                new Money(1234, new Currency('USD')),
            ),
        );
        $itemInput->units   = 1;
        $input->items       = [$itemInput];

        self::assertSame('LV', $input->countryCode());

        $domainModel = $input->toDomainUpdate();
        self::assertFalse($domainModel->shippingAddress()->isDomestic());

        $input->countryCode = 'US';
        $input->street      = 'Z1';
        $domainModel        = $input->toDomainUpdate();

        self::assertTrue($domainModel->shippingAddress()->isDomestic());
    }
}
