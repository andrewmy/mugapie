<?php

declare(strict_types=1);

namespace App\Infrastructure\Model\Product;

use App\Domain\Model\Product\Exceptions\ProductPersistenceFailed;
use App\Domain\Model\Product\Interfaces\ProductRepository;
use App\Domain\Model\Product\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

final class DoctrineProductRepository implements ProductRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function save(Product $product): void
    {
        try {
            $this->entityManager->persist($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw ProductPersistenceFailed::saveFailed($exception);
        }
    }

    public function delete(Product $product): void
    {
        try {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw ProductPersistenceFailed::deleteFailed($exception);
        }
    }
}
