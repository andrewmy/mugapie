<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Doctrine;

use App\Infrastructure\Doctrine\DoctrineTransactionalExecutor;
use Doctrine\ORM\EntityManagerInterface;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;

final class DoctrineTransactionalExecutorTest extends TestCase
{
    public function testExecute() : void
    {
        $executor = new DoctrineTransactionalExecutor(
            Mockery::mock(
                EntityManagerInterface::class,
                static function (MockInterface $mock) : void {
                    $mock->shouldReceive('wrapInTransaction')
                        ->once()
                        ->andReturnUsing(static function (callable $operation) : void {
                            $operation();
                        });
                },
            ),
        );

        $var = 1;
        $executor->execute(static function () use (&$var) : void {
            $var = 2;
        });

        self::assertSame(2, $var);
    }
}
