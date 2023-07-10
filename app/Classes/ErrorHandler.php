<?php

namespace App\Classes;

use App\Classes\Config\Error;
use Throwable;

class ErrorHandler {

    /**
     * Method to handle error
     */
    public static function error($type, $message, $file, $line) {
        $type       = static::readableError($type);
        $message    = $message;
        $file       = $file;
        $line       = $line;

        static::logMessage($type, $message, $file, $line);

        return static::render();
    }

    /**
     * Method to handle fatal error
     */
    public static function shutdown() {
        if(!empty($error = error_get_last())) {
            $type       = static::readableError($error['type']);
            $message    = $error['message'];
            $file       = $error['file'];
            $line       = $error['line'];

            static::logMessage($type, $message, $file, $line);
        }

        return static::render();
    }

    /**
     * Method to handle Exception
     */
    public static function except(Throwable $error) {
        // $type       = static::readableError($error->getCode());
        $type       = $error->getCode();
        $message    = $error->getMessage() . $error->getTraceAsString();
        $file       = $error->getFile();
        $line       = $error->getLine();
        
        static::logMessage($type, $message, $file, $line);

        return static::render();
    }

    public static function handle() {
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        ini_set('error_log', base_path().'errors.log');

        // Handle Fatal Error
        // register_shutdown_function([static::class, 'shutdown']);

        // Handle Error
        // set_error_handler([static::class, 'error']);

        // Handle Exception
        set_exception_handler([static::class, 'except']);
    }

    /**
     * Display to user when error happens
     */
    protected static function render() {
        http_response_code(500);

        if(
            (!empty($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json') 
            || 
            (!empty($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] == 'application/json')
        ) {
            header('Content-type: application/json');
            echo json_encode(['message' => 'Server Error']);
            die();
        }

        header('Content-type: text/html');
        Error::show(500);
        die();
    }

    /**
     * Log error message
     */
    protected static function logMessage($type, $message, $file, $line) {
        $message    = "(Severity: {$type}) - {$file} ({$line}) \n{$message}\n";
        Log::Error($message);
    }

    /**
     * Turn Severity code to readable
     */
    protected static function readableError($type) {
        switch($type)
        {
            case E_ERROR: // 1 //
                return 'ERROR';
            case E_WARNING: // 2 //
                return 'WARNING';
            case E_PARSE: // 4 //
                return 'PARSE';
            case E_NOTICE: // 8 //
                return 'NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'USER_DEPRECATED';
        }
        return "";
    }
}