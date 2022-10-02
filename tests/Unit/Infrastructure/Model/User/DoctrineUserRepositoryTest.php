<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Model\User;

use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\Exceptions\UserPersistenceFailed;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Model\User\DoctrineUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\EntityManagerClosed;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DoctrineUserRepositoryTest extends TestCase
{
    private static function user(): User
    {
        return User::create(
            new UserId(Uuid::uuid4()),
            new CreateUser(
                'abc',
                new Money(0, new Currency('USD')),
            ),
        );
    }

    public function testSaveFailed(): void
    {
        $repository = new DoctrineUserRepository(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('persist')
                        ->andThrow(EntityManagerClosed::create());
                },
            ),
        );

        $this->expectException(UserPersistenceFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'User save failed: The EntityManager is closed.',
        );

        $repository->save(self::user());
    }

    public function testDeleteFailed(): void
    {
        $repository = new DoctrineUserRepository(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock): void {
                    $mock->shouldReceive('remove')
                        ->andThrow(EntityManagerClosed::create());
                },
            ),
        );

        $this->expectException(UserPersistenceFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'User delete failed: The EntityManager is closed.',
        );

        $repository->delete(self::user());
    }
}
