<?php

declare(strict_types=1);

namespace App\Tests\Integration\User;

use App\Tests\Integration\IntegrationTestCase;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class GetUserIndexTest extends IntegrationTestCase
{
    public function testIndex() : void
    {
        $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $client = $this->request(
            'GET',
            'users',
        );

        $data = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertCount(1, $data);
        self::assertArraySubset([
            'id' => '99c01751-6d32-464a-9c18-6625856b9192',
            'nickname' => 'abc',
            'balance' => 10000,
        ], $data[0]);
    }
}
