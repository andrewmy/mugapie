<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\Order;

use App\Application\Exceptions\OrderOperationFailed;
use App\Domain\Model\Order\Events\OrderSentToProduction;
use App\Domain\Model\Transaction\Exceptions\TransactionCreationFailed;
use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\Transaction\Interfaces\TransactionFactory;
use App\Domain\Model\Transaction\Interfaces\TransactionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class OrderSentToProductionHandler implements EventSubscriberInterface
{
    private TransactionRepository $transactionRepository;

    private TransactionFactory $transactionFactory;

    private LoggerInterface $logger;

    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionFactory $transactionFactory,
        LoggerInterface $logger
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory    = $transactionFactory;
        $this->logger                = $logger;
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
        try {
            $transaction = $this->transactionFactory->createForOrder(
                $event->order(),
                $event->order()->orderCost()->negative(),
            );
            $this->transactionRepository->save($transaction);
        } catch (TransactionCreationFailed | TransactionPersistenceFailed $exception) {
            throw OrderOperationFailed::wrap($exception);
        }

        $this->logger->info('New transaction', [
            'transaction_id' => (string) $transaction->id(),
            'order_id' => (string) $event->order()->id(),
            'amount' => $transaction->amount(),
        ]);
    }
}
