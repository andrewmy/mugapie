<?php

declare(strict_types=1);

namespace App\Tests\Integration\Order;

use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Tests\Integration\IntegrationTestCase;
use function assert;
use function json_decode;
use const JSON_THROW_ON_ERROR;

final class GetOrderItemIndexTest extends IntegrationTestCase
{
    public function testIndex() : void
    {
        $user    = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $product = $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');
        $this->createOrder(
            $user,
            '4c898b2c-d38e-4b7b-89cf-ee301ddb6942',
            [new CreateOrderItem($product, 1)],
        );

        $client = $this->request(
            'GET',
            'orders/4c898b2c-d38e-4b7b-89cf-ee301ddb6942/items',
        );

        $response = $client->getResponse();
        assert($response !== null);
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(1, $data);
        self::assertArraySubset([
            'sku' => 'prod-f032d950-9c3e-4336-b133-74afd5bb31e5',
            'units' => 1,
        ], $data[0]);
    }
}
