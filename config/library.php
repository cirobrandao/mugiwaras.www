<?php

declare(strict_types=1);

return [
    'path' => env('LIBRARY_PATH', 'storage/library'),
    'extensions' => array_filter(array_map('trim', explode(',', env('LIBRARY_EXTENSIONS', 'cbz')))),
];
