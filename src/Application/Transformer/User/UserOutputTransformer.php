<?php

declare(strict_types=1);

namespace App\Application\Transformer\User;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Application\Dto\User\UserOutput;
use App\Domain\Model\User\User;

use function assert;

final class UserOutputTransformer implements DataTransformerInterface
{
    /**
     * @param object       $object
     * @param string       $to
     * @param array<mixed> $context
     */
    public function transform($object, string $to, array $context = []): UserOutput
    {
        assert($object instanceof User);

        $output            = new UserOutput();
        $output->id        = $object->id()->value();
        $output->createdAt = $object->createdAt();
        $output->updatedAt = $object->updatedAt();
        $output->balance   = (int) $object->balance()->getAmount();
        $output->nickname  = $object->nickname();

        return $output;
    }

    /**
     * @param array<mixed>|object $data
     * @param string              $to
     * @param array<mixed>        $context
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === UserOutput::class && $data instanceof User;
    }
}
