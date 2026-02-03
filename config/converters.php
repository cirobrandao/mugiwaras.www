<?php

declare(strict_types=1);

return [
    'pdftoppm_bin' => env('PDFTOPPM_BIN', ''),
    'pdftoppm_dpi' => (int)env('PDFTOPPM_DPI', '150'),
    'unrar_bin' => env('UNRAR_BIN', ''),
    'sevenzip_bin' => env('SEVENZIP_BIN', ''),
    'ebook_convert_bin' => env('EBOOK_CONVERT_BIN', ''),
];