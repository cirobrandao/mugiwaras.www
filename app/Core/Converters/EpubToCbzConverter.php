<?php

declare(strict_types=1);

namespace App\Core\Converters;

final class EpubToCbzConverter implements ConverterInterface
{
    private string $reason = 'EPUB converter not configured.';

    public function convert(string $sourcePath, string $targetPath): bool
    {
        if (!file_exists($sourcePath)) {
            $this->reason = 'Source not found.';
            return false;
        }
        $bin = trim((string)config('converters.ebook_convert_bin', ''));
        if ($bin === '' || !is_executable($bin)) {
            $candidates = [
                '/usr/bin/ebook-convert',
                '/usr/local/bin/ebook-convert',
                'C:\\Program Files\\Calibre2\\ebook-convert.exe',
            ];
            foreach ($candidates as $candidate) {
                if (is_executable($candidate)) {
                    $bin = $candidate;
                    break;
                }
            }
        }
        if ($bin === '' || !is_executable($bin)) {
            $this->reason = 'ebook-convert not configured.';
            return false;
        }

        $dir = dirname($targetPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $cmd = sprintf('"%s" %s %s', $bin, escapeshellarg($sourcePath), escapeshellarg($targetPath));
        $output = [];
        $code = 0;
        exec($cmd . ' 2>&1', $output, $code);
        if ($code !== 0) {
            $this->reason = 'ebook-convert failed: ' . implode(' ', $output);
            return false;
        }

        if (!file_exists($targetPath)) {
            $this->reason = 'CBZ not generated.';
            return false;
        }

        return true;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
