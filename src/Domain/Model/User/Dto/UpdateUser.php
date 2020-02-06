<?php

declare(strict_types=1);

namespace App\Domain\Model\User\Dto;

final class UpdateUser
{
    private ?string $nickname;

    public function __construct(?string $nickname)
    {
        $this->nickname = $nickname;
    }

    public function nickname() : ?string
    {
        return $this->nickname;
    }
}
