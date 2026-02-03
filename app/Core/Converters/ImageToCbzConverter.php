<?php

declare(strict_types=1);

namespace App\Core\Converters;

final class ImageToCbzConverter implements ConverterInterface
{
    private string $reason = 'Image converter not configured.';

    public function convert(string $sourcePath, string $targetPath): bool
    {
        if (!file_exists($sourcePath)) {
            $this->reason = 'Source not found.';
            return false;
        }
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $this->reason = 'Unsupported image.';
            return false;
        }

        $zip = new \ZipArchive();
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if ($zip->open($targetPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->reason = 'Cannot create CBZ.';
            return false;
        }
        $zip->addFile($sourcePath, '001.' . $ext);
        $zip->close();
        return true;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
