<?php

declare(strict_types=1);

return [
    'driver' => env('STORAGE_DRIVER', 'local'),
    'path' => env('STORAGE_PATH', 'storage/uploads'),
];
