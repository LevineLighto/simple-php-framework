<?php

use App\Controller\Error;
use App\Controller\Welcome;

/**
 * format route:
 * nama-url => [
 *    'class' => nama kelas controller,
 *    'method' => method
 *    'http-method' => ['GET','POST', 'DELETE'] pilih salah satu
 * ]
 * jika parameter false optional tidak diperlukan
 */
return [
    '/' => [
        'class'         => Welcome::class,
        'method'        => 'index',
        'http-method'   => ['GET', 'HEAD']
    ],

    'tidak-ditemukan' => [
        'class'         => Error::class,
        'method'        => 'notFound',
        'http-method'   => ['GET', 'HEAD']
    ],
];
