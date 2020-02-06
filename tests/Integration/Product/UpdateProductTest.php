<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Tests\Integration\IntegrationTestCase;

final class UpdateProductTest extends IntegrationTestCase
{
    public function testUpdateChangesPendingOrderItem() : void
    {
        $user    = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $product = $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');

        // two orders with the same product

        $this->createOrder(
            $user,
            '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            [new CreateOrderItem($product, 1)],
        );
        $this->createOrder(
            $user,
            'b7d7e251-7837-4602-8f8e-5706271cb44c',
            [new CreateOrderItem($product, 1)],
        );

        // finalize the second one

        $this->request(
            'POST',
            'orders/b7d7e251-7837-4602-8f8e-5706271cb44c/send_to_production',
        );

        // update product

        $this->request(
            'PUT',
            'products/f032d950-9c3e-4336-b133-74afd5bb31e5',
            [
                'type' => 'mug',
                'title' => 'Abc!',
                'sku' => 'abcd',
                'cost' => 1234,
            ],
        );

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => 'f032d950-9c3e-4336-b133-74afd5bb31e5',
            'type' => 'mug',
            'title' => 'Abc!',
            'sku' => 'abcd',
            'cost' => 1234,
        ]);

        // first order should be updated

        $this->request('GET', 'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            'orderCost' => 1234 + 500,
            'status' => 'pending',
        ]);

        // second order should stay the same

        $this->request('GET', 'orders/b7d7e251-7837-4602-8f8e-5706271cb44c');

        self::assertResponseIsSuccessful();
        self::assertJsonContains([
            'id' => 'b7d7e251-7837-4602-8f8e-5706271cb44c',
            'orderCost' => 4321,
            'status' => 'production',
        ]);
    }
}
