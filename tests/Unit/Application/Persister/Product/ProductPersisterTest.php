<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Persister\Product;

use App\Application\Exceptions\ProductOperationFailed;
use App\Application\Persister\Product\ProductPersister;
use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Exceptions\ProductPersistenceFailed;
use App\Domain\Model\Product\Interfaces\ProductRepository;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Tests\Mocks\NullTransactionalExecutor;
use Doctrine\ORM\ORMException;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductPersisterTest extends TestCase
{
    public function testPersistFailed() : void
    {
        $persister = new ProductPersister(
            Mockery::mock(
                ProductRepository::class,
                static function (MockInterface $mock) : void {
                    $mock->shouldReceive('save')
                        ->once()
                        ->withArgs(static function (Product $product) : bool {
                            return (string) $product->id() === '4c898b2c-d38e-4b7b-89cf-ee301ddb6942';
                        })
                        ->andThrow(
                            ProductPersistenceFailed::saveFailed(
                                ORMException::entityManagerClosed(),
                            ),
                        );
                }
            ),
            new NullTransactionalExecutor(),
        );

        $this->expectException(ProductOperationFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Product operation failed: Product save failed: The EntityManager is closed.',
        );

        $persister->persist(
            Product::create(
                new ProductId(Uuid::fromString('4c898b2c-d38e-4b7b-89cf-ee301ddb6942')),
                new CreateProduct(
                    User::create(
                        new UserId(Uuid::uuid4()),
                        new CreateUser(
                            'def',
                            new Money(10000, new Currency('USD')),
                        ),
                    ),
                    new ProductType(ProductType::MUG),
                    'abc',
                    'abc',
                    new Money(1234, new Currency('USD')),
                ),
            ),
        );
    }
}
