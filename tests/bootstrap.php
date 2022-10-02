<?php

declare(strict_types=1);

use App\Tests\DbSchemaRecreator;

require dirname(__DIR__) . '/vendor/autoload.php';

if (file_exists(dirname(__DIR__) . '/config/bootstrap.php')) {
    require dirname(__DIR__) . '/config/bootstrap.php';
}

if (isset($_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'])) {
    passthru(sprintf(
        'php "%s/../bin/console" cache:clear --no-warmup --env=%s',
        __DIR__,
        $_ENV['BOOTSTRAP_CLEAR_CACHE_ENV'],
    ));
}

DbSchemaRecreator::createDatabase();
