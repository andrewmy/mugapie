<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Persister\User;

use App\Application\Exceptions\UserOperationFailed;
use App\Application\Persister\User\UserPersister;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\Exceptions\UserPersistenceFailed;
use App\Domain\Model\User\Interfaces\UserRepository;
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

class UserPersisterTest extends TestCase
{
    public function testPersistFailed(): void
    {
        $persister = new UserPersister(
            Mockery::mock(
                UserRepository::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('save')
                        ->once()
                        ->withArgs(static function (User $user): bool {
                            return (string) $user->id() === '4c898b2c-d38e-4b7b-89cf-ee301ddb6942';
                        })
                        ->andThrow(
                            UserPersistenceFailed::saveFailed(
                                EntityManagerClosed::create(),
                            ),
                        );
                },
            ),
            new NullTransactionalExecutor(),
        );

        $this->expectException(UserOperationFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'User operation failed: User save failed: The EntityManager is closed.',
        );

        $persister->persist(
            User::create(
                new UserId(Uuid::fromString('4c898b2c-d38e-4b7b-89cf-ee301ddb6942')),
                new CreateUser(
                    'def',
                    new Money(10000, new Currency('USD')),
                ),
            ),
        );
    }
}
