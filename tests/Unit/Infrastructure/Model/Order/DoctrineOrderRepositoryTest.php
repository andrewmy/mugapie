<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Model\Order;

use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Order\OrderId;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\OrderItem\OrderItem;
use App\Domain\Model\OrderItem\OrderItemId;
use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Model\Order\DoctrineOrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DoctrineOrderRepositoryTest extends TestCase
{
    public function testSaveFailed() : void
    {
        $repository = new DoctrineOrderRepository(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock) : void {
                    $mock->shouldReceive('persist')
                        ->twice();

                    $mock->shouldReceive('flush')
                        ->andThrow(ORMException::entityManagerClosed());
                }
            ),
        );

        $this->expectException(OrderPersistenceFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Order save failed: The EntityManager is closed.'
        );

        $user  = User::create(
            new UserId(Uuid::uuid4()),
            new CreateUser(
                'abc',
                new Money(0, new Currency('USD')),
            ),
        );
        $order = Order::create(
            new OrderId(Uuid::uuid4()),
            new CreateOrder(
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
            ),
            new Money(4321, new Currency('USD')),
        );
        OrderItem::create(
            new OrderItemId(Uuid::uuid4()),
            $order,
            new CreateOrderItem(
                Product::create(
                    new ProductId(Uuid::uuid4()),
                    new CreateProduct(
                        $user,
                        new ProductType(ProductType::MUG),
                        'prod abc',
                        'abc',
                        new Money(1234, new Currency('USD')),
                    ),
                ),
                1,
            ),
        );

        $repository->save($order);
    }
}
