<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;
use App\Models\News;
use App\Models\Series;
use App\Models\ContentItem;
use App\Models\Payment;
use App\Models\Package;
use App\Models\Category;
use App\Models\Notification;

final class NewsController extends Controller
{
    public function show(\App\Core\Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }

        $newsId = (int)$id;
        $newsItem = $newsId > 0 ? News::findPublished($newsId) : null;
        if (!$newsItem) {
            Response::abort404('Noticia nao encontrada.');
        }

        $accessInfo = [
            'label' => 'Sem acesso ativo',
            'expires_at' => null,
        ];
        $activePackageTitle = null;
        $notifications = Notification::activeForUsers(5);

        if (!empty($user)) {
            if (($user['access_tier'] ?? '') === 'vitalicio') {
                $accessInfo['label'] = 'Acesso vitalicio';
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

        $mostReadSeries = Series::mostRead(5);
        if (!empty($mostReadSeries)) {
            foreach ($mostReadSeries as $idx => $row) {
                $color = (string)($row['category_tag_color'] ?? '');
                $catId = (int)($row['category_id'] ?? 0);
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

        $recentContent = ContentItem::latestSeriesWithContent(5);
        $recentNews = News::latestPublished(5);

        echo $this->view('news/show', [
            'newsItem' => $newsItem,
            'viewer' => $user,
            'accessInfo' => $accessInfo,
            'activePackageTitle' => $activePackageTitle,
            'notifications' => $notifications,
            'mostReadSeries' => $mostReadSeries,
            'recentContent' => $recentContent,
            'recentNews' => $recentNews,
        ]);
    }
}
