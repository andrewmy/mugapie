<?php

declare(strict_types=1);

namespace App\Application\Persister\User;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Application\Exceptions\UserOperationFailed;
use App\Application\Interfaces\TransactionalExecutor;
use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\User\Exceptions\UserPersistenceFailed;
use App\Domain\Model\User\Interfaces\UserRepository;
use App\Domain\Model\User\User;

use function assert;

final class UserPersister implements DataPersisterInterface
{
    private UserRepository $userRepository;

    private TransactionalExecutor $transactionalExecutor;

    public function __construct(
        UserRepository $userRepository,
        TransactionalExecutor $transactionalExecutor
    ) {
        $this->userRepository        = $userRepository;
        $this->transactionalExecutor = $transactionalExecutor;
    }

    /** @param mixed $data */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /** @param mixed $data */
    public function persist($data): User
    {
        assert($data instanceof User);

        try {
            $this->transactionalExecutor->execute(function () use ($data): void {
                $this->userRepository->save($data);
            });
        } catch (UserPersistenceFailed | TransactionPersistenceFailed $exception) {
            throw UserOperationFailed::wrap($exception);
        }

        return $data;
    }

    /** @param mixed $data */
    public function remove($data): void
    {
        assert($data instanceof User);

        $this->userRepository->delete($data);
    }
}
