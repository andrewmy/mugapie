<?php

declare(strict_types=1);

namespace App\Tests\Integration\Product;

use App\Tests\Integration\IntegrationTestCase;

final class UpdateProductTest extends IntegrationTestCase
{
    public function testUpdate() : void
    {
        $user = $this->createUser('99c01751-6d32-464a-9c18-6625856b9192');
        $this->createProduct($user, 'f032d950-9c3e-4336-b133-74afd5bb31e5');

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

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'id' => 'f032d950-9c3e-4336-b133-74afd5bb31e5',
            'type' => 'mug',
            'title' => 'Abc!',
            'sku' => 'abcd',
            'cost' => 1234,
        ]);
    }
}
