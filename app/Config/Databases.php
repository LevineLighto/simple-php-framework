<?php

return [
    'default'   => [
        'hostname'  => env('DB_HOST', 'localhost'),
        'username'  => env('DB_USERNAME', 'root'),
        'password'  => '',
        'database'  => env('DB_NAME', 'database'),
        'port'      => null
    ],
];