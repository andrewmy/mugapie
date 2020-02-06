<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Interfaces;

use App\Domain\Model\User\Exceptions\UserPersistenceFailed;
use App\Domain\Model\User\User;

interface UserRepository
{
    /**
     * @throws UserPersistenceFailed
     */
    public function save(User $user) : void;

    /**
     * @throws UserPersistenceFailed
     */
    public function delete(User $user) : void;
}
