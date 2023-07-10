<?php

namespace App\Classes;

use Imagick;

class Image {
    public static function createThumbnail(string $source, string $name) {
        $imagick = new Imagick();
        if(File::Mime($source) == 'pdf') {
            $source .= '[0]';
        }
        $imagick->readImage($source);
        $width  = $imagick->getImageWidth();
        $cropWidth  = $width * 0.8;
        $cropHeight = $cropWidth * 9 / 16;
        $imagick->cropImage($cropWidth, $cropHeight, $width/2 - $cropWidth/2, 0);
        $imagick->mergeImageLayers(Imagick::LAYERMETHOD_MERGE);
        $imagick->scaleImage(320, 180);
        $imagick->writeImage($name);
    }

    public static function replaceThumbnail(string $oldFile, string $source, string $name) {
        if(File::exists($oldFile)) {
            File::Remove($oldFile);
        }

        static::createThumbnail($source, $name);
    }
}