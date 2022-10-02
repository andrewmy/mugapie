<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Transformer\Order;

use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use ApiPlatform\Validator\ValidatorInterface;
use App\Application\Dto\Order\OrderInput;
use App\Application\Transformer\Order\OrderInputTransformer;
use App\Domain\Calculator\Interfaces\OrderCostCalculator;
use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Order\OrderId;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Model\OrderItem\SimpleOrderItemFactory;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OrderInputTransformerTest extends TestCase
{
    public function testTransformPostWorld(): void
    {
        $transformer = new OrderInputTransformer(
            Mockery::mock(
                ValidatorInterface::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('validate')
                        ->once()
                        ->withArgs(static function (OrderInput $input, array $context): bool {
                            return ($context['groups'] ?? null) === 'post_world'
                                && $input->countryCode === 'LV';
                        });
                },
            ),
            Mockery::mock(
                OrderCostCalculator::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('calculate')
                        ->once()
                        ->andReturn(new Money(100, new Currency('USD')));
                },
            ),
            new SimpleOrderItemFactory(),
        );

        $input               = new OrderInput();
        $input->user         = User::create(
            new UserId(Uuid::uuid4()),
            new CreateUser(
                'abc',
                new Money(1000, new Currency('USD')),
            ),
        );
        $input->shippingType = 'standard';
        $input->countryCode  = 'LV';
        $input->region       = '-';
        $input->city         = 'Riga';
        $input->address      = 'Z1';
        $input->street       = 'Z1';
        $input->zip          = 'LV-1001';
        $input->phone        = '123';
        $input->fullName     = 'A B';
        $input->items        = [];

        $this->expectNotToPerformAssertions();

        $transformer->transform($input, Order::class);
    }

    public function testTransformPutWorld(): void
    {
        $transformer = new OrderInputTransformer(
            Mockery::mock(
                ValidatorInterface::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('validate')
                        ->once()
                        ->withArgs(static function (OrderInput $input, array $context): bool {
                            return ($context['groups'] ?? null) === 'put_world'
                                && $input->countryCode === 'LV';
                        });
                },
            ),
            Mockery::mock(
                OrderCostCalculator::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('calculate')
                        ->once()
                        ->andReturn(new Money(100, new Currency('USD')));
                },
            ),
            new SimpleOrderItemFactory(),
        );

        $input               = new OrderInput();
        $input->user         = User::create(
            new UserId(Uuid::uuid4()),
            new CreateUser(
                'abc',
                new Money(1000, new Currency('USD')),
            ),
        );
        $input->shippingType = 'standard';
        $input->countryCode  = 'LV';
        $input->region       = '-';
        $input->city         = 'Riga';
        $input->address      = 'Z1';
        $input->street       = 'Z1';
        $input->zip          = 'LV-1001';
        $input->phone        = '123';
        $input->fullName     = 'A B';
        $input->items        = [];

        $this->expectNotToPerformAssertions();

        $transformer->transform($input, Order::class, [
            AbstractItemNormalizer::OBJECT_TO_POPULATE => Order::create(
                new OrderId(Uuid::uuid4()),
                new CreateOrder(
                    $input->user,
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
                ),
                new Money(4321, new Currency('USD')),
            ),
        ]);
    }
}
