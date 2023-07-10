<?php

$basePath = __DIR__;
$basePath = str_replace('\\', '/', $basePath);

$rootPath = $_SERVER['DOCUMENT_ROOT'];

$omittedPath = str_replace($rootPath, '', $basePath);

/**
 * Tambah autoload class yang dibutuhin
 */

include __DIR__."/vendor/autoload.php";

/**
 * Tambah fungsi-fungsi tambahan
 */

include __DIR__."/app/Helpers/Helper.php";

/**
 * Jalanin aplikasinya
 */

include __DIR__."/app/Run.php";