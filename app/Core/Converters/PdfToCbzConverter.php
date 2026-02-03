<?php

declare(strict_types=1);

namespace App\Core\Converters;

final class PdfToCbzConverter implements ConverterInterface
{
    private string $reason = 'PDF converter not configured.';

    public function convert(string $sourcePath, string $targetPath): bool
    {
        if (!file_exists($sourcePath)) {
            $this->reason = 'Source not found.';
            return false;
        }
        $bin = trim((string)config('converters.pdftoppm_bin', ''));
        if ($bin === '' || !is_executable($bin)) {
            $candidates = [
                '/usr/bin/pdftoppm',
                '/usr/local/bin/pdftoppm',
                'C:\\Program Files\\poppler\\Library\\bin\\pdftoppm.exe',
            ];
            foreach ($candidates as $candidate) {
                if (is_executable($candidate)) {
                    $bin = $candidate;
                    break;
                }
            }
        }
        if ($bin === '' || !is_executable($bin)) {
            $this->reason = 'pdftoppm not configured.';
            return false;
        }

        $dpi = (int)config('converters.pdftoppm_dpi', 120);
        if ($dpi < 72) {
            $dpi = 72;
        } elseif ($dpi > 300) {
            $dpi = 300;
        }
        $maxPages = (int)config('converters.pdftoppm_max_pages', 0);
        $jpegQuality = (int)config('converters.pdftoppm_jpeg_quality', 85);
        if ($jpegQuality < 40) {
            $jpegQuality = 40;
        } elseif ($jpegQuality > 95) {
            $jpegQuality = 95;
        }
        $tmpDir = $this->tempDir('pdf');
        $prefix = $tmpDir . '/page';

        $pageArgs = $maxPages > 0 ? sprintf('-f 1 -l %d', $maxPages) : '';
        $cmd = sprintf('"%s" -jpeg -jpegopt quality=%d -r %d %s %s %s', $bin, $jpegQuality, $dpi, $pageArgs, escapeshellarg($sourcePath), escapeshellarg($prefix));
        $output = [];
        $code = 0;
        exec($cmd . ' 2>&1', $output, $code);
        if ($code !== 0) {
            $this->reason = 'pdftoppm failed: ' . implode(' ', $output);
            $this->cleanup($tmpDir);
            return false;
        }

        $images = array_merge(
            glob($tmpDir . '/page-*.jpg') ?: [],
            glob($tmpDir . '/page-*.jpeg') ?: []
        );
        if (!$images) {
            $this->reason = 'No pages generated.';
            $this->cleanup($tmpDir);
            return false;
        }
        natsort($images);

        if (!$this->zipImages($images, $targetPath, true)) {
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

    private function zipImages(array $images, string $targetPath, bool $deleteAfter = false): bool
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
            if ($deleteAfter && is_file($img)) {
                @unlink($img);
            }
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
