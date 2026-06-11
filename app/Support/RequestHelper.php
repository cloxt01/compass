<?php

namespace App\Support;

class RequestHelper
{
    public static function parseSetCookieToCookieString(string $setCookieHeader): string
    {
        $cookiePart = strtok($setCookieHeader, ';');
        return $cookiePart !== false ? trim($cookiePart) : '';
    }
}
