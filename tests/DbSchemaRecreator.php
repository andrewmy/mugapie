<?php

declare(strict_types=1);

namespace App\Tests;

use function passthru;
use function sprintf;

final class DbSchemaRecreator
{
    public static function dropDatabase(): void
    {
        passthru(sprintf(
            'APP_ENV=test php "%s/../bin/console" doctrine:schema:drop --force',
            __DIR__,
        ));
    }

    public static function createDatabase(): void
    {
        self::dropDatabase();

        passthru(sprintf(
            'php "%s/../bin/console" doctrine:query:sql "DROP TABLE IF EXISTS migration_versions" -n --env=test',
            __DIR__,
        ));
        passthru(sprintf(
            'php "%s/../bin/console" doctrine:migrations:migrate -n --env=test',
            __DIR__,
        ));
    }
}
