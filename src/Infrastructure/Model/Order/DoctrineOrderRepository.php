<?php

declare(strict_types=1);

namespace App\Infrastructure\Model\Order;

use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

final class DoctrineOrderRepository implements OrderRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function save(Order $order) : void
    {
        try {
            $this->entityManager->persist($order);
            // cascade persist doesn't work if all we change is items
            foreach ($order->items() as $item) {
                $this->entityManager->persist($item);
            }

            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw OrderPersistenceFailed::saveFailed($exception);
        }
    }

    public function delete(Order $order) : void
    {
        try {
            $this->entityManager->remove($order);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw OrderPersistenceFailed::deleteFailed($exception);
        }
    }

    /**
     * @return Order[]
     */
    public function findAllHavingProduct(Product $product) : array
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Order::class, 'o')
            ->leftJoin('o.items', 'items')
            ->select(['o', 'items'])
            ->where('items.product = :product')
            ->setParameter('product', $product->id()->value()->getBytes())
            ->getQuery()
            ->getResult();
    }
}
