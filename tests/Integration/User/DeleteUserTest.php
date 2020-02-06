<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class DeleteUserTest extends IntegrationTestCase
{
    public function testDelete() : void
    {
        $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $this->request(
            'DELETE',
            'users/99c01751-6d32-464a-9c18-6625856b9192',
        );

        $this->assertResponseIsSuccessful();

        $client = $this->request(
            'GET',
            'users/99c01751-6d32-464a-9c18-6625856b9192',
        );

        self::assertSame(404, $client->getResponse()->getStatusCode());

        $client = $this->request('GET', 'users');
        $data   = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertCount(0, $data);
    }
}
