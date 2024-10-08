<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// executes the "php bin/console cache:clear" command
passthru(sprintf(
    'php "%s/../bin/console" --env=%s cache:clear --no-warmup',
    __DIR__,
    $_ENV['APP_ENV']
));
