<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Domain\Model\Order\Dto\CreateOrder;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Order\OrderId;
use App\Domain\Model\Order\ShippingAddress;
use App\Domain\Model\Order\ShippingType;
use App\Domain\Model\OrderItem\Dto\CreateOrderItem;
use App\Domain\Model\OrderItem\OrderItem;
use App\Domain\Model\OrderItem\OrderItemId;
use App\Domain\Model\Product\Dto\CreateProduct;
use App\Domain\Model\Product\Interfaces\ProductRepository;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductId;
use App\Domain\Model\Product\ProductType;
use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\Interfaces\UserRepository;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

use function assert;
use function in_array;
use function json_encode;

abstract class IntegrationTestCase extends ApiTestCase
{
    /** @param array<mixed> $data */
    protected function request(
        string $method,
        string $endpoint,
        array $data = []
    ): Client {
        /** @var Client $client */
        $client = static::createClient();

        $options = [
            'headers' => [
                'accept' => ['application/json'],
                'content-type' => 'application/json',
            ],
        ];
        if (in_array($method, ['POST', 'PUT'], true)) {
            $options['body'] = ($data === [] ? '{}' : json_encode($data));
        }

        $client->request($method, '/api/' . $endpoint, $options);

        return $client;
    }

    protected function createUser(string $id): User
    {
        self::bootKernel();
        $repository = self::$container->get(UserRepository::class);
        assert($repository instanceof UserRepository);
        $user = User::create(
            new UserId(Uuid::fromString($id)),
            new CreateUser(
                'abc',
                new Money(0, new Currency('USD')),
            ),
        );
        $repository->save($user);

        return $user;
    }

    private function getManagedUser(UserId $id): User
    {
        $em = self::$container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        $result = $em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->where('u.id = :id')
            ->setParameter('id', $id->value()->getBytes())
            ->getQuery()
            ->getSingleResult();
        assert($result instanceof User);

        return $result;
    }

    protected function createProduct(User $user, string $id, string $type = 'mug'): Product
    {
        self::bootKernel();

        $repository = self::$container->get(ProductRepository::class);
        assert($repository instanceof ProductRepository);

        $em = self::$container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        $userManaged = $this->getManagedUser($user->id());

        $product = Product::create(
            new ProductId(Uuid::fromString($id)),
            new CreateProduct(
                $userManaged,
                new ProductType($type),
                'prod abc',
                'prod-' . $id,
                new Money(1234, new Currency('USD')),
            ),
        );
        $repository->save($product);

        return $product;
    }

    private function getManagedProduct(ProductId $id): Product
    {
        $em = self::$container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        $result = $em->createQueryBuilder()
            ->from(Product::class, 'p')
            ->select('p')
            ->where('p.id = :id')
            ->setParameter('id', $id->value()->getBytes())
            ->getQuery()
            ->getSingleResult();
        assert($result instanceof Product);

        return $result;
    }

    /** @param CreateOrderItem[] $items */
    protected function createOrder(
        User $user,
        string $id,
        array $items
    ): Order {
        self::bootKernel();

        $repository = self::$container->get(OrderRepository::class);
        assert($repository instanceof OrderRepository);

        $em = self::$container->get(EntityManagerInterface::class);
        assert($em instanceof EntityManagerInterface);

        $userManaged = $this->getManagedUser($user->id());

        $order = Order::create(
            new OrderId(Uuid::fromString($id)),
            new CreateOrder(
                $userManaged,
                new ShippingType(ShippingType::STANDARD),
                ShippingAddress::createInternational(
                    'LV',
                    '-',
                    'Riga',
                    'Z1',
                    'LV-1001',
                    '1234',
                    'A B',
                ),
            ),
            new Money(4321, new Currency('USD')),
        );

        foreach ($items as $item) {
            OrderItem::create(
                new OrderItemId(Uuid::uuid4()),
                $order,
                new CreateOrderItem(
                    $this->getManagedProduct($item->product()->id()),
                    1,
                ),
            );
        }

        $repository->save($order);

        return $order;
    }
}
