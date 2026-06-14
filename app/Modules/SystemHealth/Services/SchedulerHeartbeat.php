<?php

namespace App\Modules\SystemHealth\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Throwable;

class SchedulerHeartbeat
{
    private const CACHE_KEY = 'system_health:scheduler_heartbeat:last_seen_at';

    public function __construct(
        private readonly CacheRepository $cache,
    ) {}

    public function record(): bool
    {
        try {
            return $this->cache->put(self::CACHE_KEY, now()->timestamp, now()->addDay());
        } catch (Throwable) {
            return false;
        }
    }

    public function ageSeconds(): ?int
    {
        try {
            $timestamp = $this->cache->get(self::CACHE_KEY);
        } catch (Throwable) {
            return null;
        }

        if (! is_int($timestamp) && ! ctype_digit((string) $timestamp)) {
            return null;
        }

        return max(0, now()->timestamp - (int) $timestamp);
    }
}
