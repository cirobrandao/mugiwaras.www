<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Converters\ImageToCbzConverter;
use App\Core\Converters\CbrToCbzConverter;
use App\Core\Converters\EpubToCbzConverter;
use App\Core\Converters\ZipToCbzConverter;

final class JobProcessor
{
    public function process(array $job): bool
    {
        $type = $job['job_type'] ?? '';
        $payload = json_decode((string)$job['payload'], true) ?: [];
        $source = $payload['source'] ?? '';
        $target = $payload['target'] ?? '';

        switch ($type) {
            case 'images_to_cbz':
                $converter = new ImageToCbzConverter();
                break;
            case 'cbr_to_cbz':
                $converter = new CbrToCbzConverter();
                break;
            case 'epub_to_cbz':
                $converter = new EpubToCbzConverter();
                break;
            case 'zip_to_cbz':
                $converter = new ZipToCbzConverter();
                break;
            default:
                throw new \RuntimeException('Unknown job type');
        }

        if (!$converter->convert($source, $target)) {
            throw new \RuntimeException($converter->reason());
        }

        return true;
    }
}
