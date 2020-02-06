<?php

declare(strict_types=1);

namespace App\Application\EventSubscriber\Transaction;

use App\Domain\Model\Transaction\Events\TransactionCreated;
use App\Domain\Model\User\Interfaces\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class TransactionCreatedHandler implements EventSubscriberInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @return array<string, string>
     *
     * @codeCoverageIgnore
     */
    public static function getSubscribedEvents() : array
    {
        return [TransactionCreated::class => 'handle'];
    }

    public function handle(TransactionCreated $event) : void
    {
        $transaction = $event->transaction();
        $user        = $transaction->user();

        $user->adjustBalanceByAmount((int) $transaction->amount()->getAmount());

        $this->userRepository->save($user);
    }
}
