<?php

declare(strict_types=1);

namespace App\Application\Persister\Order;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;
use function assert;

final class OrderPersister implements DataPersisterInterface
{
    private OrderRepository $orderRepository;

    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param mixed $data
     */
    public function supports($data) : bool
    {
        return $data instanceof Order;
    }

    /**
     * @param mixed $data
     */
    public function persist($data) : Order
    {
        assert($data instanceof Order);

        $this->orderRepository->save($data);

        return $data;
    }

    /**
     * @param mixed $data
     */
    public function remove($data) : void
    {
        assert($data instanceof Order);

        $this->orderRepository->delete($data);
    }
}
