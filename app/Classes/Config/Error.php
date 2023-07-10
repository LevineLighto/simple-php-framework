<?php

namespace App\Classes\Config;

use App\Classes\Base\Fetch;

/**
 * Class untuk menampilkan laman error
 */
class Error extends Fetch {

    public static function show($code) {
        if(!static::exists($code)) {
            return static::show(404);
        }

        $views = static::fetch();

        return layout('layouts.error', $views[$code]);
    }
}