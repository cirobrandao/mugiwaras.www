<?php

declare(strict_types=1);

namespace App\Core\Converters;

final class SevenZipToCbzConverter implements ConverterInterface
{
    private string $reason = '7z converter not configured.';

    public function convert(string $sourcePath, string $targetPath): bool
    {
        if (!file_exists($sourcePath)) {
            $this->reason = 'Source not found.';
            return false;
        }
        $bin = trim((string)config('converters.sevenzip_bin', ''));
        if ($bin === '' || !is_executable($bin)) {
            $candidates = [
                'C:\\Program Files\\7-Zip\\7z.exe',
                'C:\\Program Files (x86)\\7-Zip\\7z.exe',
                '/usr/bin/7z',
                '/usr/local/bin/7z',
            ];
            foreach ($candidates as $candidate) {
                if (is_executable($candidate)) {
                    $bin = $candidate;
                    break;
                }
            }
        }
        if ($bin === '' || !is_executable($bin)) {
            $this->reason = '7z not configured.';
            return false;
        }

        $tmpDir = $this->tempDir('7z');
        $cmd = sprintf('"%s" x -y -bd -o%s %s', $bin, escapeshellarg($tmpDir), escapeshellarg($sourcePath));
        $output = [];
        $code = 0;
        exec($cmd . ' 2>&1', $output, $code);
        if ($code !== 0) {
            $this->reason = '7z failed: ' . implode(' ', $output);
            $this->cleanup($tmpDir);
            return false;
        }

        $images = $this->collectImages($tmpDir);
        if (!$images) {
            $this->reason = 'No images extracted.';
            $this->cleanup($tmpDir);
            return false;
        }
        natsort($images);

        if (!$this->zipImages($images, $targetPath)) {
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

    private function collectImages(string $root): array
    {
        if (!is_dir($root)) {
            return [];
        }
        $images = [];
        $iter = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iter as $file) {
            if (!$file->isFile()) {
                continue;
            }
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff', 'jfif'], true)) {
                $images[] = $file->getPathname();
            }
        }
        return $images;
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