<?php

declare(strict_types=1);

namespace App\Core;

interface StorageInterface
{
    public function put(string $path, string $contents): bool;
    public function exists(string $path): bool;
    public function read(string $path): string;
    public function delete(string $path): bool;
}
