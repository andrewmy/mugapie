<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\User;

use App\Application\Exceptions\UserOperationFailed;
use App\Domain\Model\Transaction\Exceptions\TransactionCreationFailed;
use App\Domain\Model\Transaction\Exceptions\TransactionPersistenceFailed;
use App\Domain\Model\Transaction\Interfaces\TransactionFactory;
use App\Domain\Model\Transaction\Interfaces\TransactionRepository;
use App\Domain\Model\User\Events\UserCreated;
use Money\Currency;
use Money\Money;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserCreatedHandler implements EventSubscriberInterface
{
    private TransactionRepository $transactionRepository;

    private TransactionFactory $transactionFactory;

    private LoggerInterface $logger;

    private string $currency;

    private int $startingBalance;

    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionFactory $transactionFactory,
        LoggerInterface $logger,
        string $currency,
        int $startingBalance
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory    = $transactionFactory;
        $this->logger                = $logger;
        $this->currency              = $currency;
        $this->startingBalance       = $startingBalance;
    }

    /**
     * @return array<string, string>
     *
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents(): array
    {
        return [UserCreated::class => 'handle'];
    }

    public function handle(UserCreated $event): void
    {
        try {
            $transaction = $this->transactionFactory->createForUser(
                $event->user(),
                new Money($this->startingBalance, new Currency($this->currency)),
            );
            $this->transactionRepository->save($transaction);
        } catch (TransactionCreationFailed | TransactionPersistenceFailed $exception) {
            throw UserOperationFailed::wrap($exception);
        }

        $this->logger->info('New transaction', [
            'transaction_id' => (string) $transaction->id(),
            'user_id' => (string) $event->user()->id(),
            'amount' => $transaction->amount(),
        ]);
    }
}
