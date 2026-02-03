<?php

declare(strict_types=1);

namespace App\Core\Converters;

final class CbrToCbzConverter implements ConverterInterface
{
    private string $reason = 'CBR converter not configured.';

    public function convert(string $sourcePath, string $targetPath): bool
    {
        if (!file_exists($sourcePath)) {
            $this->reason = 'Source not found.';
            return false;
        }
        $bin = (string)config('converters.unrar_bin', '');
        if ($bin !== '' && is_executable($bin)) {
            $tmpDir = $this->tempDir('cbr');
            $cmd = sprintf('"%s" e -idq %s %s', $bin, escapeshellarg($sourcePath), escapeshellarg($tmpDir));
            $output = [];
            $code = 0;
            exec($cmd . ' 2>&1', $output, $code);
            if ($code === 0) {
                $images = glob($tmpDir . '/*.{jpg,jpeg,png,gif,webp,bmp,tif,tiff,jfif}', GLOB_BRACE) ?: [];
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
            $this->cleanup($tmpDir);
            $this->reason = 'unrar failed: ' . implode(' ', $output);
        } else {
            $this->reason = 'unrar not configured.';
        }

        $sevenZip = new SevenZipToCbzConverter();
        if ($sevenZip->convert($sourcePath, $targetPath)) {
            return true;
        }
        $this->reason .= ' / ' . $sevenZip->reason();
        return false;
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
