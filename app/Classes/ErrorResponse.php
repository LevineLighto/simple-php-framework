<?php

namespace App\Classes;

class ErrorResponse {

    public static function Failed($code = 500, $message = '') {
        http_response_code($code);
        $json = json_encode(['message' => $message]);

        echo $json;
        return;
    }
    
    public static function Unauthorized() {
        static::Failed(401, 'User Unauthorized');
    }

    public static function NotFound() {
        static::Failed(404, 'Not Found');
    }
}