<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;

final class GetUserTest extends IntegrationTestCase
{
    public function testGet(): void
    {
        $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $this->request(
            'GET',
            'users/99c01751-6d32-464a-9c18-6625856b9192',
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => '99c01751-6d32-464a-9c18-6625856b9192',
            'nickname' => 'abc',
            'balance' => 10000,
        ]);
    }
}
