<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;

use function assert;
use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class GetProductIndexTest extends IntegrationTestCase
{
    public function testIndex(): void
    {
        $user = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $this->createUser('5f86ec67-9de9-4b22-a4f5-ded8d25b9146');
        $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');

        $client = $this->request(
            'GET',
            'users/99c01751-6d32-464a-9c18-6625856b9192/products',
        );

        $response = $client->getResponse();
        assert($response !== null);
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($data));

        self::assertCount(1, $data);
        self::assertArraySubset([
            'id' => 'f032d950-9c3e-4336-b133-74afd5bb31e5',
            'type' => 'mug',
            'title' => 'prod abc',
            'sku' => 'prod-f032d950-9c3e-4336-b133-74afd5bb31e5',
            'cost' => 1234,
        ], $data[0]);

        $client = $this->request(
            'GET',
            'users/5f86ec67-9de9-4b22-a4f5-ded8d25b9146/products',
        );

        $response = $client->getResponse();
        assert($response !== null);
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($data));

        self::assertCount(0, $data);
    }
}
