<?php

namespace App\Controller;

use App\Classes\Config\Error as ConfigError;

class Error {
    public function notFound() {
        return ConfigError::show(404);
    }
}