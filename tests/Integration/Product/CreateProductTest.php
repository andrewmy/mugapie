<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;

final class CreateProductTest extends IntegrationTestCase
{
    public function testCreate() : void
    {
        $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $this->request('POST', 'products', [
            'user' => '/api/users/99c01751-6d32-464a-9c18-6625856b9192',
            'type' => 'mug',
            'title' => 'Abc',
            'sku' => 'abc',
            'cost' => 1234,
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'type' => 'mug',
            'title' => 'Abc',
            'sku' => 'abc',
            'cost' => 1234,
        ]);

        $this->request('POST', 'products', [
            'user' => '/api/users/99c01751-6d32-464a-9c18-6625856b9192',
            'type' => 'mug',
            'title' => 'Abc',
            'sku' => 'abc',
            'cost' => 1234,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains(['detail' => 'sku: This value is already used.']);
    }
}
