<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use App\Domain\Model\Product\ProductId;
use Money\Money;
use RuntimeException;
use Throwable;
use function array_map;
use function implode;
use function sprintf;

final class OrderOperationFailed extends RuntimeException
{
    private function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public static function costTooHigh(Money $cost, Money $balance) : self
    {
        return new self(
            sprintf(
                'Cannot proceed with order, its cost of %s %.2f exceeds the available balance of %s %.2f',
                $cost->getCurrency()->getCode(),
                (float) $cost->getAmount() / 100,
                $balance->getCurrency()->getCode(),
                (float) $balance->getAmount() / 100,
            ),
        );
    }

    public static function notEditable() : self
    {
        return new self('Order is not editable');
    }

    /**
     * @param ProductId[] $products
     */
    public static function hasForeignProducts(array $products) : self
    {
        return new self(
            sprintf(
                'Cannot proceed with order, it contains products from another user: %s',
                implode(', ', array_map(static function (ProductId $id) : string {
                    return (string) $id;
                }, $products)),
            ),
        );
    }

    public static function wrap(Throwable $previous) : self
    {
        return new self(
            sprintf('Order operation failed: %s', $previous->getMessage()),
            $previous,
        );
    }
}
