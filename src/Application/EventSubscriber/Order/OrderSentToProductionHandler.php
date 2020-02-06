<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\Order;

use App\Domain\Model\Order\Events\OrderSentToProduction;
use App\Domain\Model\Transaction\Interfaces\TransactionFactory;
use App\Domain\Model\Transaction\Interfaces\TransactionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OrderSentToProductionHandler implements EventSubscriberInterface
{
    private TransactionRepository $transactionRepository;

    private TransactionFactory $transactionFactory;

    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionFactory $transactionFactory
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory    = $transactionFactory;
    }

    /**
     * @return array<string, string>
     *
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents() : array
    {
        return [OrderSentToProduction::class => 'handle'];
    }

    public function handle(OrderSentToProduction $event) : void
    {
        $this->transactionRepository->save(
            $this->transactionFactory->createForOrder(
                $event->order(),
                $event->order()->orderCost()->negative(),
            ),
        );
    }
}
