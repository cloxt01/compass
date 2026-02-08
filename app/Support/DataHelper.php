<?php

namespace App\Support;

class DataHelper {
    public static function is_json(string $data): bool
    {
        json_decode($data);
        return json_last_error() === JSON_ERROR_NONE;
    }
}

