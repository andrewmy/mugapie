<?php

declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Tests\Integration\IntegrationTestCase;

final class SendToProductionOrderTest extends IntegrationTestCase
{
    public function testSendSuccess() : void
    {
        $user    = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $product = $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createOrder(
            $user,
            '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            [new CreateOrderItem($product, 1)],
        );

        $this->request(
            'POST',
            'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942/send_to_production',
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'shippingType' => 'standard',
            'countryCode' => 'LV',
            'region' => '-',
            'city' => 'Riga',
            'address' => 'Z1',
            'zip' => 'LV-1001',
            'phone' => '1234',
            'fullName' => 'A B',
            'orderCost' => 4321,
            'status' => 'production',
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
                    'units' => 3,
                ],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(['detail' => 'Order is not editable']);

        $this->request(
            'POST',
            'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942/send_to_production',
        );

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(['detail' => 'Order is not editable']);
    }

    public function testSendTooExpensiveFails() : void
    {
        $user    = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $product = $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createOrder(
            $user,
            '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            [new CreateOrderItem($product, 1)],
        );

        $this->request(
            'PUT',
            'products/f032d950-9c3e-4336-b133-74afd5bb31e5',
            [
                'type' => 'mug',
                'title' => 'Abc!',
                'sku' => 'abcd',
                'cost' => 12345,
            ],
        );

        $this->request(
            'POST',
            'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942/send_to_production',
        );

        self::assertResponseStatusCodeSame(400);
        self::assertJsonContains(['detail' => 'Cannot proceed with order, its cost of USD 128.45 exceeds the available balance of USD 100.00']);
    }
}
