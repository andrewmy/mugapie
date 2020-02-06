<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;
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

        $this->assertResponseIsSuccessful();

        $client = $this->request(
            'GET',
            'products/f032d950-9c3e-4336-b133-74afd5bb31e5',
        );

        self::assertSame(404, $client->getResponse()->getStatusCode());

        $client = $this->request(
            'GET',
            'users/99c01751-6d32-464a-9c18-6625856b9192/products',
        );
        $data   = json_decode(
            $client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertCount(0, $data);
    }
}
