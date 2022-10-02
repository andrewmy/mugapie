<?php

declare(strict_types=1);

namespace App\Application\Persister\Product;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Application\Exceptions\ProductOperationFailed;
use App\Application\Interfaces\TransactionalExecutor;
use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
use App\Domain\Model\Product\Exceptions\ProductPersistenceFailed;
use App\Domain\Model\Product\Interfaces\ProductRepository;
use App\Domain\Model\Product\Product;

use function assert;

final class ProductPersister implements DataPersisterInterface
{
    private ProductRepository $productRepository;

    private TransactionalExecutor $transactionalExecutor;

    public function __construct(
        ProductRepository $productRepository,
        TransactionalExecutor $transactionalExecutor
    ) {
        $this->productRepository     = $productRepository;
        $this->transactionalExecutor = $transactionalExecutor;
    }

    /** @param mixed $data */
    public function supports($data): bool
    {
        return $data instanceof Product;
    }

    /** @param mixed $data */
    public function persist($data): Product
    {
        assert($data instanceof Product);

        try {
            $this->transactionalExecutor->execute(function () use ($data): void {
                $this->productRepository->save($data);
            });
        } catch (ProductPersistenceFailed | OrderPersistenceFailed $exception) {
            throw ProductOperationFailed::wrap($exception);
        }

        return $data;
    }

    /** @param mixed $data */
    public function remove($data): void
    {
        assert($data instanceof Product);

        $this->productRepository->delete($data);
    }
}
