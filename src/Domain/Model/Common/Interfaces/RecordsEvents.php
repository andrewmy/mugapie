<?php

declare(strict_types=1);

namespace App\Domain\Model\Common\Interfaces;

interface RecordsEvents
{
    /** @return Event[] */
    public function popEvents(): array;
}
