<?php

declare(strict_types=1);

namespace App\Application\Persister\Order;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Application\Exceptions\OrderOperationFailed;
use App\Application\Interfaces\TransactionalExecutor;
use App\Domain\Model\Order\Exceptions\OrderPersistenceFailed;
use App\Domain\Model\Order\Interfaces\OrderRepository;
use App\Domain\Model\Order\Order;
use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\User\Exceptions\UserPersistenceFailed;

use function assert;

final class OrderPersister implements DataPersisterInterface
{
    private OrderRepository $orderRepository;

    private TransactionalExecutor $transactionalExecutor;

    public function __construct(
        OrderRepository $orderRepository,
        TransactionalExecutor $transactionalExecutor
    ) {
        $this->orderRepository       = $orderRepository;
        $this->transactionalExecutor = $transactionalExecutor;
    }

    /** @param mixed $data */
    public function supports($data): bool
    {
        return $data instanceof Order;
    }

    /** @param mixed $data */
    public function persist($data): Order
    {
        assert($data instanceof Order);

        try {
            $this->transactionalExecutor->execute(function () use ($data): void {
                $this->orderRepository->save($data);
            });
        } catch (OrderPersistenceFailed | TransactionPersistenceFailed | UserPersistenceFailed $exception) {
            throw OrderOperationFailed::wrap($exception);
        }

        return $data;
    }

    /** @param mixed $data */
    public function remove($data): void
    {
        // nothing happens here
    }
}
