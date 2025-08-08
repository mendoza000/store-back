<?php

namespace App\Services;

use App\Models\Store;

class CurrentStore
{
    protected static ?Store $store = null;

    public static function set(?Store $store): void
    {
        self::$store = $store;
    }

    public static function get(): ?Store
    {
        return self::$store;
    }

    public static function id(): ?string
    {
        return self::$store?->id;
    }

    public static function has(): bool
    {
        return self::$store !== null;
    }
}
