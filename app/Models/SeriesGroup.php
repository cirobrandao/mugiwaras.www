<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class SeriesGroup
{
    /**
     * Busca todos os grupos de uma categoria
     */
    public static function byCategory(int $categoryId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM series_groups WHERE category_id = :c ORDER BY display_order ASC, name ASC'
        );
        $stmt->execute(['c' => $categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Busca um grupo por ID
     */
    public static function findById(int $id): ?array
    {
        $stmt = Database::connection()->prepare('SELECT * FROM series_groups WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Busca um grupo por nome em uma categoria
     */
    public static function findByName(int $categoryId, string $name): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM series_groups WHERE category_id = :c AND name = :n'
        );
        $stmt->execute(['c' => $categoryId, 'n' => $name]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Cria um novo grupo
     */
    public static function create(int $categoryId, string $name, ?string $description = null, int $displayOrder = 0): int
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO series_groups (category_id, name, description, display_order, created_at) 
             VALUES (:c, :n, :d, :o, NOW())'
        );
        $stmt->execute([
            'c' => $categoryId,
            'n' => $name,
            'd' => $description,
            'o' => $displayOrder
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    /**
     * Atualiza um grupo
     */
    public static function update(int $id, string $name, ?string $description = null, int $displayOrder = 0, int $isCollapsed = 0): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE series_groups 
             SET name = :n, description = :d, display_order = :o, is_collapsed = :c 
             WHERE id = :id'
        );
        $stmt->execute([
            'n' => $name,
            'd' => $description,
            'o' => $displayOrder,
            'c' => $isCollapsed,
            'id' => $id
        ]);
    }

    /**
     * Deleta um grupo (remove group_id das séries associadas)
     */
    public static function delete(int $id): void
    {
        // Primeiro remove as associações das séries
        $stmt = Database::connection()->prepare('UPDATE series SET group_id = NULL WHERE group_id = :id');
        $stmt->execute(['id' => $id]);
        
        // Depois deleta o grupo
        $stmt = Database::connection()->prepare('DELETE FROM series_groups WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    /**
     * Conta quantas séries existem em um grupo
     */
    public static function countSeries(int $groupId): int
    {
        $stmt = Database::connection()->prepare('SELECT COUNT(*) AS c FROM series WHERE group_id = :id');
        $stmt->execute(['id' => $groupId]);
        $row = $stmt->fetch();
        return (int)($row['c'] ?? 0);
    }

    /**
     * Busca grupos de uma categoria com contagem de séries
     */
    public static function byCategoryWithCounts(int $categoryId): array
    {
        $sql = 'SELECT sg.*, COUNT(s.id) AS series_count
                FROM series_groups sg
                LEFT JOIN series s ON s.group_id = sg.id
                WHERE sg.category_id = :c
                GROUP BY sg.id
                ORDER BY sg.display_order ASC, sg.name ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute(['c' => $categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Busca todos os grupos de todas as categorias com contagem de séries e nome da categoria
     */
    public static function allWithCounts(): array
    {
        $sql = 'SELECT sg.*, c.name AS category_name, COUNT(s.id) AS series_count
                FROM series_groups sg
                LEFT JOIN categories c ON c.id = sg.category_id
                LEFT JOIN series s ON s.group_id = sg.id
                GROUP BY sg.id
                ORDER BY c.name ASC, sg.display_order ASC, sg.name ASC';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Move série para um grupo
     */
    public static function addSeries(int $groupId, int $seriesId): void
    {
        $stmt = Database::connection()->prepare('UPDATE series SET group_id = :g WHERE id = :s');
        $stmt->execute(['g' => $groupId, 's' => $seriesId]);
    }

    /**
     * Remove série de um grupo
     */
    public static function removeSeries(int $seriesId): void
    {
        $stmt = Database::connection()->prepare('UPDATE series SET group_id = NULL WHERE id = :s');
        $stmt->execute(['s' => $seriesId]);
    }

    /**
     * Busca todas as séries de um grupo
     */
    public static function getSeries(int $groupId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM series WHERE group_id = :g ORDER BY name ASC'
        );
        $stmt->execute(['g' => $groupId]);
        return $stmt->fetchAll();
    }

    /**
     * Reordena grupos de uma categoria
     */
    public static function reorder(array $orderedIds): void
    {
        $db = Database::connection();
        $db->beginTransaction();
        
        try {
            $stmt = $db->prepare('UPDATE series_groups SET display_order = :o WHERE id = :id');
            foreach ($orderedIds as $order => $id) {
                $stmt->execute(['o' => $order, 'id' => $id]);
            }
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Alterna estado de colapso de um grupo
     */
    public static function toggleCollapsed(int $id): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE series_groups SET is_collapsed = NOT is_collapsed WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }
}
