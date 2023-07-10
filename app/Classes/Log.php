<?php

namespace App\Classes;

use App\Classes\Base\Storage;

class Log extends Storage{

    private static $path = 'storage/logs/';

    public static function Error($message) {
        $now    = date('Y-m-d');
        $path   = static::$path."{$now}.log";
        $time   = date('H:i:s');

        static::toString($message);

        $message = "[ERROR] - {$time}: {$message}\n";

        return static::Write($path, $message);
    }

    public static function Log($message) {
        $now    = date('Y-m-d');
        $path   = static::$path."{$now}.log";
        $time   = date('H:i:s');

        static::toString($message);

        $message = "[LOG] - {$time}: {$message}\n";

        return static::Write($path, $message);
    }
}