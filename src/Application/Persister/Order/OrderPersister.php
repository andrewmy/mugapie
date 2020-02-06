<?php

declare(strict_types=1);

namespace App\Application\Persister\Order;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Application\Exceptions\OrderOperationFailed;
use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
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

        try {
            $this->orderRepository->save($data);
        } catch (OrderPersistenceFailed $exception) {
            throw OrderOperationFailed::wrap($exception);
        }

        return $data;
    }

    /**
     * @param mixed $data
     */
    public function remove($data) : void
    {
        // nothing happens here
    }
}
