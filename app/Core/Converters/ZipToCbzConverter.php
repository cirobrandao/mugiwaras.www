<?php

declare(strict_types=1);

namespace App\Core\Converters;

final class ZipToCbzConverter implements ConverterInterface
{
    private string $reason = 'ZIP converter not configured.';

    public function convert(string $sourcePath, string $targetPath): bool
    {
        if (!file_exists($sourcePath)) {
            $this->reason = 'Source not found.';
            return false;
        }
        if (!class_exists('ZipArchive')) {
            $this->reason = 'Zip extension not installed.';
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($sourcePath) !== true) {
            $this->reason = 'Cannot open ZIP.';
            return false;
        }

        $tmpDir = $this->tempDir('zip');
        $images = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!$name) {
                continue;
            }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif','webp','bmp','tif','tiff','jfif'], true)) {
                $images[] = $name;
            }
        }

        if (empty($images)) {
            $zip->close();
            $this->reason = 'No images found in ZIP.';
            $this->cleanup($tmpDir);
            return false;
        }

        foreach ($images as $name) {
            $zip->extractTo($tmpDir, [$name]);
        }
        $zip->close();

        $paths = [];
        foreach ($images as $name) {
            $path = $tmpDir . '/' . $name;
            if (is_file($path)) {
                $paths[] = $path;
            }
        }
        if (empty($paths)) {
            $this->reason = 'No images extracted.';
            $this->cleanup($tmpDir);
            return false;
        }
        natsort($paths);

        if (!$this->zipImages($paths, $targetPath)) {
            $this->reason = 'Failed to create CBZ.';
            $this->cleanup($tmpDir);
            return false;
        }

        $this->cleanup($tmpDir);
        return true;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    private function zipImages(array $images, string $targetPath): bool
    {
        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $zip = new \ZipArchive();
        if ($zip->open($targetPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return false;
        }
        $i = 1;
        foreach ($images as $img) {
            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
            $zip->addFile($img, str_pad((string)$i, 3, '0', STR_PAD_LEFT) . '.' . $ext);
            $i++;
        }
        $zip->close();
        return true;
    }

    private function tempDir(string $prefix): string
    {
        $root = dirname(__DIR__, 3) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $dir = $root . '/tmp/' . $prefix . '_' . bin2hex(random_bytes(6));
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    private function cleanup(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            $file->isDir() ? rmdir($file->getRealPath()) : unlink($file->getRealPath());
        }
        @rmdir($dir);
    }
}
