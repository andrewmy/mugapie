<?php

declare(strict_types=1);

namespace App\Application\Persister\User;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Domain\Model\User\Interfaces\UserRepository;
use App\Domain\Model\User\User;
use function assert;

final class UserPersister implements DataPersisterInterface
{
    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    /**
     * @param mixed $data
     */
    public function supports($data) : bool
    {
        return $data instanceof User;
    }

    /**
     * @param mixed $data
     */
    public function persist($data) : User
    {
        assert($data instanceof User);

        $this->userRepository->save($data);

        return $data;
    }

    /**
     * @param mixed $data
     */
    public function remove($data) : void
    {
        assert($data instanceof User);

        $this->userRepository->delete($data);
    }
}
