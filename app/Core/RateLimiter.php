<?php

declare(strict_types=1);

namespace App\Core;

final class RateLimiter
{
    private string $path;

    public function __construct()
    {
        $this->path = dirname(__DIR__, 2) . '/storage/ratelimit';
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    public function hit(string $key, int $limit, int $windowSeconds): bool
    {
        $file = $this->path . '/' . sha1($key) . '.json';
        $now = time();
        $data = ['count' => 0, 'reset' => $now + $windowSeconds];
        if (file_exists($file)) {
            $data = json_decode((string)file_get_contents($file), true) ?: $data;
            if ($now > ($data['reset'] ?? 0)) {
                $data = ['count' => 0, 'reset' => $now + $windowSeconds];
            }
        }
        $data['count']++;
        file_put_contents($file, json_encode($data));
        return $data['count'] <= $limit;
    }
}
