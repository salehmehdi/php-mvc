<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return [

    'default' => $_ENV['DB_CONNECTION'] ?: 'mysql',

    'connections' => [

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => $_ENV['DB_HOST'],
            'port'      => $_ENV['DB_PORT'],
            'database'  => $_ENV['DB_DATABASE'],
            'username'  => $_ENV['DB_USERNAME'],
            'password'  => $_ENV['DB_PASSWORD'],
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],

        'mongodb' => [
            'driver'   => 'mongodb',
            'host'     => $_ENV['MONGODB_HOST'],
            'port'     => $_ENV['MONGODB_PORT'],
            'database' => $_ENV['MONGODB_DATABASE'],
            'username' => $_ENV['MONGODB_USERNAME'],
            'password' => $_ENV['MONGODB_PASSWORD'],
            'options'  => [
                'database' => getenv('MONGODB_DATABASE'),
            ],
        ],

    ],

];
