<?php

namespace App\Classes;

class Session {
    public static function handle() {
        session_start();

        if(
            isset($_SESSION['LAST_ACTIVITY'])
            &&
            time() - $_SESSION['LAST_ACTIVITY'] > 3600
        ) {
            return static::destroy();
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    public static function Error() {
        if(empty($_SESSION['error'])) {
            return;
        }

        $message = $_SESSION['error'];
        unset($_SESSION['error']);

        return $message;
    }

    public static function destroy() {
        session_unset();
        session_destroy();

        session_start();
    }

    public static function restart() {
        static::destroy();

        static::handle();
    }
}