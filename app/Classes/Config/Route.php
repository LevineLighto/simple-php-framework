<?php

namespace App\Classes\Config;

use App\Classes\Base\Fetch;

/**
 * Class untuk mengarahkan ke file yang diinginkan
 */
class Route extends Fetch{

    protected static $withoutAuth = ['/', 'daftar', 'register', 'lupa-password', 'api-asal-wilayah'];

    /**
     * Cek apakah route menerima argumen,
     * @return true Jika route dapat menerima argumen dan ada argumen yang diterima,
     * @return false Jika route dapat menerima argumen dan tidak ada argumen diterima,
     * @return false Jika route tidak menerima argumen namun ada argumen yang diterima,
     * @return true Jika route tidak menerima argumen dan tidak ada argumen yang diterima
     */
    private static function checkParam($url, $params = []) {
        $routes = static::fetch();

        $acceptParam    = $routes[$url]['parameter'];
        $optionalParam  = $routes[$url]['optional'];
        $hasParam       = !empty($params);

        // Disable optional if doesn't accept param
        if(!$acceptParam) {
            $optionalParam = false;
        }

        // Check if route has param or is optional
        $hasParam = $optionalParam || $hasParam;


        return !($acceptParam xor $hasParam);
    }

    /**
     * Jalankan metode yang diinginkan sesuai dengan url yang diminta
     */
    public static function Run($url, $param = []) {
        static::CheckAuth($url);

        if(!empty($_SESSION['prev-url'])) {
            return redirect($_SESSION['prev-url']);
        }

        if(!static::exists($url)) {
            return redirect('tidak-ditemukan', 301);
        }

        if(!static::checkParam($url, $param)) {
            return redirect('tidak-ditemukan', 301);
        }

        $routes     = static::fetch();
        $controller = $routes[$url];

        $http_methods   = $controller['http-method'];
        $current_method = strtoupper($_SERVER['REQUEST_METHOD']);

        if(!in_array($current_method, $http_methods)) {
            return Error::show(405);
        }

        $classname  = $controller['class'];
        $method     = $controller['method'];

        return (new $classname)->$method(...$param);
    }

    /**
     * Cek apakah sudah login atau belum
     */
    protected static function CheckAuth($url) {
        if(in_array($url, static::$withoutAuth)) {
            return;
        }

        if(empty($_SESSION['userlogin'])) {
            $_SESSION['prev_url'] = trim($_SERVER['REQUEST_URI'], '/');
            return redirect('daftar');        
        }
    }
}