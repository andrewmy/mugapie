<?php

declare(strict_types=1);

namespace App\Domain\Model\Order\Events;

use App\Domain\Model\Common\Interfaces\Event;
use App\Domain\Model\Order\Order;

final class OrderSentToProduction implements Event
{
    private Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function order(): Order
    {
        return $this->order;
    }
}
