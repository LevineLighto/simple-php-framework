<?php

namespace App\Classes\Base;

abstract class Storage {
    protected static function Write($path, $content) {
        $path = base_path(). '/' . $path;

        $file = fopen($path, 'a');

        if(!$file) {
            return;
        }

        fwrite($file, $content);
        
        return fclose($file);
    }

    protected static function Delete($path) {
        $path = base_path(). '/' . $path;

        if(!static::exists($path)) {
            return;
        }

        unlink($path);
    }

    public static function Store($source, $path) {
        $path = base_path(). '/' . $path;

        if(static::exists($path)) {
            return;
        }

        return move_uploaded_file($source, $path);
    }

    public static function exists($path) {
        return file_exists($path);
    }

    protected static function toString(&$content) {
        if(!is_string($content)) {
            $content = json_encode($content);
        }
    }
}