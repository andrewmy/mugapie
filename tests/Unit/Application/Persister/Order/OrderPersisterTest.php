<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Persister\Order;

use App\Application\Exceptions\OrderOperationFailed;
use App\Application\Persister\Order\OrderPersister;
use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Order\OrderId;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Tests\Mocks\NullTransactionalExecutor;
use Doctrine\ORM\Exception\EntityManagerClosed;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OrderPersisterTest extends TestCase
{
    public function testPersistFailed(): void
    {
        $persister = new OrderPersister(
            Mockery::mock(
                OrderRepository::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('save')
                        ->once()
                        ->withArgs(static function (Order $order): bool {
                            return (string) $order->id() === '4c898b2c-d38e-4b7b-89cf-ee301ddb6942';
                        })
                        ->andThrow(
                            OrderPersistenceFailed::saveFailed(
                                EntityManagerClosed::create(),
                            ),
                        );
                },
            ),
            new NullTransactionalExecutor(),
        );

        $this->expectException(OrderOperationFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Order operation failed: Order save failed: The EntityManager is closed.',
        );

        $persister->persist(
            Order::create(
                new OrderId(Uuid::fromString('4c898b2c-d38e-4b7b-89cf-ee301ddb6942')),
                new CreateOrder(
                    User::create(
                        new UserId(Uuid::uuid4()),
                        new CreateUser(
                            'abc',
                            new Money(0, new Currency('USD')),
                        ),
                    ),
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
        );
    }
}
