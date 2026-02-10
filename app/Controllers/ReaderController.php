<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Audit;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Logger;
use App\Models\ContentItem;
use App\Models\ContentEvent;
use App\Models\Setting;
use App\Models\Category;
use App\Models\Series;
use App\Models\UserFavorite;
use App\Models\UserContentStatus;
use App\Models\Payment;
use App\Models\Package;
use App\Core\Csrf;

final class ReaderController extends Controller
{
    public function open(Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            echo $this->view('reader/open', ['error' => $msg]);
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            echo 'Conteúdo não encontrado.';
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            echo $this->view('reader/open', ['error' => 'Acesso indisponível para esta categoria.']);
            return;
        }
        $path = (string)($content['cbz_path'] ?? '');
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            echo $this->view('reader/open', ['error' => 'Leitor indisponível para PDF. Faça o download.']);
            return;
        }
        if ($ext === 'epub') {
            echo $this->view('reader/open', ['error' => 'Leitor de ePub em desenvolvimento. Faça o download.']);
            return;
        }

        $cbzMode = 'page';
        $cbzDirection = 'rtl';
        if (!empty($content['category_id'])) {
            $cat = Category::findByName((string)($content['category_name'] ?? ''));
            if (!$cat) {
                $catStmt = \App\Core\Database::connection()->prepare('SELECT * FROM categories WHERE id = :id');
                $catStmt->execute(['id' => (int)$content['category_id']]);
                $cat = $catStmt->fetch();
            }
            if ($cat) {
                $content['category_name'] = $cat['name'] ?? null;
                $cbzMode = (string)($cat['cbz_mode'] ?? 'page');
                $cbzDirection = (string)($cat['cbz_direction'] ?? 'rtl');
            }
        }
        if (!empty($content['series_id'])) {
            $serStmt = \App\Core\Database::connection()->prepare('SELECT * FROM series WHERE id = :id');
            $serStmt->execute(['id' => (int)$content['series_id']]);
            $ser = $serStmt->fetch();
            if ($ser) {
                $content['series_name'] = $ser['name'] ?? null;
            }
        }

        $limitReads = (int)Setting::get('trial_reads_per_day', '0');
        if ($user['access_tier'] === 'trial' && $limitReads > 0) {
            $count = ContentEvent::countToday((int)$user['id'], 'read_open');
            if ($count >= $limitReads) {
                echo $this->view('reader/open', ['error' => 'Limite diário de leitura atingido.']);
                return;
            }
        }

        ContentItem::incrementView((int)$content['id']);
        ContentEvent::log((int)$user['id'], (int)$content['id'], 'read_open', null, (new Request())->ip());
        Audit::log('read_open', (int)$user['id'], ['content_id' => (int)$content['id']]);

        [$pages, $pageError] = $this->listPagesWithError((string)$content['cbz_path']);
        // determine previous and next chapter in the same series (by id)
        $previousChapterUrl = '';
        $nextChapterUrl = '';
        if (!empty($content['series_id'])) {
            $db = \App\Core\Database::connection();
            // previous
            $pstmt = $db->prepare('SELECT id FROM content_items WHERE series_id = :s AND id < :id ORDER BY id DESC LIMIT 1');
            $pstmt->execute(['s' => (int)$content['series_id'], 'id' => (int)$content['id']]);
            $prev = $pstmt->fetch();
            if ($prev && !empty($prev['id'])) {
                $previousChapterUrl = base_path('/reader/' . (int)$prev['id']);
            }
            // next
            $nstmt = $db->prepare('SELECT id FROM content_items WHERE series_id = :s AND id > :id ORDER BY id ASC LIMIT 1');
            $nstmt->execute(['s' => (int)$content['series_id'], 'id' => (int)$content['id']]);
            $next = $nstmt->fetch();
            if ($next && !empty($next['id'])) {
                $nextChapterUrl = base_path('/reader/' . (int)$next['id']);
            }
        }
        $favIds = UserFavorite::getIdsForUser((int)$user['id'], [(int)$content['id']]);
        $progress = UserContentStatus::getProgressForUser((int)$user['id'], [(int)$content['id']]);
        $requestedPage = $request->get['page'] ?? null;
        $lastPage = (int)($progress[(int)$content['id']] ?? 0);
        if ($requestedPage !== null && is_numeric($requestedPage)) {
            $lastPage = max(0, (int)$requestedPage);
        }
        $pdfDownloadUrl = null;
        $pdfToken = $this->downloadToken((int)$user['id'], (int)$content['id']);
        $pdfPath = $this->resolvePdfPathForContent($content);
        if ($pdfToken !== '' && $pdfPath !== null && file_exists($pdfPath)) {
            $pdfDownloadUrl = base_path('/download-pdf/' . (int)$content['id'] . '?token=' . urlencode($pdfToken));
        }

        echo $this->view('reader/open', [
            'content' => $content,
            'pages' => $pages,
            'error' => $pageError,
            'downloadToken' => $this->downloadToken((int)$user['id'], (int)$content['id']),
            'pdfDownloadUrl' => $pdfDownloadUrl,
            'isFavorite' => !empty($favIds),
            'lastPage' => $lastPage,
            'nextChapterUrl' => $nextChapterUrl,
            'previousChapterUrl' => $previousChapterUrl,
            'cbzMode' => $cbzMode,
            'cbzDirection' => $cbzDirection,
            'csrf' => Csrf::token(),
        ]);
    }

    public function downloadPdf(Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $token = (string)($request->get['token'] ?? '');
        if (!$this->validateDownloadToken((int)$user['id'], (int)$id, $token)) {
            http_response_code(403);
            echo 'Token inválido.';
            return;
        }
        if ($msg = $this->accessError($user)) {
            http_response_code(403);
            echo $msg;
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            echo 'Conteúdo não encontrado.';
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            echo 'Acesso indisponível para esta categoria.';
            return;
        }

        $abs = $this->resolvePdfPathForContent($content);
        if ($abs === null || !file_exists($abs)) {
            http_response_code(404);
            echo 'Arquivo não encontrado.';
            return;
        }

        ContentItem::incrementDownload((int)$content['id']);
        ContentEvent::log((int)$user['id'], (int)$content['id'], 'download', null, (new Request())->ip());
        Audit::log('download', (int)$user['id'], ['content_id' => (int)$content['id'], 'type' => 'pdf_from_cbz']);

        $seriesName = '';
        if (!empty($content['series_id'])) {
            $series = Series::findById((int)$content['series_id']);
            $seriesName = (string)($series['name'] ?? '');
        }
        $chapterName = (string)($content['title'] ?? '');
        $siteName = (string)config('app.name', 'Site');
        $base = trim($seriesName) !== '' ? $seriesName : 'Serie';
        $chapter = trim($chapterName) !== '' ? $chapterName : 'Capitulo';
        $downloadName = $this->sanitizeDownloadFilename($base . ' - ' . $chapter . ' [' . $siteName . '].pdf');

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($abs));
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        readfile($abs);
        exit;
    }

    private function resolvePdfPathForContent(array $content): ?string
    {
        $cbzPath = (string)($content['cbz_path'] ?? '');
        if ($cbzPath === '') {
            return null;
        }
        $abs = $this->resolveCbzPath($cbzPath);
        if ($abs === null) {
            return null;
        }
        $seriesName = '';
        if (!empty($content['series_id'])) {
            $series = Series::findById((int)$content['series_id']);
            $seriesName = (string)($series['name'] ?? '');
        }
        $chapterName = (string)($content['title'] ?? '');
        $siteName = (string)config('app.name', 'Site');
        $base = trim($seriesName) !== '' ? $seriesName : 'Serie';
        $chapter = trim($chapterName) !== '' ? $chapterName : 'Capitulo';
        $filename = $this->sanitizeDownloadFilename($base . ' - ' . $chapter . ' [' . $siteName . '].pdf');
        return rtrim(dirname($abs), '/') . '/' . $filename;
    }

    public function download(Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        $token = (string)($request->get['token'] ?? '');
        if (!$this->validateDownloadToken((int)$user['id'], (int)$id, $token)) {
            http_response_code(403);
            echo 'Token inválido.';
            return;
        }
        if ($msg = $this->accessError($user)) {
            http_response_code(403);
            echo $msg;
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            echo 'Conteúdo não encontrado.';
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            echo 'Acesso indisponível para esta categoria.';
            return;
        }

        $limitDownloads = (int)Setting::get('trial_downloads_per_day', '0');
        if ($user['access_tier'] === 'trial' && $limitDownloads > 0) {
            $count = ContentEvent::countToday((int)$user['id'], 'download');
            if ($count >= $limitDownloads) {
                http_response_code(403);
                echo 'Limite diário de downloads atingido.';
                return;
            }
        }

        $abs = $this->resolveCbzPath((string)$content['cbz_path']);
        if ($abs === null || !file_exists($abs)) {
            http_response_code(404);
            echo 'Arquivo não encontrado.';
            return;
        }

        ContentItem::incrementDownload((int)$content['id']);
        ContentEvent::log((int)$user['id'], (int)$content['id'], 'download', null, (new Request())->ip());
        Audit::log('download', (int)$user['id'], ['content_id' => (int)$content['id']]);

        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        $mime = $ext === 'pdf' ? 'application/pdf' : ($ext === 'epub' ? 'application/epub+zip' : 'application/zip');
        $inline = in_array($ext, ['pdf', 'epub'], true) && (($request->get['inline'] ?? '') === '1');
        if ($inline && $ext === 'pdf') {
            ContentItem::incrementView((int)$content['id']);
            ContentEvent::log((int)$user['id'], (int)$content['id'], 'read_open', null, (new Request())->ip());
            Audit::log('read_open', (int)$user['id'], ['content_id' => (int)$content['id'], 'type' => 'pdf_inline']);
        }
        $downloadName = basename($abs);
        if (in_array($ext, ['pdf', 'epub', 'cbz', 'zip'], true)) {
            $downloadName = $this->buildDownloadName($content, $ext);
        }
        header('Content-Type: ' . $mime);
        header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment') . '; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($abs));
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        readfile($abs);
        exit;
    }

    private function sanitizeDownloadFilename(string $name): string
    {
        $clean = preg_replace('/[\x00-\x1F\x7F"\\\/<>:\\|?*]+/', ' ', $name) ?? $name;
        $clean = preg_replace('/\s+/', ' ', $clean) ?? $clean;
        $clean = trim($clean);
        if ($clean === '' || $clean === '.pdf') {
            return 'arquivo.pdf';
        }
        return $clean;
    }

    private function buildDownloadName(array $content, string $ext): string
    {
        $seriesName = '';
        if (!empty($content['series_id'])) {
            $series = Series::findById((int)$content['series_id']);
            $seriesName = (string)($series['name'] ?? '');
        }
        $chapterName = (string)($content['title'] ?? '');
        $siteName = (string)config('app.name', 'Site');
        $base = trim($seriesName) !== '' ? $seriesName : 'Serie';
        $chapter = trim($chapterName) !== '' ? $chapterName : 'Capitulo';
        $filename = $base . ' - ' . $chapter . ' [' . $siteName . '].' . $ext;
        return $this->sanitizeDownloadFilename($filename);
    }

    public function epubOpen(Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            echo $this->view('reader/epub', ['error' => $msg]);
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            echo $this->view('reader/epub', ['error' => 'Conteúdo não encontrado.']);
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            echo $this->view('reader/epub', ['error' => 'Acesso indisponível para esta categoria.']);
            return;
        }
        $path = (string)($content['cbz_path'] ?? '');
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== 'epub') {
            Response::redirect(base_path('/reader/' . (int)$id));
            return;
        }

        ContentItem::incrementView((int)$content['id']);
        ContentEvent::log((int)$user['id'], (int)$content['id'], 'read_open', null, (new Request())->ip());
        Audit::log('read_open', (int)$user['id'], ['content_id' => (int)$content['id'], 'type' => 'epub']);

        $token = $this->downloadToken((int)$user['id'], (int)$content['id']);
        $fileUrl = base_path('/download/' . (int)$id . '?inline=1&token=' . urlencode($token));

        echo $this->view('reader/epub', [
            'content' => $content,
            'fileUrl' => $fileUrl,
        ]);
    }

    public function pdfOpen(Request $request, string $id): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            echo $this->view('reader/pdf', ['error' => $msg]);
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            echo $this->view('reader/pdf', ['error' => 'Conteúdo não encontrado.']);
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            echo $this->view('reader/pdf', ['error' => 'Acesso indisponível para esta categoria.']);
            return;
        }
        $path = (string)($content['cbz_path'] ?? '');
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            Response::redirect(base_path('/reader/' . (int)$id));
        }

        if (!empty($content['category_id'])) {
            $cat = Category::findByName((string)($content['category_name'] ?? ''));
            if (!$cat) {
                $catStmt = \App\Core\Database::connection()->prepare('SELECT * FROM categories WHERE id = :id');
                $catStmt->execute(['id' => (int)$content['category_id']]);
                $cat = $catStmt->fetch();
            }
            if ($cat) {
                $content['category_name'] = $cat['name'] ?? null;
            }
        }
        if (!empty($content['series_id'])) {
            $serStmt = \App\Core\Database::connection()->prepare('SELECT * FROM series WHERE id = :id');
            $serStmt->execute(['id' => (int)$content['series_id']]);
            $ser = $serStmt->fetch();
            if ($ser) {
                $content['series_name'] = $ser['name'] ?? null;
            }
        }

        $limitReads = (int)Setting::get('trial_reads_per_day', '0');
        if ($user['access_tier'] === 'trial' && $limitReads > 0) {
            $count = ContentEvent::countToday((int)$user['id'], 'read_open');
            if ($count >= $limitReads) {
                echo $this->view('reader/pdf', ['error' => 'Limite diário de leitura atingido.']);
                return;
            }
        }

        $previousChapterUrl = '';
        $nextChapterUrl = '';
        if (!empty($content['series_id'])) {
            $db = \App\Core\Database::connection();
            $pstmt = $db->prepare('SELECT id FROM content_items WHERE series_id = :s AND id < :id ORDER BY id DESC LIMIT 1');
            $pstmt->execute(['s' => (int)$content['series_id'], 'id' => (int)$content['id']]);
            $prev = $pstmt->fetch();
            if ($prev && !empty($prev['id'])) {
                $previousChapterUrl = base_path('/reader/pdf/' . (int)$prev['id']);
            }
            $nstmt = $db->prepare('SELECT id FROM content_items WHERE series_id = :s AND id > :id ORDER BY id ASC LIMIT 1');
            $nstmt->execute(['s' => (int)$content['series_id'], 'id' => (int)$content['id']]);
            $next = $nstmt->fetch();
            if ($next && !empty($next['id'])) {
                $nextChapterUrl = base_path('/reader/pdf/' . (int)$next['id']);
            }
        }

        $favIds = UserFavorite::getIdsForUser((int)$user['id'], [(int)$content['id']]);
        $progress = UserContentStatus::getProgressForUser((int)$user['id'], [(int)$content['id']]);
        $requestedPage = $request->get['page'] ?? null;
        $lastPage = (int)($progress[(int)$content['id']] ?? 0);
        if ($requestedPage !== null && is_numeric($requestedPage)) {
            $lastPage = max(0, (int)$requestedPage);
        }

        $token = $this->downloadToken((int)$user['id'], (int)$content['id']);
        $downloadUrl = $token !== '' ? base_path('/download/' . (int)$id . '?token=' . urlencode($token)) : '';
        $inlineUrl = $token !== '' ? base_path('/download/' . (int)$id . '?inline=1&token=' . urlencode($token)) : '';

        [$pages, $pageError] = $this->listPdfPagesWithError((string)$content['cbz_path']);

        echo $this->view('reader/pdf', [
            'content' => $content,
            'pages' => $pages,
            'error' => $pageError,
            'downloadUrl' => $downloadUrl,
            'inlineUrl' => $inlineUrl,
            'previousChapterUrl' => $previousChapterUrl,
            'nextChapterUrl' => $nextChapterUrl,
            'isFavorite' => !empty($favIds),
            'lastPage' => $lastPage,
            'csrf' => Csrf::token(),
        ]);
    }

    public function pdfPage(Request $request, string $id, string $page): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            http_response_code(403);
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            return;
        }
        $path = (string)($content['cbz_path'] ?? '');
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'pdf') {
            http_response_code(404);
            return;
        }

        $abs = $this->resolveCbzPath($path);
        if ($abs === null || !file_exists($abs)) {
            http_response_code(404);
            return;
        }

        $error = null;
        $images = $this->pdfImages($abs, $error);
        $pageIndex = (int)$page;
        if (!isset($images[$pageIndex])) {
            http_response_code(404);
            return;
        }
        $imgPath = $images[$pageIndex];
        if (!is_file($imgPath)) {
            http_response_code(404);
            return;
        }

        header('Content-Type: image/jpeg');
        header('Content-Length: ' . filesize($imgPath));
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        readfile($imgPath);
        exit;
    }

    private function isIos(string $ua): bool
    {
        $ua = strtolower($ua);
        return str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ipod');
    }

    public function page(Request $request, string $id, string $page): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            http_response_code(403);
            return;
        }
        $content = ContentItem::find((int)$id);
        if (!$content) {
            http_response_code(404);
            return;
        }
        if (!$this->canAccessCategory($user, (int)($content['category_id'] ?? 0))) {
            http_response_code(403);
            return;
        }
        $path = (string)($content['cbz_path'] ?? '');
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'pdf') {
            http_response_code(403);
            return;
        }
        if (!class_exists('ZipArchive')) {
            http_response_code(500);
            return;
        }
        $abs = $this->resolveCbzPath((string)$content['cbz_path']);
        if ($abs === null || !file_exists($abs)) {
            http_response_code(404);
            return;
        }

        $pageIndex = (int)$page;
        $zip = new \ZipArchive();
        if ($zip->open($abs) !== true) {
            http_response_code(500);
            return;
        }
        $images = $this->zipImages($zip);
        if (!isset($images[$pageIndex])) {
            $zip->close();
            http_response_code(404);
            return;
        }
        $name = $images[$pageIndex];
        $stream = $zip->getStream($name);
        if (!$stream) {
            $zip->close();
            http_response_code(404);
            return;
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'bmp' => 'image/bmp',
            'tif', 'tiff' => 'image/tiff',
            'jfif' => 'image/jpeg',
            default => 'image/jpeg',
        };
        header('Content-Type: ' . $mime);
        fpassthru($stream);
        fclose($stream);
        $zip->close();
        ContentEvent::log((int)$user['id'], (int)$content['id'], 'read_page', $pageIndex, (new Request())->ip());
    }

    public function openFile(Request $request): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            echo $this->view('reader/file', ['error' => $msg]);
            return;
        }
        $token = (string)($request->get['f'] ?? '');
        $relative = $this->decodeFileToken($token);
        if ($relative === null) {
            http_response_code(404);
            echo $this->view('reader/file', ['error' => 'Arquivo inválido.']);
            return;
        }
        $abs = $this->resolveLibraryCbzPath($relative);
        if ($abs === null || !file_exists($abs)) {
            http_response_code(404);
            echo $this->view('reader/file', ['error' => 'Arquivo não encontrado.']);
            return;
        }

        $pages = $this->listPagesFromAbsolute($abs);
        echo $this->view('reader/file', [
            'title' => basename($relative),
            'pages' => $pages,
            'fileToken' => $token,
        ]);
    }

    public function pageFile(Request $request, string $page): void
    {
        $user = Auth::user();
        if (!$user) {
            Response::redirect(base_path('/'));
        }
        if ($msg = $this->accessError($user)) {
            http_response_code(403);
            return;
        }
        $token = (string)($request->get['f'] ?? '');
        $relative = $this->decodeFileToken($token);
        if ($relative === null) {
            http_response_code(404);
            return;
        }
        $abs = $this->resolveLibraryCbzPath($relative);
        if ($abs === null || !file_exists($abs)) {
            http_response_code(404);
            return;
        }

        $pageIndex = (int)$page;
        $zip = new \ZipArchive();
        if ($zip->open($abs) !== true) {
            http_response_code(500);
            return;
        }
        $images = $this->zipImages($zip);
        if (!isset($images[$pageIndex])) {
            $zip->close();
            http_response_code(404);
            return;
        }
        $name = $images[$pageIndex];
        $stream = $zip->getStream($name);
        if (!$stream) {
            $zip->close();
            http_response_code(404);
            return;
        }
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
        header('Content-Type: ' . $mime);
        fpassthru($stream);
        fclose($stream);
        $zip->close();
    }

    private function listPagesWithError(string $cbzPath): array
    {
        if (!class_exists('ZipArchive')) {
            return [[], 'Leitor indisponível: extensão zip do PHP não instalada.'];
        }
        $abs = $this->resolveCbzPath($cbzPath);
        if ($abs === null || !file_exists($abs)) {
            return [[], 'Arquivo não encontrado.'];
        }
        $pages = $this->listPagesFromAbsolute($abs);
        if (empty($pages)) {
            $sig = $this->fileSignature($abs);
            if ($sig !== null && $sig !== '504b') {
                return [[], 'Arquivo não é um CBZ válido (assinatura diferente de ZIP).'];
            }
            return [[], 'Nenhuma página encontrada no CBZ.'];
        }
        return [$pages, null];
    }

    private function listPdfPagesWithError(string $pdfPath): array
    {
        $abs = $this->resolveCbzPath($pdfPath);
        if ($abs === null || !file_exists($abs)) {
            return [[], 'Arquivo não encontrado.'];
        }
        $error = null;
        $images = $this->pdfImages($abs, $error);
        if (!empty($error)) {
            return [[], $error];
        }
        if (empty($images)) {
            return [[], 'Nenhuma página encontrada no PDF.'];
        }
        return [$images, null];
    }

    private function pdfImages(string $abs, ?string &$error = null): array
    {
        $bin = $this->pdfToPpmBin();
        if ($bin === '') {
            $error = 'Conversor de PDF não configurado.';
            return [];
        }
        $cacheDir = $this->pdfCacheDir($abs);
        $images = $this->pdfImagesInDir($cacheDir);
        if (!empty($images)) {
            return $images;
        }

        $dpi = (int)config('converters.pdftoppm_dpi', 120);
        if ($dpi < 72) {
            $dpi = 72;
        } elseif ($dpi > 300) {
            $dpi = 300;
        }
        $maxPages = (int)config('converters.pdftoppm_max_pages', 0);
        $jpegQuality = (int)config('converters.pdftoppm_jpeg_quality', 85);
        if ($jpegQuality < 40) {
            $jpegQuality = 40;
        } elseif ($jpegQuality > 95) {
            $jpegQuality = 95;
        }
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $prefix = $cacheDir . '/page';
        $pageArgs = $maxPages > 0 ? sprintf('-f 1 -l %d', $maxPages) : '';
        $cmd = sprintf('"%s" -jpeg -jpegopt quality=%d -r %d %s %s %s', $bin, $jpegQuality, $dpi, $pageArgs, escapeshellarg($abs), escapeshellarg($prefix));
        $output = [];
        $code = 0;
        exec($cmd . ' 2>&1', $output, $code);
        if ($code !== 0) {
            $error = 'Falha ao converter PDF: ' . implode(' ', $output);
            return [];
        }

        $images = $this->pdfImagesInDir($cacheDir);
        if (empty($images)) {
            $error = 'Nenhuma página gerada.';
            return [];
        }
        return $images;
    }

    private function pdfImagesInDir(string $dir): array
    {
        $images = array_merge(
            glob($dir . '/page-*.jpg') ?: [],
            glob($dir . '/page-*.jpeg') ?: []
        );
        if (!empty($images)) {
            natsort($images);
            $images = array_values($images);
        }
        return $images;
    }

    private function pdfCacheDir(string $abs): string
    {
        $storageRoot = dirname(__DIR__, 2) . '/' . trim((string)config('storage.path', 'storage/uploads'), '/');
        $hash = hash('sha256', $abs . '|' . @filemtime($abs));
        return rtrim($storageRoot, '/') . '/pdf_cache/' . $hash;
    }

    private function pdfToPpmBin(): string
    {
        $bin = trim((string)config('converters.pdftoppm_bin', ''));
        if ($bin !== '' && is_executable($bin)) {
            return $bin;
        }
        $candidates = [
            '/usr/bin/pdftoppm',
            '/usr/local/bin/pdftoppm',
            'C:\\Program Files\\poppler\\Library\\bin\\pdftoppm.exe',
        ];
        foreach ($candidates as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }
        return '';
    }

    private function listPagesFromAbsolute(string $abs): array
    {
        $zip = new \ZipArchive();
        $open = $zip->open($abs);
        if ($open !== true) {
            Logger::error('cbz_open_failed', ['file' => basename($abs), 'code' => $open, 'size' => @filesize($abs)]);
            return [];
        }
        $images = $this->zipImages($zip);
        if (empty($images)) {
            $entries = [];
            $limit = min(20, $zip->numFiles);
            for ($i = 0; $i < $limit; $i++) {
                $name = $zip->getNameIndex($i);
                if ($name) {
                    $entries[] = $name;
                }
            }
            Logger::error('cbz_no_images', ['file' => basename($abs), 'entries' => $entries, 'size' => @filesize($abs)]);
        }
        $zip->close();
        return $images;
    }

    private function zipImages(\ZipArchive $zip): array
    {
        $images = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!$name) {
                continue;
            }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tif', 'tiff', 'jfif'], true)) {
                $images[] = $name;
            }
        }
        sort($images, SORT_NATURAL | SORT_FLAG_CASE);
        return $images;
    }

    private function resolveCbzPath(string $cbzPath): ?string
    {
        $clean = str_replace(['..', '\\'], ['', '/'], $cbzPath);
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

    private function validateDownloadToken(int $userId, int $contentId, string $token): bool
    {
        $secret = (string)config('security.download_secret', '');
        if ($secret === '') {
            return true;
        }
        if (!str_contains($token, '.')) {
            return false;
        }
        [$ts, $sig] = explode('.', $token, 2);
        $tsInt = (int)$ts;
        if ($tsInt <= 0 || abs(time() - $tsInt) > 3600) {
            return false;
        }
        $expected = hash_hmac('sha256', $userId . ':' . $contentId . ':' . $ts, $secret);
        return hash_equals($expected, $sig);
    }

    private function resolveLibraryCbzPath(string $relative): ?string
    {
        $clean = str_replace(['..', '\\'], ['', '/'], $relative);
        $root = dirname(__DIR__, 2) . '/' . trim((string)config('library.path', 'storage/library'), '/');
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

    private function fileSignature(string $path): ?string
    {
        $fh = @fopen($path, 'rb');
        if (!$fh) {
            return null;
        }
        $bytes = fread($fh, 2);
        fclose($fh);
        if ($bytes === false || $bytes === '') {
            return null;
        }
        return bin2hex($bytes);
    }

    private function decodeFileToken(string $token): ?string
    {
        if ($token === '') {
            return null;
        }
        $token = strtr($token, '-_', '+/');
        $pad = strlen($token) % 4;
        if ($pad !== 0) {
            $token .= str_repeat('=', 4 - $pad);
        }
        $decoded = base64_decode($token, true);
        if ($decoded === false || $decoded === '') {
            return null;
        }
        return $decoded;
    }

    private function accessError(array $user): ?string
    {
        if (Auth::isAdmin($user)) {
            return null;
        }
        if (Auth::isEquipe($user) && (!empty($user['uploader_agent']) || !empty($user['moderator_agent']) || !empty($user['support_agent']))) {
            return null;
        }
        if (($user['access_tier'] ?? '') === 'restrito') {
            return 'Acesso restrito.';
        }
        return null;
    }

    private function canAccessCategory(array $user, int $categoryId): bool
    {
        if ($categoryId <= 0) {
            return true;
        }
        if (Auth::isAdmin($user) || Auth::isModerator($user) || Auth::isEquipe($user)) {
            return true;
        }
        if (($user['access_tier'] ?? '') === 'vitalicio') {
            return true;
        }
        $cat = Category::findById($categoryId);
        if (!$cat) {
            return false;
        }
        $restrictedIds = [4, 5, 6];
        $requires = !empty($cat['requires_subscription']);
        $isRestricted = in_array($categoryId, $restrictedIds, true);
        if (!$requires && !$isRestricted) {
            return true;
        }
        if (!$this->subscriptionActive($user)) {
            return false;
        }
        $allowedIds = $this->allowedCategoryIds($user);
        if (empty($allowedIds)) {
            return false;
        }
        $allowedSet = array_flip($allowedIds);
        return isset($allowedSet[$categoryId]);
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
}
