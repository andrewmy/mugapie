<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;
use function assert;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class DeleteProductTest extends IntegrationTestCase
{
    public function testDelete() : void
    {
        $user = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');

        $this->request(
            'DELETE',
            'products/f032d950-9c3e-4336-b133-74afd5bb31e5',
        );

        self::assertResponseIsSuccessful();

        $client = $this->request(
            'GET',
            'products/f032d950-9c3e-4336-b133-74afd5bb31e5',
        );

        $response = $client->getResponse();
        assert($response !== null);
        self::assertSame(404, $response->getStatusCode());

        $client   = $this->request(
            'GET',
            'users/99c01751-6d32-464a-9c18-6625856b9192/products',
        );
        $response = $client->getResponse();
        assert($response !== null);
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(0, $data);
    }
}
