<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Connector
{
    public static function isReady(): bool
    {
        try {
            Database::connection()->query('SELECT 1 FROM connectors LIMIT 1');
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public static function all(): array
    {
        $stmt = Database::connection()->query('SELECT * FROM connectors ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM connectors WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByName(string $name): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM connectors WHERE name = :n');
        $stmt->execute(['n' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(
        string $name,
        string $label,
        string $url,
        string $template,
        string $className,
        ?array $tags,
        ?array $customConfig,
        string $generatedCode,
        ?int $createdBy
    ): int
    {
        $stmt = Database::connection()->prepare('
            INSERT INTO connectors (name, label, url, template, class_name, tags, custom_config, generated_code, created_by, created_at) 
            VALUES (:n, :l, :u, :t, :c, :tags, :cfg, :code, :by, NOW())
        ');
        $stmt->execute([
            'n' => $name,
            'l' => $label,
            'u' => $url,
            't' => $template,
            'c' => $className,
            'tags' => $tags ? json_encode($tags) : null,
            'cfg' => $customConfig ? json_encode($customConfig) : null,
            'code' => $generatedCode,
            'by' => $createdBy,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function update(
        int $id,
        string $label,
        string $url,
        string $template,
        ?array $tags,
        ?array $customConfig,
        string $generatedCode
    ): void
    {
        $stmt = Database::connection()->prepare('
            UPDATE connectors 
            SET label = :l, url = :u, template = :t, tags = :tags, custom_config = :cfg, generated_code = :code, updated_at = NOW() 
            WHERE id = :id
        ');
        $stmt->execute([
            'l' => $label,
            'u' => $url,
            't' => $template,
            'tags' => $tags ? json_encode($tags) : null,
            'cfg' => $customConfig ? json_encode($customConfig) : null,
            'code' => $generatedCode,
            'id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM connectors WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function generateClassName(string $url): string
    {
        // Extract domain from URL
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        
        // Remove www. and TLD
        $host = preg_replace('/^www\./', '', $host);
        $host = preg_replace('/\.[a-z]{2,}$/i', '', $host);
        
        // Convert to PascalCase
        $parts = preg_split('/[^a-z0-9]+/i', $host);
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst(strtolower($part));
        }
        
        return $className ?: 'CustomConnector';
    }

    public static function generateIdentifier(string $url): string
    {
        // Extract domain from URL and make lowercase identifier
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';
        
        // Remove www. and TLD
        $host = preg_replace('/^www\./', '', $host);
        $host = preg_replace('/\.[a-z]{2,}$/i', '', $host);
        
        // Convert to lowercase alphanumeric
        $identifier = preg_replace('/[^a-z0-9]+/i', '', strtolower($host));
        
        return $identifier ?: 'customconnector';
    }
}
