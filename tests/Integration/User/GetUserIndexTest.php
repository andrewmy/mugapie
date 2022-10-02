<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;

use function assert;
use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class GetUserIndexTest extends IntegrationTestCase
{
    public function testIndex(): void
    {
        $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $client = $this->request(
            'GET',
            'users',
        );

        $response = $client->getResponse();
        assert($response !== null);
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($data));

        self::assertCount(1, $data);
        static::assertArraySubset([
            'id' => '99c01751-6d32-464a-9c18-6625856b9192',
            'nickname' => 'abc',
            'balance' => 10000,
        ], $data[0]);
    }
}
