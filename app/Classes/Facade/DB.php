<?php

namespace App\Classes\Facade;

use App\Classes\Base\Facade;
use App\Classes\DB as BaseDB;

/**
 * @method static \App\Classes\DB connection(string $name)
 * @method static \App\Classes\DB table(string $name)
 * @method static \App\Classes\DB prepare(string $query, array $data)
 * @method static \App\Classes\DB where(string $column, string $operator, mixed $value)
 */

class DB extends Facade {
    protected static function getFacadeAccessor() {
        return BaseDB::class;
    }
}