<?php

declare(strict_types=1);

namespace App\Infrastructure\Model\User;

use App\Domain\Model\User\Exceptions\UserPersistenceFailed;
use App\Domain\Model\User\Interfaces\UserRepository;
use App\Domain\Model\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

final class DoctrineUserRepository implements UserRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function save(User $user) : void
    {
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw UserPersistenceFailed::saveFailed($exception);
        }
    }

    public function delete(User $user) : void
    {
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        } catch (ORMException $exception) {
            throw UserPersistenceFailed::deleteFailed($exception);
        }
    }
}
