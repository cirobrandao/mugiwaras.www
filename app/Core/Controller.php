<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function view(string $name, array $data = []): string
    {
        return View::render($name, $data);
    }
}
