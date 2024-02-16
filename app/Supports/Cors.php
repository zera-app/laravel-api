<?php

namespace App\Supports;

final class Cors
{
    /**
     * Get Allowed Host
     */
    public static function getAllowedHost(): array
    {
        return explode(',', env('ALLOWED_HOST'));
    }
}
