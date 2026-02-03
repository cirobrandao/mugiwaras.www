<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

final class AssetsController extends Controller
{
    public function categoryTagsCss(): void
    {
        header('Content-Type: text/css; charset=utf-8');
        $lines = [];
        foreach (Category::all() as $cat) {
            $id = (int)($cat['id'] ?? 0);
            $color = (string)($cat['tag_color'] ?? '');
            if ($id > 0 && $color !== '') {
                $safe = preg_replace('/[^#a-fA-F0-9]/', '', $color);
                if ($safe === '') {
                    continue;
                }
                $lines[] = ".cat-badge-{$id} { background-color: {$safe} !important; color: #fff; }";
            }
        }
        echo implode("\n", $lines);
    }
}
