<?php

namespace App\Support;

class FileHelper {
    public static function readFile($path) {
        return file_get_contents($path);
    }

    public static function writeFile($path, $content) {
        file_put_contents($path, $content);
    }

}

