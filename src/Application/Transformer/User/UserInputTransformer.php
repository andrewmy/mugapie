<?php

declare(strict_types=1);

namespace App\Application\Transformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\Application\Dto\User\UserInput;
use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use Ramsey\Uuid\Uuid;
use function assert;

final class UserInputTransformer implements DataTransformerInterface
{
    private string $currency;

    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []) : User
    {
        assert($object instanceof UserInput);

        $user = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        if ($user instanceof User) {
            $user->update($object->toDomainUpdate());

            return $user;
        }

        return User::create(
            new UserId(Uuid::uuid4()),
            $object->toDomainCreate($this->currency),
        );
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []) : bool
    {
        if ($data instanceof User) {
            return false;
        }

        return $to === User::class && ($context['input']['class'] ?? null) !== null;
    }
}
