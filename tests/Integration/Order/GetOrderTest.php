<?php

declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Tests\Integration\IntegrationTestCase;

final class GetOrderTest extends IntegrationTestCase
{
    public function testGet() : void
    {
        $user    = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $product = $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createOrder(
            $user,
            '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            [new CreateOrderItem($product, 1)],
        );

        $this->request(
            'GET',
            'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            'orderCost' => 4321,
            'status' => 'pending',
        ]);
    }
}
