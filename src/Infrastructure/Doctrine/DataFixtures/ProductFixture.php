<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\DataFixtures;

use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use function assert;

/**
 * @codeCoverageIgnore
 */
final class ProductFixture extends Fixture implements DependentFixtureInterface
{
    private Currency $currency;

    public function __construct(string $currency)
    {
        $this->currency = new Currency($currency);
    }

    public function getDependencies() : array
    {
        return [UserFixture::class];
    }

    public function load(ObjectManager $manager) : void
    {
        $list = [
            [
                'user' => 'User_0',
                'type' => ProductType::MUG,
                'title' => 'Black and white mug',
                'sku' => 'abc',
                'cost' => 450,
            ],
            [
                'user' => 'User_0',
                'type' => ProductType::TSHIRT,
                'title' => 'ACID-compliant shirt',
                'sku' => 'def',
                'cost' => 650,
            ],

            [
                'user' => 'User_1',
                'type' => ProductType::MUG,
                'title' => 'White and black mug',
                'sku' => 'ghi',
                'cost' => 350,
            ],
            [
                'user' => 'User_1',
                'type' => ProductType::TSHIRT,
                'title' => 'Three Wolf Moon',
                'sku' => 'jkl',
                'cost' => 850,
            ],
        ];

        foreach ($list as $n => $data) {
            $user = $this->getReference($data['user']);
            assert($user instanceof User);

            $product = Product::create(
                new ProductId(Uuid::uuid4()),
                new CreateProduct(
                    $user,
                    new ProductType($data['type']),
                    $data['title'],
                    $data['sku'],
                    new Money($data['cost'], $this->currency),
                ),
            );

            $manager->persist($product);
            $this->setReference('Product_' . $n, $product);
        }

        $manager->flush();
    }
}
