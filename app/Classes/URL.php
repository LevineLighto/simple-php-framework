<?php

namespace App\Classes;

class URL {
    static function parse() {
        $uri = urldecode(
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        );

        global $omittedPath;
        
        $uri = str_replace($omittedPath, '', $uri);
        
        $uri = trim($uri);
        $uri = rtrim($uri, '/');

        
        $params = explode("/", $uri);
        
        if(count($params) > 1) {
            $url    = $params[1];
            array_splice($params, 0, 2);
        } else {
            $url = '/';
            $params = [];
        }

        $_SESSION['current_url'] = $url;

        return [$url, $params];
    }
}