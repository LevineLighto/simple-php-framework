<?php

namespace App\Classes\Facade;

use App\Classes\Base\Facade;
use App\Classes\RemoteFile as BaseRemoteFile;


/**
 * @method static void Get(string $filename)
 * @method static void Store($file, string $filename)
 * @method static void Delete(string $filename)
 * @method static \App\Classes\RemoteFile Folder(string $foldername)
 */
class RemoteFile extends Facade {
    protected static function getFacadeAccessor() {
        return BaseRemoteFile::class;
    }
}