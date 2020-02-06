<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;

final class CreateUserTest extends IntegrationTestCase
{
    public function testCreatesWithBalance() : void
    {
        $this->request('POST', 'users', ['nickname' => 'abc']);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['nickname' => 'abc', 'balance' => 10000]);
    }
}
