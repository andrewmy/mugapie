<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\User;

use App\Domain\Model\Transaction\Interfaces\TransactionFactory;
use App\Domain\Model\Transaction\Interfaces\TransactionRepository;
use App\Domain\Model\User\Events\UserCreated;
use Money\Currency;
use Money\Money;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UserCreatedHandler implements EventSubscriberInterface
{
    private TransactionRepository $transactionRepository;

    private TransactionFactory $transactionFactory;

    private string $currency;

    private int $startingBalance;

    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionFactory $transactionFactory,
        string $currency,
        int $startingBalance
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory    = $transactionFactory;
        $this->currency              = $currency;
        $this->startingBalance       = $startingBalance;
    }

    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents() : array
    {
        return [UserCreated::class => 'handle'];
    }

    public function handle(UserCreated $event) : void
    {
        $this->transactionRepository->save(
            $this->transactionFactory->createForUser(
                $event->user(),
                new Money($this->startingBalance, new Currency($this->currency)),
            ),
        );
    }
}
