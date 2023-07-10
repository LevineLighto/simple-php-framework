<?php

namespace App\Classes\Base;

/**
 * Abstrak class untuk mengambil
 * data dari file-file config
 */
abstract class Fetch {

    /**
     * Ambil data dari file config
     * yang bernama sama dengan classnya
     * 
     * @return mixed
     */
    protected static function fetch() {
        $classname = static::class;
        $exploded = explode('\\', $classname);
        $filename = array_pop($exploded);

        $value = include base_path()."/app/Config/{$filename}s.php";

        return $value;
    }


    /**
     * Melakukan pengecekan apakah config dengan 
     * kata kunci yang diminta ada atau tidak
     * 
     * @return boolean
     */
    protected static function exists($key) {
        $config = static::fetch();

        $key = strtolower($key);

        return array_key_exists($key, $config);
    }
}