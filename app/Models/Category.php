<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category
{
    public static function isReady(): bool
    {
        try {
            Database::connection()->query('SELECT 1 FROM categories LIMIT 1');
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE name = :n');
        $stmt->execute(['n' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM categories WHERE slug = :s');
        $stmt->execute(['s' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function generateSlug(string $name): string
    {
        // Remove acentos e caracteres especiais
        $slug = mb_strtolower($name, 'UTF-8');
        $replacements = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            '\'' => '', '"' => '', ' ' => '-'
        ];
        $slug = strtr($slug, $replacements);
        // Remove tudo que não é letra, número ou hífen
        $slug = preg_replace('/[^a-z0-9-]/', '', $slug);
        // Remove hífens duplicados
        $slug = preg_replace('/-+/', '-', $slug);
        // Remove hífens do início e fim
        $slug = trim($slug, '-');
        return $slug;
    }

    public static function create(
        string $name,
        ?string $bannerPath = null,
        ?string $tagColor = null,
        string $displayOrientation = 'vertical',
        string $cbzDirection = 'rtl',
        string $cbzMode = 'page',
        string $epubMode = 'text',
        int $hideFromStore = 0,
        int $contentVideo = 0,
        int $contentCbz = 1,
        int $contentPdf = 1,
        int $contentEpub = 0,
        int $contentDownload = 0,
        int $sortOrder = 0,
        int $requiresSubscription = 0,
        int $adultOnly = 0
    ): int
    {
        $stmt = Database::connection()->prepare('INSERT INTO categories (name, banner_path, tag_color, display_orientation, cbz_direction, cbz_mode, epub_mode, hide_from_store, content_video, content_cbz, content_pdf, content_epub, content_download, sort_order, requires_subscription, adult_only, created_at) VALUES (:n, :b, :t, :o, :d, :m, :e, :hfs, :v, :cbz, :pdf, :epub, :dl, :so, :rs, :ao, NOW())');
        $stmt->execute([
            'n' => $name,
            'b' => $bannerPath,
            't' => $tagColor,
            'o' => $displayOrientation,
            'd' => $cbzDirection,
            'm' => $cbzMode,
            'e' => $epubMode,
            'hfs' => $hideFromStore,
            'v' => $contentVideo,
            'cbz' => $contentCbz,
            'pdf' => $contentPdf,
            'epub' => $contentEpub,
            'dl' => $contentDownload,
            'so' => $sortOrder,
            'rs' => $requiresSubscription,
            'ao' => $adultOnly,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function rename(int $id, string $name): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET name = :n WHERE id = :id');
        $stmt->execute(['n' => $name, 'id' => $id]);
    }

    public static function updateSlug(int $id, string $slug): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET slug = :s WHERE id = :id');
        $stmt->execute(['s' => $slug, 'id' => $id]);
    }

    public static function updateBanner(int $id, ?string $bannerPath): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET banner_path = :b WHERE id = :id');
        $stmt->execute(['b' => $bannerPath, 'id' => $id]);
    }

    public static function updateTagColor(int $id, ?string $tagColor): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET tag_color = :t WHERE id = :id');
        $stmt->execute(['t' => $tagColor, 'id' => $id]);
    }

    public static function updatePreferences(
        int $id,
        string $displayOrientation,
        string $cbzDirection,
        string $cbzMode,
        string $epubMode,
        int $hideFromStore,
        int $contentVideo,
        int $contentCbz,
        int $contentPdf,
        int $contentEpub,
        int $contentDownload,
        int $sortOrder,
        int $requiresSubscription,
        int $adultOnly
    ): void
    {
        $stmt = Database::connection()->prepare('UPDATE categories SET display_orientation = :o, cbz_direction = :d, cbz_mode = :m, epub_mode = :e, hide_from_store = :hfs, content_video = :v, content_cbz = :cbz, content_pdf = :pdf, content_epub = :epub, content_download = :dl, sort_order = :so, requires_subscription = :rs, adult_only = :ao WHERE id = :id');
        $stmt->execute([
            'o' => $displayOrientation,
            'd' => $cbzDirection,
            'm' => $cbzMode,
            'e' => $epubMode,
            'hfs' => $hideFromStore,
            'v' => $contentVideo,
            'cbz' => $contentCbz,
            'pdf' => $contentPdf,
            'epub' => $contentEpub,
            'dl' => $contentDownload,
            'so' => $sortOrder,
            'rs' => $requiresSubscription,
            'ao' => $adultOnly,
            'id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $seriesCount = \App\Models\Series::countByCategory($id);
        $contentCount = \App\Models\ContentItem::countByCategory($id);
        if ($seriesCount > 0 || $contentCount > 0) {
            throw new \RuntimeException('category_in_use');
        }
        $stmt = Database::connection()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

}