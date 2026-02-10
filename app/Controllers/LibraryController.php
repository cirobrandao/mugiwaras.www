<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Response;
use App\Models\Category;
use App\Models\Series;
use App\Models\ContentItem;
use App\Models\Upload;
use App\Models\UserFavorite;
use App\Models\UserContentStatus;
use App\Models\UserSeriesFavorite;
use App\Models\Payment;
use App\Models\Package;
use App\Core\Csrf;
use App\Core\Audit;
use App\Models\SearchLog;
use App\Models\SeriesSearchLog;

final class LibraryController extends Controller
{
    public function index(\App\Core\Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if (!Category::isReady()) {
            echo $this->view('libraries/index', ['error' => 'Biblioteca ainda não inicializada. Execute a migração 009_library_series.sql.']);
            return;
        }
        $categories = Category::all();
        $isVitalicio = ($user['access_tier'] ?? '') === 'vitalicio';
        $restrictedIds = [4, 5, 6];
        $allowedIds = $isVitalicio ? [] : $this->allowedCategoryIds($user);
        $allowedSet = array_flip($allowedIds);
        $isStaff = \App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user);
        if (!$isStaff && !$isVitalicio) {
            $categories = array_values(array_filter($categories, function ($c) use ($allowedSet, $restrictedIds) {
                $cid = (int)($c['id'] ?? 0);
                $requires = !empty($c['requires_subscription']);
                $isRestricted = in_array($cid, $restrictedIds, true);
                if ($requires || $isRestricted) {
                    return !empty($allowedSet) && isset($allowedSet[$cid]);
                }
                return true;
            }));
        }
        $categories = array_values(array_filter($categories, fn ($c) => !empty($c['content_cbz']) || !empty($c['content_pdf']) || !empty($c['content_epub']) || !empty($c['content_video']) || !empty($c['content_download'])));
        $iosTest = isset($request->get['ios_test']) && $request->get['ios_test'] === '1' && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user));

        echo $this->view('libraries/index', [
            'categories' => $categories,
            'q' => '',
            'iosTest' => $iosTest,
        ]);
    }

    public function search(\App\Core\Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if (!Category::isReady()) {
            echo $this->view('libraries/search', ['error' => 'Biblioteca ainda não inicializada.']);
            return;
        }
        $q = $this->sanitizeSearchTerm((string)($request->get['q'] ?? ''));
        $seriesResults = [];
        if ($q !== '') {
            $minChapters = (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user)) ? 0 : 1;
            $seriesResults = Series::searchByNameWithCounts($q, 60, $minChapters);
            $categories = Category::all();
            $categoryMap = [];
            foreach ($categories as $c) {
                $categoryMap[(int)$c['id']] = $c;
            }
            $isVitalicio = ($user['access_tier'] ?? '') === 'vitalicio';
            $restrictedIds = [4, 5, 6];
            $allowedIds = $isVitalicio ? [] : $this->allowedCategoryIds($user);
            $allowedSet = array_flip($allowedIds);
            $isStaff = \App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user);
            $seriesResults = array_values(array_filter($seriesResults, function ($s) use ($categoryMap, $isVitalicio, $isStaff, $allowedSet, $restrictedIds) {
                $cid = (int)($s['category_id'] ?? 0);
                $cat = $categoryMap[$cid] ?? null;
                if (!$cat) {
                    return false;
                }
                if (!$isStaff && !$isVitalicio) {
                    $requires = !empty($cat['requires_subscription']);
                    $isRestricted = in_array($cid, $restrictedIds, true);
                    if ($requires || $isRestricted) {
                        if (empty($allowedSet) || !isset($allowedSet[$cid])) {
                            return false;
                        }
                    }
                }
                $allowCbz = !empty($cat['content_cbz']);
                $allowPdf = !empty($cat['content_pdf']);
                $allowEpub = !empty($cat['content_epub']);
                $allowVideo = !empty($cat['content_video']);
                $allowDownload = !empty($cat['content_download']);
                return $allowCbz || $allowPdf || $allowEpub || $allowVideo || $allowDownload;
            }));
            SearchLog::create([
                'uid' => (int)$user['id'],
                'term' => $q,
                'cnt' => count($seriesResults),
                'ip' => $request->ip(),
            ]);
            $seriesIds = array_map(fn ($s) => (int)($s['id'] ?? 0), array_slice($seriesResults, 0, 20));
            SeriesSearchLog::createMany((int)$user['id'], $q, $seriesIds);
        }

        echo $this->view('libraries/search', [
            'q' => $q,
            'seriesResults' => $seriesResults,
        ]);
    }

    private function sanitizeSearchTerm(string $term): string
    {
        $term = trim(strip_tags($term));
        $term = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $term) ?? '';
        $term = trim(preg_replace('/\s+/u', ' ', $term) ?? '');
        if (mb_strlen($term) > 120) {
            $term = mb_substr($term, 0, 120);
        }
        return $term;
    }

    private function subscriptionActive(array $user): bool
    {
        if (($user['access_tier'] ?? '') === 'vitalicio') {
            return true;
        }
        if (!empty($user['subscription_expires_at'])) {
            $expires = strtotime((string)$user['subscription_expires_at']);
            if ($expires !== false && $expires > time()) {
                return true;
            }
        }
        return false;
    }

    private function allowedCategoryIds(array $user): array
    {
        if (($user['access_tier'] ?? '') === 'vitalicio') {
            return [];
        }
        if (!$this->subscriptionActive($user)) {
            return [];
        }
        $payment = Payment::latestApprovedByUser((int)($user['id'] ?? 0));
        if (!$payment) {
            return [];
        }
        $packageId = (int)($payment['package_id'] ?? 0);
        if ($packageId <= 0) {
            return [];
        }
        $map = Package::categoriesMap([$packageId]);
        return array_values(array_map('intval', $map[$packageId] ?? []));
    }

    public function category(\App\Core\Request $request, string $category): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $name = rawurldecode($category);
        if (!Category::isReady()) {
            http_response_code(500);
            echo $this->view('libraries/category', ['error' => 'Biblioteca ainda não inicializada.', 'category' => ['name' => '']]);
            return;
        }
        $cat = Category::findByName($name);
        if (!$cat) {
            http_response_code(404);
            echo $this->view('libraries/category', ['error' => 'Categoria não encontrada.', 'category' => ['name' => '']]);
            return;
        }
        $isVitalicio = ($user['access_tier'] ?? '') === 'vitalicio';
        $restrictedIds = [4, 5, 6];
        $allowedIds = $isVitalicio ? [] : $this->allowedCategoryIds($user);
        $allowedSet = array_flip($allowedIds);
        $isStaff = \App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user);
        if (!$isStaff && !$isVitalicio) {
            $requires = !empty($cat['requires_subscription']);
            $isRestricted = in_array((int)($cat['id'] ?? 0), $restrictedIds, true);
            if ($requires || $isRestricted) {
                if (empty($allowedSet) || !isset($allowedSet[(int)($cat['id'] ?? 0)])) {
                    Response::redirect(base_path('/loja'));
                }
            }
        }
        $seriesAll = Series::byCategoryWithCountsAndTypes((int)$cat['id']);
        $isAdultUser = $this->isAdultUser($user);
        if (!$isStaff && !$isAdultUser) {
            $seriesAll = array_values(array_filter($seriesAll, static fn ($s) => empty($s['adult_only'])));
        }
        $query = trim((string)($request->get['q'] ?? ''));
        if ($query !== '') {
            $seriesAll = array_values(array_filter($seriesAll, fn ($s) => mb_stripos((string)($s['name'] ?? ''), $query) !== false));
        }
        $allowCbz = !empty($cat['content_cbz']);
        $allowPdf = !empty($cat['content_pdf']);
        $allowEpub = !empty($cat['content_epub']);
        if (!$allowCbz || !$allowPdf || !$allowEpub) {
            $seriesAll = array_values(array_map(static function ($s) use ($allowCbz, $allowPdf, $allowEpub) {
                if (!$allowCbz) {
                    $s['cbz_count'] = 0;
                }
                if (!$allowPdf) {
                    $s['pdf_count'] = 0;
                }
                if (!$allowEpub) {
                    $s['epub_count'] = 0;
                }
                $s['chapter_count'] = (int)($s['cbz_count'] ?? 0) + (int)($s['pdf_count'] ?? 0) + (int)($s['epub_count'] ?? 0);
                return $s;
            }, $seriesAll));
        }
        if (!$isStaff) {
            $seriesAll = array_values(array_filter($seriesAll, fn ($s) => (int)($s['chapter_count'] ?? 0) > 0));
        }
        usort($seriesAll, static function ($a, $b): int {
            $pa = (int)($a['pin_order'] ?? 0);
            $pb = (int)($b['pin_order'] ?? 0);
            if ($pa === $pb) {
                return strcasecmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
            }
            return $pb <=> $pa;
        });
        $seriesIds = array_map(fn ($s) => (int)$s['id'], $seriesAll);
        $favoriteSeriesIds = UserSeriesFavorite::getIdsForUser((int)$user['id'], $seriesIds);
        $pendingCounts = $isStaff && !empty($seriesIds)
            ? Upload::pendingCountsBySeries($seriesIds)
            : [];
        $iosTest = isset($request->get['ios_test']) && $request->get['ios_test'] === '1' && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user));

        echo $this->view('libraries/category', [
            'category' => $cat,
            'series' => $seriesAll,
            'favoriteSeries' => $favoriteSeriesIds,
            'pendingCounts' => $pendingCounts,
            'csrf' => Csrf::token(),
            'user' => $user,
            'q' => $query,
            'iosTest' => $iosTest,
        ]);
    }

    public function series(\App\Core\Request $request, string $category, string $series): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $categoryName = rawurldecode($category);
        $seriesName = rawurldecode($series);
        if (!Category::isReady()) {
            http_response_code(500);
            echo $this->view('libraries/series', ['error' => 'Biblioteca ainda não inicializada.']);
            return;
        }
        $cat = Category::findByName($categoryName);
        if (!$cat) {
            http_response_code(404);
            echo $this->view('libraries/series', ['error' => 'Categoria não encontrada.']);
            return;
        }
        $isVitalicio = ($user['access_tier'] ?? '') === 'vitalicio';
        $restrictedIds = [4, 5, 6];
        $allowedIds = $isVitalicio ? [] : $this->allowedCategoryIds($user);
        $allowedSet = array_flip($allowedIds);
        $isStaff = \App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user);
        if (!$isStaff && !$isVitalicio) {
            $requires = !empty($cat['requires_subscription']);
            $isRestricted = in_array((int)($cat['id'] ?? 0), $restrictedIds, true);
            if ($requires || $isRestricted) {
                if (empty($allowedSet) || !isset($allowedSet[(int)($cat['id'] ?? 0)])) {
                    Response::redirect(base_path('/loja'));
                }
            }
        }
        $ser = Series::findByName((int)$cat['id'], $seriesName);
        if (!$ser) {
            http_response_code(404);
            echo $this->view('libraries/series', ['error' => 'Série não encontrada.']);
            return;
        }
        $isAdultUser = $this->isAdultUser($user);
        if (!$isStaff && !$isAdultUser && (!empty($cat['adult_only']) || !empty($ser['adult_only']))) {
            http_response_code(403);
            echo $this->view('libraries/series', ['error' => 'Conteúdo 18+ disponível apenas para maiores.']);
            return;
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $format = strtolower((string)($request->get['format'] ?? ''));
        if (!in_array($format, ['pdf', 'cbz', 'epub'], true)) {
            $format = '';
        }
        $allowCbz = !empty($cat['content_cbz']);
        $allowPdf = !empty($cat['content_pdf']);
        $allowEpub = !empty($cat['content_epub']);
        if ($format === 'pdf' && !$allowPdf) {
            $format = '';
        }
        if ($format === 'cbz' && !$allowCbz) {
            $format = '';
        }
        if ($format === 'epub' && !$allowEpub) {
            $format = '';
        }
        if ($format === '' && $allowCbz && !$allowPdf && !$allowEpub) {
            $format = 'cbz';
        }
        if ($format === '' && !$allowCbz && $allowPdf && !$allowEpub) {
            $format = 'pdf';
        }
        if ($format === '' && !$allowCbz && !$allowPdf && $allowEpub) {
            $format = 'epub';
        }
        $order = strtolower((string)($request->get['order'] ?? 'desc'));
        if (!in_array($order, ['asc', 'desc'], true)) {
            $order = 'desc';
        }
        $perPage = 40;
        $allowedTypes = [];
        if ($allowCbz) {
            $allowedTypes[] = 'cbz';
        }
        if ($allowPdf) {
            $allowedTypes[] = 'pdf';
        }
        if ($allowEpub) {
            $allowedTypes[] = 'epub';
        }
        if (empty($allowedTypes)) {
            $total = 0;
        } else {
            $total = $format !== ''
                ? ContentItem::countBySeriesAndFormat((int)$ser['id'], $format)
                : ContentItem::countBySeriesAndTypes((int)$ser['id'], $allowedTypes);
        }
        $bulkTotal = !empty($allowedTypes)
            ? ContentItem::countBySeriesAndTypes((int)$ser['id'], $allowedTypes)
            : 0;
        $readCount = 0;
        if (!empty($allowedTypes)) {
            $readCount = UserContentStatus::countReadForSeriesAndTypes((int)$user['id'], (int)$ser['id'], $allowedTypes);
        }
        $seriesReadAll = $bulkTotal > 0 && $readCount >= $bulkTotal;
        $offset = ($page - 1) * $perPage;
        if (empty($allowedTypes)) {
            $items = [];
        } else {
            $items = $format !== ''
                ? ContentItem::bySeriesAndFormat((int)$ser['id'], $format, $order, $perPage, $offset)
                : ContentItem::bySeriesAndTypes((int)$ser['id'], $allowedTypes, $order, $perPage, $offset);
        }
        $cbzTitles = [];
        foreach ($items as $item) {
            $path = (string)($item['cbz_path'] ?? '');
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext !== 'pdf' && $ext !== 'epub') {
                $cbzTitles[(string)($item['title'] ?? '')] = true;
            }
        }
        if (!empty($cbzTitles)) {
            $items = array_values(array_filter($items, function (array $item) use ($cbzTitles): bool {
                $path = (string)($item['cbz_path'] ?? '');
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    return true;
                }
                $title = (string)($item['title'] ?? '');
                return $title === '' || !isset($cbzTitles[$title]);
            }));
        }
        $pending = Upload::pendingBySeries((int)$ser['id']);

        $contentIds = array_map(fn ($i) => (int)$i['id'], $items);
        $favoriteIds = UserFavorite::getIdsForUser((int)$user['id'], $contentIds);
        $readIds = UserContentStatus::getReadIdsForUser((int)$user['id'], $contentIds);
        $progressMap = UserContentStatus::getProgressForUser((int)$user['id'], $contentIds);
        $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
        $isIos = false;
        $iosTest = isset($request->get['ios_test']) && $request->get['ios_test'] === '1' && (\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user));
        if ($iosTest) {
            $isIos = true;
        }
        $downloadTokens = [];
        foreach ($contentIds as $contentId) {
            $downloadTokens[$contentId] = $this->downloadToken((int)$user['id'], (int)$contentId);
        }
        $pdfDownloadUrls = [];
        foreach ($items as $item) {
            $itemId = (int)($item['id'] ?? 0);
            $path = (string)($item['cbz_path'] ?? '');
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($ext === 'pdf' || $ext === 'epub') {
                continue;
            }
            $token = $downloadTokens[$itemId] ?? '';
            if ($token === '') {
                continue;
            }
            $pdfPath = $this->resolvePdfForContent($item, (string)($ser['name'] ?? ''));
            if ($pdfPath !== null && file_exists($pdfPath)) {
                $pdfDownloadUrls[$itemId] = base_path('/download-pdf/' . $itemId . '?token=' . urlencode($token));
            }
        }

        echo $this->view('libraries/series', [
            'category' => $cat,
            'series' => $ser,
            'items' => $items,
            'pending' => $pending,
            'favorites' => $favoriteIds,
            'read' => $readIds,
            'progress' => $progressMap,
            'csrf' => Csrf::token(),
            'user' => $user,
            'page' => $page,
            'pages' => (int)ceil($total / $perPage),
            'isIos' => $isIos,
            'downloadTokens' => $downloadTokens,
            'pdfDownloadUrls' => $pdfDownloadUrls,
            'iosTest' => $iosTest,
            'format' => $format,
            'order' => $order,
            'seriesReadAll' => $seriesReadAll,
        ]);
    }

    private function resolvePdfForContent(array $content, string $seriesName): ?string
    {
        $cbzPath = (string)($content['cbz_path'] ?? '');
        if ($cbzPath === '') {
            return null;
        }
        $abs = $this->resolveContentPath($cbzPath);
        if ($abs === null) {
            return null;
        }
        $chapterName = (string)($content['title'] ?? '');
        $siteName = (string)config('app.name', 'Site');
        $base = trim($seriesName) !== '' ? $seriesName : 'Serie';
        $chapter = trim($chapterName) !== '' ? $chapterName : 'Capitulo';
        $filename = $this->sanitizeDownloadFilename($base . ' - ' . $chapter . ' [' . $siteName . '].pdf');
        return rtrim(dirname($abs), '/') . '/' . $filename;
    }

    private function resolveContentPath(string $relative): ?string
    {
        $clean = str_replace(['..', '\\'], ['', '/'], $relative);
        $storageRoot = dirname(__DIR__, 2) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $storageFull = rtrim($storageRoot, '/') . '/' . ltrim($clean, '/');
        $storageReal = realpath($storageFull);
        $storageRootReal = realpath($storageRoot);
        if ($storageReal && $storageRootReal && str_starts_with($storageReal, $storageRootReal)) {
            return $storageReal;
        }

        $libraryRoot = dirname(__DIR__, 2) . '/' . trim((string)config('library.path', 'storage/library'), '/');
        $libraryFull = rtrim($libraryRoot, '/') . '/' . ltrim($clean, '/');
        $libraryReal = realpath($libraryFull);
        $libraryRootReal = realpath($libraryRoot);
        if ($libraryReal && $libraryRootReal && str_starts_with($libraryReal, $libraryRootReal)) {
            return $libraryReal;
        }
        return null;
    }

    private function sanitizeDownloadFilename(string $name): string
    {
        $clean = preg_replace('/[\x00-\x1F\x7F"\\\\\/<>:\\|?*]+/', ' ', $name) ?? $name;
        $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;
        $clean = trim($clean);
        if ($clean === '' || $clean === '.pdf') {
            return 'arquivo.pdf';
        }
        return $clean;
    }

    private function downloadToken(int $userId, int $contentId): string
    {
        $secret = (string)config('security.download_secret', '');
        if ($secret === '') {
            return '';
        }
        $ts = (string)time();
        $sig = hash_hmac('sha256', $userId . ':' . $contentId . ':' . $ts, $secret);
        return $ts . '.' . $sig;
    }

    public function updateContent(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $title = trim((string)($request->post['title'] ?? ''));
        if ($id <= 0 || $title === '') {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        ContentItem::updateTitle($id, $title);
        Audit::log('content_rename', (int)$user['id'], ['content_id' => $id]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function updateContentOrder(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $order = (int)($request->post['content_order'] ?? 0);
        if ($id <= 0) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        ContentItem::updateOrder($id, $order);
        Audit::log('content_order', (int)$user['id'], ['content_id' => $id, 'order' => $order]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function pinSeries(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $pinOrder = (int)($request->post['pin_order'] ?? 0);
        if ($id <= 0) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        Series::updatePinOrder($id, $pinOrder);
        Audit::log('series_pin', (int)$user['id'], ['series_id' => $id, 'order' => $pinOrder]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function deleteContent(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $item = ContentItem::find($id);
        if (!$item) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $abs = $this->resolveLibraryPath((string)$item['cbz_path']);
        if ($abs && file_exists($abs)) {
            @unlink($abs);
        }
        ContentItem::delete($id);
        Audit::log('content_delete', (int)$user['id'], ['content_id' => $id]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function toggleFavorite(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $action = (string)($request->post['action'] ?? 'add');
        if ($id > 0) {
            if ($action === 'remove') {
                UserFavorite::remove((int)$user['id'], $id);
            } else {
                UserFavorite::add((int)$user['id'], $id);
            }
        }
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function toggleRead(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $read = (string)($request->post['read'] ?? '1') === '1';
        if ($id > 0) {
            UserContentStatus::setRead((int)$user['id'], $id, $read);
        }
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function markSeriesRead(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/libraries'));
        }
        $seriesId = (int)($request->post['series_id'] ?? 0);
        if ($seriesId <= 0) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $ser = Series::findById($seriesId);
        if (!$ser) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $cat = Category::findById((int)($ser['category_id'] ?? 0));
        if (!$cat) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $isVitalicio = ($user['access_tier'] ?? '') === 'vitalicio';
        $restrictedIds = [4, 5, 6];
        $allowedIds = $isVitalicio ? [] : $this->allowedCategoryIds($user);
        $allowedSet = array_flip($allowedIds);
        $isStaff = \App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user);
        if (!$isStaff && !$isVitalicio) {
            $requires = !empty($cat['requires_subscription']);
            $isRestricted = in_array((int)($cat['id'] ?? 0), $restrictedIds, true);
            if ($requires || $isRestricted) {
                if (empty($allowedSet) || !isset($allowedSet[(int)($cat['id'] ?? 0)])) {
                    Response::redirect(base_path('/loja'));
                }
            }
        }
        $isAdultUser = $this->isAdultUser($user);
        if (!$isStaff && !$isAdultUser && (!empty($cat['adult_only']) || !empty($ser['adult_only']))) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $action = (string)($request->post['action'] ?? 'read');
        $scope = (string)($request->post['scope'] ?? 'all');
        $maxOrder = (int)($request->post['episode_order'] ?? 0);
        $allowCbz = !empty($cat['content_cbz']);
        $allowPdf = !empty($cat['content_pdf']);
        $allowEpub = !empty($cat['content_epub']);
        $types = [];
        if ($allowCbz) {
            $types[] = 'cbz';
        }
        if ($allowPdf) {
            $types[] = 'pdf';
        }
        if ($allowEpub) {
            $types[] = 'epub';
        }
        if (!empty($types)) {
            $applyOrder = ($scope === 'upto' && $maxOrder > 0) ? $maxOrder : null;
            if ($action === 'unread') {
                UserContentStatus::setUnreadForSeriesAndTypes((int)$user['id'], $seriesId, $types, $applyOrder);
            } else {
                UserContentStatus::setReadForSeriesAndTypes((int)$user['id'], $seriesId, $types, $applyOrder);
            }
        }
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function markSeriesUnread(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/libraries'));
        }
        $seriesId = (int)($request->post['series_id'] ?? 0);
        if ($seriesId <= 0) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $ser = Series::findById($seriesId);
        if (!$ser) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $cat = Category::findById((int)($ser['category_id'] ?? 0));
        if (!$cat) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $isVitalicio = ($user['access_tier'] ?? '') === 'vitalicio';
        $restrictedIds = [4, 5, 6];
        $allowedIds = $isVitalicio ? [] : $this->allowedCategoryIds($user);
        $allowedSet = array_flip($allowedIds);
        $isStaff = \App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user) || \App\Core\Auth::isEquipe($user);
        if (!$isStaff && !$isVitalicio) {
            $requires = !empty($cat['requires_subscription']);
            $isRestricted = in_array((int)($cat['id'] ?? 0), $restrictedIds, true);
            if ($requires || $isRestricted) {
                if (empty($allowedSet) || !isset($allowedSet[(int)($cat['id'] ?? 0)])) {
                    Response::redirect(base_path('/loja'));
                }
            }
        }
        $isAdultUser = $this->isAdultUser($user);
        if (!$isStaff && !$isAdultUser && (!empty($cat['adult_only']) || !empty($ser['adult_only']))) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $scope = (string)($request->post['scope'] ?? 'all');
        $maxOrder = (int)($request->post['episode_order'] ?? 0);
        $allowCbz = !empty($cat['content_cbz']);
        $allowPdf = !empty($cat['content_pdf']);
        $allowEpub = !empty($cat['content_epub']);
        $types = [];
        if ($allowCbz) {
            $types[] = 'cbz';
        }
        if ($allowPdf) {
            $types[] = 'pdf';
        }
        if ($allowEpub) {
            $types[] = 'epub';
        }
        if (!empty($types)) {
            $applyOrder = ($scope === 'upto' && $maxOrder > 0) ? $maxOrder : null;
            UserContentStatus::setUnreadForSeriesAndTypes((int)$user['id'], $seriesId, $types, $applyOrder);
        }
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function updateProgress(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::json(['error' => 'csrf'], 422);
        }
        $user = Auth::user();
        if (!$user) {
            Response::json(['error' => 'auth'], 401);
        }
        $id = (int)($request->post['id'] ?? 0);
        $page = (int)($request->post['page'] ?? 0);
        if ($id > 0) {
            UserContentStatus::setLastPage((int)$user['id'], $id, $page);
        }
        Response::json(['ok' => true]);
    }

    public function toggleSeriesFavorite(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $action = (string)($request->post['action'] ?? 'add');
        if ($id > 0) {
            if ($action === 'remove') {
                UserSeriesFavorite::remove((int)$user['id'], $id);
            } else {
                UserSeriesFavorite::add((int)$user['id'], $id);
            }
        }
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function updateSeries(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $name = trim((string)($request->post['name'] ?? ''));
        if ($id <= 0 || $name === '') {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        Series::rename($id, $name);
        Audit::log('series_rename', (int)$user['id'], ['series_id' => $id]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function updateSeriesAdult(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        $adultOnly = (int)($request->post['adult_only'] ?? 0) > 0 ? 1 : 0;
        if ($id <= 0) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        Series::updateAdultOnly($id, $adultOnly);
        Audit::log('series_adult', (int)$user['id'], ['series_id' => $id, 'adult_only' => $adultOnly]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    public function deleteSeries(\App\Core\Request $request): void
    {
        if (!Csrf::verify($request->post['_csrf'] ?? null)) {
            Response::redirect(base_path('/libraries'));
        }
        $user = Auth::user();
        if (!$user || !(\App\Core\Auth::isAdmin($user) || \App\Core\Auth::isModerator($user))) {
            Response::redirect(base_path('/libraries'));
        }
        $id = (int)($request->post['id'] ?? 0);
        if ($id <= 0) {
            Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
        }
        $this->deleteSeriesCascade($id);
        Audit::log('series_delete', (int)$user['id'], ['series_id' => $id]);
        Response::redirect($request->server['HTTP_REFERER'] ?? base_path('/libraries'));
    }

    private function resolveLibraryPath(string $relative): ?string
    {
        $root = dirname(__DIR__, 2) . '/' . trim((string)config('library.path', 'storage/library'), '/');
        $clean = str_replace(['..', '\\'], ['', '/'], $relative);
        $full = rtrim($root, '/') . '/' . ltrim($clean, '/');
        $real = realpath($full);
        if (!$real) {
            return null;
        }
        $rootReal = realpath($root);
        if ($rootReal && str_starts_with($real, $rootReal)) {
            return $real;
        }
        return null;
    }

    private function isAdultUser(array $user): bool
    {
        $birth = trim((string)($user['birth_date'] ?? ''));
        if ($birth === '') {
            return false;
        }
        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d', 'Y/m/d'];
        $dt = null;
        foreach ($formats as $fmt) {
            $tmp = \DateTimeImmutable::createFromFormat($fmt, $birth);
            if ($tmp !== false && $tmp->format($fmt) === $birth) {
                $dt = $tmp;
                break;
            }
        }
        if (!$dt) {
            try {
                $dt = new \DateTimeImmutable($birth);
            } catch (\Throwable $e) {
                return false;
            }
        }
        $now = new \DateTimeImmutable('now');
        if ($dt > $now) {
            return false;
        }
        $age = $now->diff($dt)->y;
        return $age >= 18;
    }

    private function deleteSeriesCascade(int $seriesId): void
    {
        $db = \App\Core\Database::connection();
        $items = $db->prepare('SELECT id, cbz_path FROM content_items WHERE series_id = :s');
        $items->execute(['s' => $seriesId]);
        $rows = $items->fetchAll();
        foreach ($rows as $row) {
            $abs = $this->resolveLibraryPath((string)$row['cbz_path']);
            if ($abs && file_exists($abs)) {
                @unlink($abs);
            }
            ContentItem::delete((int)$row['id']);
        }
        $db->prepare('DELETE FROM uploads WHERE series_id = :s')->execute(['s' => $seriesId]);
        $db->prepare('DELETE FROM series WHERE id = :s')->execute(['s' => $seriesId]);
    }
}
