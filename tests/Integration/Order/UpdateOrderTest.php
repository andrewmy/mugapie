<?php

declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Tests\Integration\IntegrationTestCase;

final class UpdateOrderTest extends IntegrationTestCase
{
    public function testUpdate(): void
    {
        $user    = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $product = $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createOrder(
            $user,
            '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            [new CreateOrderItem($product, 1)],
        );

        $this->request('PUT', 'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942', [
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

        $this->request('PUT', 'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942', [
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
