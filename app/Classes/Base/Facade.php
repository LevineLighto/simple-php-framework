<?php

namespace App\Classes\Base;

use Exception;

/**
 * Abstract class untuk mempermudah
 * akses method-method class.
 */

abstract class Facade {

    /**
     * Method untuk menetapkan class yang
     * akan digunakan.
     */
    protected static function getFacadeAccessor() {
        throw new Exception('No Accessor Set.');
    }

    public static function __callStatic($method, $arguments)
    {
        $instance = static::getFacadeAccessor();

        if(!$instance) {
            throw new Exception('A facade root has not been set.');
        }

        return (new $instance)->$method(...$arguments);
    }
}