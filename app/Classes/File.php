<?php

namespace App\Classes;

use App\Classes\Base\Storage;
use App\Classes\Config\Error;

class File extends Storage {
    public static function Mime($path) {
        if(!static::exists($path)) {
            return false;
        }

        return mime_content_type($path);
    }

    public static function Size($path) {
        if(!static::exists($path)) {
            return 0;
        }

        return filesize($path);
    }

    public static function Name($path) {
        if(!static::exists($path)) {
            return '';
        }

        return basename($path);
    }

    public static function Extension($path) {
        return strtolower(pathinfo($path, PATHINFO_EXTENSION));
    }

    public static function Response($path) {
        $path = base_path() . '/' . $path;
        if(!static::exists($path)) {
            Error::show(404);
        }

        $mime = static::Mime($path);
        $size = static::Size($path);
        $name = static::Name($path);
        
        header("Content-type: {$mime}");
        header("Content-length: {$size}");
        header("Content-Disposition: inline; filename={$name}");
        header("Content-Transfer-Encoding: binary");

        ob_clean();
        flush();

        readfile($path);
        die();
    }

    public static function Replace($oldpath, $newpath, $file) {
        static::Delete($oldpath);

        static::Store($file, $newpath);
    }

    public static function Remove($path) {
        static::Delete($path);
    }
}