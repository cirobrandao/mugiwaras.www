<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\News;
use App\Models\Series;
use App\Models\User;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Package;
use App\Models\ContentItem;
use App\Models\Notification;

final class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $newsBelowMostRead = News::latestPublishedBelowMostRead(5);
        $favoriteSeries = [];
        $mostReadSeries = [];
        $recentUsers = [];
        $categoryColors = [];
        $categoryColorsByName = [];
        $isAdmin = Auth::isAdmin($user);
        $recentContent = [];
        $accessInfo = [
            'label' => 'Sem acesso ativo',
            'expires_at' => null,
        ];
        $activePackageTitle = null;
        $notifications = Notification::activeForUsers(5);

        if (!empty($user['id'])) {
            $favoriteSeries = Series::favoritesForUser((int)$user['id'], 8);
        }

        $mostReadSeries = Series::mostRead(10);

        if ($isAdmin) {
            $recentUsers = User::recentLogins(10);
        }

        if (!empty($user)) {
            if (($user['access_tier'] ?? '') === 'vitalicio') {
                $accessInfo['label'] = 'Acesso vitalício';
            } elseif (!empty($user['subscription_expires_at'])) {
                $expires = strtotime((string)$user['subscription_expires_at']);
                if ($expires !== false && $expires >= time()) {
                    $days = (int)ceil(($expires - time()) / 86400);
                    $accessInfo['label'] = 'Acesso ativo: ' . $days . ' dia' . ($days === 1 ? '' : 's');
                    $accessInfo['expires_at'] = (string)$user['subscription_expires_at'];
                } else {
                    $accessInfo['label'] = 'Acesso expirado';
                }
            }
        }

        if (!empty($user['id'])) {
            $lastApproved = Payment::latestApprovedByUser((int)$user['id']);
            if ($lastApproved) {
                $pkg = Package::find((int)($lastApproved['package_id'] ?? 0));
                if ($pkg) {
                    $activePackageTitle = (string)($pkg['title'] ?? null);
                }
            }
        }

        $recentContent = ContentItem::latestSeriesWithContent(8);
        $lastLogin = $user['data_ultimo_login'] ?? $user['data_registro'] ?? null;
        $lastLoginTs = is_string($lastLogin) ? strtotime($lastLogin) : null;
        if (!empty($recentContent) && $lastLoginTs) {
            foreach ($recentContent as $idx => $row) {
                $createdAt = $row['created_at'] ?? null;
                $createdTs = is_string($createdAt) ? strtotime($createdAt) : null;
                $recentContent[$idx]['is_new'] = $createdTs !== false && $createdTs !== null && $createdTs > $lastLoginTs;
            }
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
            'newsBelowMostRead' => $newsBelowMostRead,
            'favoriteSeries' => $favoriteSeries,
            'mostReadSeries' => $mostReadSeries,
            'recentUsers' => $recentUsers,
            'recentContent' => $recentContent,
            'notifications' => $notifications,
            'accessInfo' => $accessInfo,
            'activePackageTitle' => $activePackageTitle,
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
