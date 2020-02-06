<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Model\Transaction;

use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\Transaction\Transaction;
use App\Domain\Model\Transaction\TransactionId;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Infrastructure\Model\Transaction\DoctrineTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Mockery;
use Mockery\MockInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DoctrineTransactionRepositoryTest extends TestCase
{
    public function testSaveFailed() : void
    {
        $repository = new DoctrineTransactionRepository(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock) : void {
                    $mock->shouldReceive('persist')
                        ->andThrow(ORMException::entityManagerClosed());
                }
            ),
        );

        $this->expectException(TransactionPersistenceFailed::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(
            'Transaction save failed: The EntityManager is closed.'
        );

        $repository->save(
            Transaction::createForUser(
                new TransactionId(Uuid::uuid4()),
                User::create(
                    new UserId(Uuid::uuid4()),
                    new CreateUser(
                        'abc',
                        new Money(0, new Currency('USD')),
                    ),
                ),
                new Money(100, new Currency('USD')),
            ),
        );
    }
}
