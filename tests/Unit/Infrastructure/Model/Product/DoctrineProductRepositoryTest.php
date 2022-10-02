<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Model\Product;

use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Exceptions\ProductPersistenceFailed;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Model\Product\DoctrineProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\EntityManagerClosed;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class DoctrineProductRepositoryTest extends TestCase
{
    private static function product() : Product
    {
        return Product::create(
            new ProductId(Uuid::uuid4()),
            new CreateProduct(
                User::create(
                    new UserId(Uuid::uuid4()),
                    new CreateUser(
                        'abc',
                        new Money(0, new Currency('USD')),
                    ),
                ),
                new ProductType(ProductType::MUG),
                'prod abc',
                'abc',
                new Money(1234, new Currency('USD')),
            ),
        );
    }

    public function testSaveFailed() : void
    {
        $repository = new DoctrineProductRepository(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock) : void {
                    $mock->shouldReceive('persist')
                        ->andThrow(EntityManagerClosed::create());
                }
            ),
        );

        $this->expectException(ProductPersistenceFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Product save failed: The EntityManager is closed.'
        );

        $repository->save(self::product());
    }

    public function testDeleteFailed() : void
    {
        $repository = new DoctrineProductRepository(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock) : void {
                    $mock->shouldReceive('remove')
                        ->andThrow(EntityManagerClosed::create());
                }
            ),
        );

        $this->expectException(ProductPersistenceFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Product delete failed: The EntityManager is closed.'
        );

        $repository->delete(self::product());
    }
}
