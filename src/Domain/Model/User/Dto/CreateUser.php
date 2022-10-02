<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Dto;

use Money\Money;

final class CreateUser
{
    private ?string $nickname;

    private Money $balance;

    public function __construct(
        ?string $nickname,
        Money $balance
    ) {
        $this->nickname = $nickname;
        $this->balance  = $balance;
    }

    public function nickname(): ?string
    {
        return $this->nickname;
    }

    public function balance(): Money
    {
        return $this->balance;
    }
}
