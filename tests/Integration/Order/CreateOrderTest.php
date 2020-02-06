<?php

declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Tests\Integration\IntegrationTestCase;

class CreateOrderTest extends IntegrationTestCase
{
    public function testCreate() : void
    {
        $user = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');

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
                    'units' => 3,
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
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
}
