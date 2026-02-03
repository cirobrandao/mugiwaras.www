<?php

declare(strict_types=1);

namespace App\Core;

final class StorageLocal implements StorageInterface
{
    private string $root;

    public function __construct()
    {
        $this->root = dirname(__DIR__, 2) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        if (!is_dir($this->root)) {
            mkdir($this->root, 0777, true);
        }
    }

    public function put(string $path, string $contents): bool
    {
        $full = $this->sanitize($path);
        if (!is_dir(dirname($full))) {
            mkdir(dirname($full), 0777, true);
        }
        return (bool)file_put_contents($full, $contents);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->sanitize($path));
    }

    public function read(string $path): string
    {
        return (string)file_get_contents($this->sanitize($path));
    }

    public function delete(string $path): bool
    {
        $full = $this->sanitize($path);
        return file_exists($full) ? unlink($full) : true;
    }

    private function sanitize(string $path): string
    {
        $clean = str_replace(['..', '\\'], ['', '/'], $path);
        return rtrim($this->root, '/') . '/' . ltrim($clean, '/');
    }
}
