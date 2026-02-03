<?php

declare(strict_types=1);

namespace App\Core;

final class StorageExternalStub implements StorageInterface
{
    public function put(string $path, string $contents): bool
    {
        return false;
    }

    public function exists(string $path): bool
    {
        return false;
    }

    public function read(string $path): string
    {
        return '';
    }

    public function delete(string $path): bool
    {
        return false;
    }
}
