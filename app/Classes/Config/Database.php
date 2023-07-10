<?php

namespace App\Classes\Config;

use App\Classes\Base\Fetch;

/**
 * Class untuk mengambil detail database
 */
class Database extends Fetch {

    /**
     * Mengambil database yang diinginkan
     */
    public static function get($name) {
        if(!static::exists($name)) {
            return false;
        }

        $databases = static::fetch();

        return $databases[$name];
    }
}