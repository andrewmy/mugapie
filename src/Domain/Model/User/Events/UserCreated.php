<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Events;

use App\Domain\Model\Common\Interfaces\Event;
use App\Domain\Model\User\User;

final class UserCreated implements Event
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function user() : User
    {
        return $this->user;
    }
}
