<?php

declare(strict_types=1);

namespace App\Application\Persister\Product;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Domain\Model\Product\Interfaces\ProductRepository;
use App\Domain\Model\Product\Product;
use function assert;

final class ProductPersister implements DataPersisterInterface
{
    private ProductRepository $productRepository;

    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param mixed $data
     */
    public function supports($data) : bool
    {
        return $data instanceof Product;
    }

    /**
     * @param mixed $data
     */
    public function persist($data) : Product
    {
        assert($data instanceof Product);

        $this->productRepository->save($data);

        return $data;
    }

    /**
     * @param mixed $data
     */
    public function remove($data) : void
    {
        assert($data instanceof Product);

        $this->productRepository->delete($data);
    }
}
