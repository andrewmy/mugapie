<?php

declare(strict_types=1);

namespace App\Application\Dto\User;

use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\Dto\UpdateUser;
use Money\Currency;
use Money\Money;

final class UserInput
{
    /** @var string|null */
    public ?string $nickname;

    public function toDomainCreate(string $currency) : CreateUser
    {
        return new CreateUser(
            $this->nickname,
            new Money(0, new Currency($currency)),
        );
    }

    public function toDomainUpdate() : UpdateUser
    {
        return new UpdateUser($this->nickname);
    }
}
