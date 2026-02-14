<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple in-memory cache for request lifecycle
 * Reduces repeated database queries for rarely-changing data
 */
final class SimpleCache
{
    private static array $store = [];

    /**
     * Get or compute a cached value
     * @param string $key Cache key
     * @param int $ttl Time to live in seconds
     * @param callable $callback Function to compute value if not cached
     * @return mixed Cached or computed value
     */
    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        if (isset(self::$store[$key])) {
            [$value, $expires] = self::$store[$key];
            if (time() < $expires) {
                return $value;
            }
        }
        
        $value = $callback();
        self::$store[$key] = [$value, time() + $ttl];
        return $value;
    }

    /**
     * Store a value directly in cache
     */
    public static function put(string $key, mixed $value, int $ttl): void
    {
        self::$store[$key] = [$value, time() + $ttl];
    }

    /**
     * Get a cached value without callback
     */
    public static function get(string $key): mixed
    {
        if (isset(self::$store[$key])) {
            [$value, $expires] = self::$store[$key];
            if (time() < $expires) {
                return $value;
            }
        }
        return null;
    }

    /**
     * Remove a cached value
     */
    public static function forget(string $key): void
    {
        unset(self::$store[$key]);
    }

    /**
     * Clear all cached values
     */
    public static function flush(): void
    {
        self::$store = [];
    }
}
