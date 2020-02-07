<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\DataFixtures;

use App\Domain\Model\User\Dto\CreateUser;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

/**
 * @codeCoverageIgnore
 */
final class UserFixture extends Fixture
{
    private Currency $currency;

    public function __construct(string $currency)
    {
        $this->currency = new Currency($currency);
    }

    public function load(ObjectManager $manager) : void
    {
        $list = [
            ['nickname' => 'Founding Member'],
            ['nickname' => 'Some Rando'],
        ];

        foreach ($list as $n => $data) {
            $user = User::create(
                new UserId(Uuid::uuid4()),
                new CreateUser(
                    $data['nickname'],
                    new Money(0, $this->currency),
                ),
            );

            $manager->persist($user);
            $this->setReference('User_' . $n, $user);
        }

        $manager->flush();
    }
}
