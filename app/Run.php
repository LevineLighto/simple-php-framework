<?php

use App\Classes\Config\Route;
use App\Classes\ErrorHandler;
use App\Classes\Session;
use App\Classes\URL;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(base_path());
$dotenv->load();

ErrorHandler::handle();

date_default_timezone_set('Asia/Jakarta');

Session::handle();

[$url, $params] = URL::parse();

Route::Run($url, $params);
exit();