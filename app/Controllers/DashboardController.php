<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\News;
use App\Models\Series;
use App\Models\User;
use App\Models\Category;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $news = News::latestPublished(5);
        $favoriteSeries = [];
        $mostReadSeries = [];
        $recentUsers = [];
        $categoryColors = [];
        $categoryColorsByName = [];
        $isAdmin = !empty($user) && in_array($user['role'] ?? 'none', ['superadmin', 'admin', 'moderator'], true);

        if (!empty($user['id'])) {
            $favoriteSeries = Series::favoritesForUser((int)$user['id'], 8);
        }

        $mostReadSeries = Series::mostRead(5);

        if ($isAdmin) {
            $recentUsers = User::recentLogins(10);
        }

        $normalizeName = static function (string $name): string {
            $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', $name);
            $base = $ascii !== false && $ascii !== '' ? $ascii : $name;
            return mb_strtolower($base);
        };
        foreach (Category::all() as $cat) {
            $id = (int)($cat['id'] ?? 0);
            $name = (string)($cat['name'] ?? '');
            $color = (string)($cat['tag_color'] ?? '');
            $categoryColors[$id] = $color;
            if ($name !== '') {
                $categoryColorsByName[$normalizeName($name)] = $color;
            }
        }

        if (!empty($mostReadSeries)) {
            foreach ($mostReadSeries as $idx => $row) {
                $color = (string)($row['category_tag_color'] ?? '');
                $catId = (int)($row['category_id'] ?? 0);
                if ($catId > 0 && !empty($categoryColors[$catId])) {
                    $color = (string)$categoryColors[$catId];
                }
                if ($color === '') {
                    $catName = (string)($row['category_name'] ?? '');
                    if ($catName !== '') {
                        $color = (string)($categoryColorsByName[$normalizeName($catName)] ?? '');
                    }
                }
                if ($color === '' && $catId > 0) {
                    $cat = Category::findById($catId);
                    $color = (string)($cat['tag_color'] ?? '');
                }
                if ($color === '') {
                    $color = '#6c757d';
                }
                $mostReadSeries[$idx]['resolved_tag_color'] = $color;
            }
        }

        $this->writeTagCss();

        echo $this->view('dashboard/index', [
            'user' => $user,
            'news' => $news,
            'favoriteSeries' => $favoriteSeries,
            'mostReadSeries' => $mostReadSeries,
            'recentUsers' => $recentUsers,
            'isAdmin' => $isAdmin,
            'categoryColors' => $categoryColors,
            'categoryColorsByName' => $categoryColorsByName,
        ]);
    }

    private function writeTagCss(): void
    {
        $target = dirname(__DIR__, 2) . '/public/assets/category-tags.css';
        $lines = ["/* category tag colors - generated */"];
        foreach (Category::all() as $cat) {
            $id = (int)($cat['id'] ?? 0);
            $color = (string)($cat['tag_color'] ?? '');
            if ($id <= 0 || $color === '') {
                continue;
            }
            $safe = preg_replace('/[^a-fA-F0-9]/', '', $color);
            if ($safe === '' || !in_array(strlen($safe), [3, 6], true)) {
                continue;
            }
            $lines[] = ".cat-badge-{$id} { background-color: #{$safe} !important; color: #fff; }";
        }
        @file_put_contents($target, implode("\n", $lines) . "\n");
    }
}
