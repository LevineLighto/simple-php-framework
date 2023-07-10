<?php

use App\Classes\Formatter;
use App\Classes\Session;

if(!function_exists('base_path')) {
    /**
     * Mengambil lokasi directory aplikasi
     * @return string
     */
    function base_path() {
        global $basePath;
        return $basePath;
    }
}

if(!function_exists('view')) {
    /**
     * Mengambil file view yang akan ditampilkan ke pengguna
     * 
     * @param string $name nama layout
     * @param array $data data yang diperlukan
     */
    function view($name, $data = []) {
        $name = str_replace('.', '/', $name);
        extract($data);

        return include base_path()."/view/{$name}.php";
    }
}

if(!function_exists('layout')) {
    /**
     * Mengambil file layout yang akan ditampilkan pengguna
     * Sebenarnya sama seperti fungsi view, tapi dibedakan
     * agar lebih enak dibaca. 
     * Mungkin perlu dibuat agar langsung ke folder layout
     * sebagai pembeda
     * 
     * @param string $name nama layout
     * @param array $data data yang diperlukan
     */
    function layout($name, $data = []) {
        if(!str_starts_with($name, 'layouts')) {
            $name = "layouts.{$name}";
        }
        view($name, $data);
        die();
    }
}

if (!function_exists('isSecure')) {
    /**
     * Cek apakah server https atau tidak
     * 
     * @return bool
     */
    function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
    }
}

if(!function_exists('url')) {
    /**
     * Membuat url yang sesuai dengan url aplikasi
     * 
     * @param string $path
     * @return string
     */
    function url($path) {
        $path = ltrim($path, '/');

        global $omittedPath;
        $omittedPath = trim($omittedPath, '/');

        if(!empty($omittedPath)) {
            $path = "{$omittedPath}/{$path}";
        }

        if(isSecure()) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];

        if($port == '80' || $port == 443) {
            $port = '';
        }

        if(!empty($port)) {
            $port = ":{$port}";
        }

        return "{$protocol}{$host}{$port}/{$path}";
    }
}

if(!function_exists('asset')) {
    /**
     * Mendapatkan url ke folder asset.
     * 
     * @param string $path
     * 
     * @return string
     */
    function asset($path) {
        return url("assets/{$path}");
    }
}

if(!function_exists('urlNow')) {
    /**
     * Mendapatkan url sekarang. 
     * 
     * Jika semisal url sekarang adalah...
     * https://example.com/location/999/edit
     * 
     * Apabila parameter $withParam bernilai false
     * maka akan return
     * https://example.com/location.
     * Dan Jika true maka akan return
     * https://example.com/location/999/edit
     * 
     * @param bool $withParam 
     * 
     * @return string
     */
    function urlNow($withParam = false) {
        if(!$withParam) {
            return url($_SESSION['current_url']);
        }

        if(isSecure()) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        $host   = $_SERVER['SERVER_NAME'];
        $port   = $_SERVER['SERVER_PORT'];
        $uri    = trim($_SERVER['REQUEST_URI'], '/');

        if($port == '80' || $port == 443) {
            $port = '';
        }

        if(!empty($port)) {
            $port = ":{$port}";
        }

        return "{$protocol}{$host}{$port}/{$uri}";
    }
}

if(!function_exists('redirect')) {
    /**
     * Melakukan redirect ke lokasi lain dalam aplikasi
     * 
     * @param string $path
     * @param int $http_code
     */
    function redirect($path, int $http_code = 301) {
        $url = url($path);
        header("Location: {$url}", true, $http_code);
        exit();
    }
}

if(!function_exists('formatDate')) {
    /**
     * Melakukan pemformatan tanggal
     * 
     * @param string|int $date
     * @param string $format
     * 
     * @return string
     */
    function formatDate($date, $format = 'd M Y H:i:s') {
        return Formatter::date($date, $format);
    }
}

if(!function_exists('readableDate')) {
    /**
     * Melakukan format data dalam bahasa Indonesia
     * 
     * @param DateTime $date
     * @param int $dateFormat
     * @param int $timeFormat
     * 
     * @return string
     */
    function readableDate($date, $dateFormat = IntlDateFormatter::FULL, $timeFormat = IntlDateFormatter::LONG) {
        return Formatter::readableDate($date, $dateFormat, $timeFormat);
    }
}

if(!function_exists('dateValid')) {
    /**
     * Cek apakah string date valid
     */
    function dateValid(string $date) {
        try {
            $date = new DateTime($date);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}

if(!function_exists('jsonResponse')) {
    /**
     * Memastikan response yang akan diberikan
     * aplikasi adalah json
     */
    function jsonResponse() {
        header('Content-type: application/json');
    }
}

if(!function_exists('htmlResponse')) {
    /**
     * Untuk memastikan response yang diberikan adalah html
     */
    function htmlResponse() {
        header('Content-type: text/html');
    }
}

if(!function_exists('debug')) {
    /**
     * Menampilkan variabel untuk proses 
     * debugging
     * @param mixed $data
     */
    function debug(...$data) {
        $previous = false;
        foreach($data as $debug) {
            if($previous) {
                echo '<hr/>';
            }
            echo '<pre>';
            echo json_encode($debug, JSON_PRETTY_PRINT);
            $previous = true;
            echo '</pre>';
        }
        die();
    }
}

if(!function_exists('isAssoc')) {
    /**
     * Melakukan pengecekan apakah
     * array assosiatif atau bukan
     * 
     * @param array $array
     * @return bool
     */
    function isAssoc(array $array) {
        if(empty($array)) return false;

        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if(!function_exists('sanitize')) {
    /**
     * Melakukan sanitasi data
     * @param mixed $data
     * @return mixed 
     */
    function sanitize($data) {
        if(is_array($data)) {
            foreach ($data as $index => $input) {
                if(is_string($input)) {
                    $data[$index] = strip_tags($input);
                }
            }
        } else if(is_string($data)) {
            $data = strip_tags($data);
        }

        return $data;
    }
}

if(!function_exists('toObject')) {
    /**
     * Mengubah array assosiatif menjadi
     * object
     * 
     * @param array $assoc_array
     * @return stdClass
     */
    function toObject($assoc_array) {
        if(!isAssoc($assoc_array)) {
            return;
        }

        $assoc_array = sanitize($assoc_array);

        return json_decode(json_encode($assoc_array));
    }
}

if(!function_exists('toArray')) {
    /**
     * Mengubah obyek menjadi array
     * assosiatif
     * 
     * @param stdClass $object
     * @return array
     */
    function toArray($object) {
        if(!is_object($object)) {
            throw new Exception('Data is not an object');
        }

        return get_object_vars($object);
    }
}

if(!function_exists('now')) {
    /**
     * Waktu sekarang
     */
    function now() {
        return new DateTime();
    }
}

if(!function_exists('formatPrice')) {
    /**
     * Melakukan format angka
     * menjadi format uang
     * 
     * @param int $amount
     * @return string
     */
    function formatPrice($amount) {
        return Formatter::price($amount);
    }
}

if(!function_exists('config')) {
    function config($configName) {
        $names  = explode('.', $configName);
        $name   = ucfirst(strtolower($names[0]));
        if(str_ends_with($name, 's')) {
            $name .= '.php';
        } else {
            $name .= 's.php';
        }
        $config = include base_path()."/app/Config/{$name}";

        if(count($names) > 1) {
            for($index = 1; $index < count($names); $index++) {
                $config = $config[$names[$index]];
            }
        }

        return $config;
    }
}

if(!function_exists('env')) {
    function env($envName, $default = null) {
        if(!empty($_SERVER[$envName])) {
            return $_SERVER[$envName];
        }

        if(!empty($_ENV[$envName])) {
            return $_ENV[$envName];
        }

        return $default;
    }
}

if(!function_exists('array_pluck')) {
    /**
     * Mengambil satu property
     * dari sekumpulan object di dalam array
     */
    function array_pluck(array $array, string $key) {
        $plucked = array_map(
            function($object) use ($key) {
                return $object->$key;
            },
            $array
        );

        return array_filter($plucked);
    }
}

if(!function_exists('getErrorMsg')) {
    /**
     * Mendapatkan pesan error & menghapus
     * pesan error dari sesi
     */
    function getErrorMsg() {
        return Session::Error();
    }
}

if(!function_exists('replaceStrVar')) {
    /**
     * Menyisipkan variabel ke dalam string
     */
    function replaceStrVar(string $string, array $array) {
        foreach($array as $key => $value) {
            $string = str_replace(":{$key}", $value, $string);
        }

        return $string;
    }
}