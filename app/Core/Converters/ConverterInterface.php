<?php

declare(strict_types=1);

namespace App\Core\Converters;

interface ConverterInterface
{
    public function convert(string $sourcePath, string $targetPath): bool;
    public function reason(): string;
}
