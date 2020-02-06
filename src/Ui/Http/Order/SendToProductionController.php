<?php

declare(strict_types=1);

namespace App\Ui\Http\Order;

use App\Application\Handler\Order\SendToProductionHandler;
use App\Domain\Model\Order\Order;

final class SendToProductionController
{
    private SendToProductionHandler $handler;

    public function __construct(SendToProductionHandler $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(Order $data) : Order
    {
        return $this->handler->handle($data);
    }
}
