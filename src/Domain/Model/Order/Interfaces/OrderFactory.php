<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Interfaces;

use App\Domain\Model\Order\Exceptions\OrderCreationFailed;
use App\Domain\Model\Order\Order;

interface OrderFactory
{
    /**
     * @throws OrderCreationFailed
     */
    public function create() : Order;
}
