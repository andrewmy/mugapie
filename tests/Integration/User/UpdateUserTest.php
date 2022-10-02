<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;

final class UpdateUserTest extends IntegrationTestCase
{
    public function testUpdates(): void
    {
        $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $this->request(
            'PUT',
            'users/99c01751-6d32-464a-9c18-6625856b9192',
            ['nickname' => 'def'],
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => '99c01751-6d32-464a-9c18-6625856b9192',
            'nickname' => 'def',
            'balance' => 10000,
        ]);
    }
}
