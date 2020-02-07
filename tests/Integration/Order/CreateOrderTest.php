<?php

declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Tests\Integration\IntegrationTestCase;

final class CreateOrderTest extends IntegrationTestCase
{
    public function testCreateSuccess() : void
    {
        $user1 = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $user2 = $this->createUser('4c583fde-4a71-47b2-923e-f53d36f5a6bb');

        $this->createProduct($user1, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createProduct($user2, '060fbebc-4e39-4eea-a161-784baff24f7e');

        $this->request('POST', 'orders', [
            'user' => '/api/users/99c01751-6d32-464a-9c18-6625856b9192',
            'shippingType' => 'standard',
            'countryCode' => 'LV',
            'region' => '-',
            'city' => 'Riga',
            'address' => 'Z1',
            'zip' => 'LV-1001',
            'phone' => '123',
            'fullName' => 'A B',
            'items' => [
                [
                    'product' => '/api/products/f032d950-9c3e-4336-b133-74afd5bb31e5',
                    'units' => 2,
                ],
                // allow dupes, we just sum the units
                [
                    'product' => '/api/products/f032d950-9c3e-4336-b133-74afd5bb31e5',
                    'units' => 1,
                ],
            ],
        ]);

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'shippingType' => 'standard',
            'countryCode' => 'LV',
            'region' => '-',
            'city' => 'Riga',
            'address' => 'Z1',
            'zip' => 'LV-1001',
            'phone' => '123',
            'fullName' => 'A B',
            'orderCost' => 1234 * 3 + 500 + 250 * 2,
            'status' => 'pending',
        ]);
    }

    public function testCreateWithForeignProductsFails() : void
    {
        $user1 = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $user2 = $this->createUser('4c583fde-4a71-47b2-923e-f53d36f5a6bb');

        $this->createProduct($user1, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createProduct($user2, '060fbebc-4e39-4eea-a161-784baff24f7e');

        $this->request('POST', 'orders', [
            'user' => '/api/users/99c01751-6d32-464a-9c18-6625856b9192',
            'shippingType' => 'standard',
            'countryCode' => 'LV',
            'region' => '-',
            'city' => 'Riga',
            'address' => 'Z1',
            'zip' => 'LV-1001',
            'phone' => '123',
            'fullName' => 'A B',
            'items' => [
                [
                    'product' => '/api/products/060fbebc-4e39-4eea-a161-784baff24f7e',
                    'units' => 3,
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(['detail' => 'Cannot proceed with order, it contains products from another user: 060fbebc-4e39-4eea-a161-784baff24f7e']);
    }

    public function testCreateTooMuchFails() : void
    {
        $user1 = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');

        $this->createProduct($user1, 'f032d950-9c3e-4336-b133-74afd5bb31e5');

        $this->request('POST', 'orders', [
            'user' => '/api/users/99c01751-6d32-464a-9c18-6625856b9192',
            'shippingType' => 'standard',
            'countryCode' => 'LV',
            'region' => '-',
            'city' => 'Riga',
            'address' => 'Z1',
            'zip' => 'LV-1001',
            'phone' => '123',
            'fullName' => 'A B',
            'items' => [
                [
                    'product' => '/api/products/f032d950-9c3e-4336-b133-74afd5bb31e5',
                    'units' => 10,
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(['detail' => 'Cannot proceed with order, its cost of USD 150.90 exceeds the available balance of USD 100.00']);
    }
}
