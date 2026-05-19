<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SidebarCacheService
{
    private const TTL_MINUTES = 15;

    public static function getCacheKey(int $userId): string
    {
        return "sidebar_projects_{$userId}";
    }

    public static function remember(int $userId, \Closure $callback)
    {
        return Cache::remember(self::getCacheKey($userId), now()->addMinutes(self::TTL_MINUTES), $callback);
    }

    public static function forget(int $userId): void
    {
        Cache::forget(self::getCacheKey($userId));
    }

    public static function forgetForMembers(array $memberIds): void
    {
        foreach ($memberIds as $id) {
            self::forget($id);
        }
    }
}
