<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}


use Doctrine\Deprecations\Deprecation;
// Ignore unfixable Doctrine deprecations
Deprecation::ignoreDeprecations(
    'https://github.com/doctrine/orm/pull/11211', // The ORM changed from arrays to named data objects in 3.x, some packages still use array access for B/C
);