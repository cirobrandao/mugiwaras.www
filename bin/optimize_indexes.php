<?php
/**
 * Script de Verificação e Otimização de Índices
 * 
 * Uso:
 * - php bin/optimize_indexes.php check     # Verifica índices atuais
 * - php bin/optimize_indexes.php backup    # Cria backup dos índices
 * - php bin/optimize_indexes.php apply     # Aplica as otimizações (faz backup automático)
 * - php bin/optimize_indexes.php analyze   # Atualiza estatísticas
 * - php bin/optimize_indexes.php report    # Gera relatório de performance
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Database;

class IndexOptimizer
{
    private $db;
    private $dbName;

    public function __construct()
    {
        try {
            $this->db = Database::connection();
        } catch (\Exception $e) {
            echo "❌ ERRO: Não foi possível conectar ao banco de dados\n";
            echo "   Detalhes: " . $e->getMessage() . "\n\n";
            echo "💡 Verifique:\n";
            echo "   1. Se o arquivo .env existe e está configurado corretamente\n";
            echo "   2. Se o MySQL está rodando\n";
            echo "   3. Se as credenciais estão corretas\n\n";
            exit(1);
        }
        
        // Pega o nome do banco de dados da configuração
        $config = \App\Core\Config::get('database');
        $this->dbName = $config['database'] ?? 'unknown';
    }

    /**
     * Cria backup dos índices atuais antes de aplicar alterações
     */
    public function backupCurrentIndexes()
    {
        echo "💾 CRIANDO BACKUP DOS ÍNDICES ATUAIS\n";
        echo str_repeat("=", 60) . "\n\n";

        $backupDir = dirname(__DIR__) . '/storage/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_His');
        $backupFile = $backupDir . "/indexes_backup_{$timestamp}.sql";

        $sql = "-- Backup de Índices - {$timestamp}\n";
        $sql .= "-- Banco de dados: {$this->dbName}\n";
        $sql .= "-- Gerado automaticamente antes de aplicar otimizações\n\n";

        // Pega todos os índices atuais de todas as tabelas
        $stmt = $this->db->prepare("
            SELECT 
                TABLE_NAME,
                INDEX_NAME,
                COLUMN_NAME,
                NON_UNIQUE,
                SEQ_IN_INDEX
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = :db
            AND INDEX_NAME != 'PRIMARY'
            ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX
        ");
        $stmt->execute(['db' => $this->dbName]);
        $indexes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $currentTable = '';
        $currentIndex = '';
        $columns = [];

        foreach ($indexes as $row) {
            if ($currentIndex !== $row['INDEX_NAME'] || $currentTable !== $row['TABLE_NAME']) {
                // Salva o índice anterior se existir
                if ($currentIndex !== '') {
                    $unique = $row['NON_UNIQUE'] == 0 ? 'UNIQUE ' : '';
                    $sql .= "ALTER TABLE `{$currentTable}` ADD {$unique}INDEX `{$currentIndex}` (" . implode(', ', $columns) . ");\n";
                }
                
                // Inicia novo índice
                $currentTable = $row['TABLE_NAME'];
                $currentIndex = $row['INDEX_NAME'];
                $columns = [];
            }
            
            $columns[] = "`{$row['COLUMN_NAME']}`";
        }

        // Salva o último índice
        if ($currentIndex !== '') {
            $sql .= "ALTER TABLE `{$currentTable}` ADD INDEX `{$currentIndex}` (" . implode(', ', $columns) . ");\n";
        }

        // Adiciona comandos de rollback comentados
        $sql .= "\n\n-- ============================================\n";
        $sql .= "-- COMANDOS DE ROLLBACK (se necessário)\n";
        $sql .= "-- ============================================\n";
        $sql .= "-- Para remover os índices criados pela otimização, descomente:\n\n";

        $expectedIndexes = [
            'users' => ['idx_data_ultimo_login', 'idx_access_tier', 'idx_role', 'idx_subscription_expires', 'idx_tier_expires'],
            'support_messages' => ['idx_user_id', 'idx_status', 'idx_status_created', 'idx_created_at'],
            'payments' => ['idx_status', 'idx_created_at', 'idx_user_status', 'idx_status_created_for_reports'],
            'vouchers' => ['idx_is_active', 'idx_expires_at', 'idx_active_expires'],
            'jobs' => ['idx_status', 'idx_job_type', 'idx_status_created'],
            'content_items' => ['idx_created_at', 'idx_view_count', 'idx_download_count', 'idx_series_order', 'idx_category_order', 'idx_series_id_navigation'],
            'series' => ['idx_pin_order', 'idx_category_pin', 'idx_adult_only'],
            'content_events' => ['idx_content_event_date', 'idx_created_at', 'idx_user_event_date'],
            'user_content_status' => ['idx_user_read', 'idx_updated_at'],
            'uploads' => ['idx_status', 'idx_user_status', 'idx_created_at'],
            'news' => ['idx_is_published', 'idx_published_at', 'idx_published_date'],
            'avatar_gallery' => ['idx_active_order'],
            'audit_log' => ['idx_created_at', 'idx_event_created', 'idx_user_id'],
            'categories' => ['idx_sort_order', 'idx_requires_subscription', 'idx_adult_only', 'idx_hide_from_store'],
            'packages' => ['idx_sort_order'],
            'news_categories' => ['idx_show_sidebar', 'idx_show_below_most_read'],
        ];

        foreach ($expectedIndexes as $table => $indexes) {
            foreach ($indexes as $index) {
                $sql .= "-- ALTER TABLE `{$table}` DROP INDEX `{$index}`;\n";
            }
        }

        if (file_put_contents($backupFile, $sql)) {
            echo "✅ Backup criado: $backupFile\n";
            echo "   Tamanho: " . number_format(filesize($backupFile)) . " bytes\n";
            echo "\n";
            return $backupFile;
        } else {
            echo "❌ ERRO: Não foi possível criar o backup\n\n";
            return false;
        }
    }

    /**
     * Verifica quais índices da otimização já existem
     */
    public function checkIndexes()
    {
        echo "🔍 VERIFICANDO ÍNDICES ATUAIS\n";
        echo str_repeat("=", 60) . "\n\n";

        $expectedIndexes = [
            'users' => ['idx_data_ultimo_login', 'idx_access_tier', 'idx_role', 'idx_subscription_expires', 'idx_tier_expires'],
            'support_messages' => ['idx_user_id', 'idx_status', 'idx_status_created', 'idx_created_at'],
            'payments' => ['idx_status', 'idx_created_at', 'idx_user_status', 'idx_status_created_for_reports'],
            'vouchers' => ['idx_is_active', 'idx_expires_at', 'idx_active_expires'],
            'jobs' => ['idx_status', 'idx_job_type', 'idx_status_created'],
            'content_items' => ['idx_created_at', 'idx_view_count', 'idx_download_count', 'idx_series_order', 'idx_category_order', 'idx_series_id_navigation'],
            'series' => ['idx_pin_order', 'idx_category_pin', 'idx_adult_only'],
            'content_events' => ['idx_content_event_date', 'idx_created_at', 'idx_user_event_date'],
            'user_content_status' => ['idx_user_read', 'idx_updated_at'],
            'uploads' => ['idx_status', 'idx_user_status', 'idx_created_at'],
            'news' => ['idx_is_published', 'idx_published_at', 'idx_published_date'],
            'avatar_gallery' => ['idx_active_order'],
            'audit_log' => ['idx_created_at', 'idx_event_created', 'idx_user_id'],
            'categories' => ['idx_sort_order', 'idx_requires_subscription', 'idx_adult_only', 'idx_hide_from_store'],
            'packages' => ['idx_sort_order'],
            'news_categories' => ['idx_show_sidebar', 'idx_show_below_most_read'],
        ];

        $totalExpected = 0;
        $totalExisting = 0;

        foreach ($expectedIndexes as $table => $indexes) {
            $existingIndexes = $this->getTableIndexes($table);
            
            echo "📋 Tabela: $table\n";
            
            foreach ($indexes as $indexName) {
                $totalExpected++;
                $exists = in_array($indexName, $existingIndexes);
                
                if ($exists) {
                    echo "  ✅ $indexName\n";
                    $totalExisting++;
                } else {
                    echo "  ❌ $indexName (faltando)\n";
                }
            }
            echo "\n";
        }

        echo str_repeat("=", 60) . "\n";
        echo "📊 RESUMO: $totalExisting/$totalExpected índices aplicados\n";
        
        if ($totalExisting < $totalExpected) {
            echo "⚠️  Execute: php bin/optimize_indexes.php apply\n";
        } else {
            echo "✅ Todos os índices de otimização estão aplicados!\n";
        }
        echo "\n";

        return $totalExisting === $totalExpected;
    }

    /**
     * Aplica as otimizações de índice
     */
    public function applyOptimizations()
    {
        echo "🚀 APLICANDO OTIMIZAÇÕES DE ÍNDICES\n";
        echo str_repeat("=", 60) . "\n\n";
        
        // Criar backup primeiro
        $backupFile = $this->backupCurrentIndexes();
        if ($backupFile === false) {
            echo "⚠️  Não foi possível criar backup. Deseja continuar mesmo assim? (s/N): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            fclose($handle);
            
            if (trim(strtolower($line)) !== 's') {
                echo "❌ Operação cancelada por segurança.\n";
                return false;
            }
        }
        
        $sqlFile = dirname(__DIR__) . '/sql/013_optimize_indexes.sql';
        
        if (!file_exists($sqlFile)) {
            echo "❌ Arquivo não encontrado: $sqlFile\n";
            return false;
        }

        $sql = file_get_contents($sqlFile);
        
        // Remove comentários
        $sql = preg_replace('/--.*$/m', '', $sql);
        
        // Separa statements individuais
        $statements = array_filter(
            array_map('trim', explode(';', $sql)),
            fn($s) => !empty($s) && stripos($s, 'ALTER TABLE') === 0
        );

        $success = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($statements as $statement) {
            // Extrai nome da tabela e índice para mensagem
            if (preg_match('/ALTER TABLE\s+(\w+)\s+ADD INDEX\s+(\w+)/i', $statement, $matches)) {
                $table = $matches[1];
                $index = $matches[2];
                
                echo "⏳ Criando $index em $table... ";
                
                try {
                    $this->db->exec($statement);
                    echo "✅\n";
                    $success++;
                } catch (\PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                        echo "⏭️  (já existe)\n";
                        $skipped++;
                    } else {
                        echo "❌ Erro: {$e->getMessage()}\n";
                        $failed++;
                    }
                }
            }
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 RESULTADO:\n";
        echo "  ✅ Criados: $success\n";
        echo "  ⏭️  Pulados: $skipped (já existiam)\n";
        echo "  ❌ Falhas: $failed\n";
        
        if ($backupFile !== false) {
            echo "\n💾 Backup dos índices anteriores salvo em:\n";
            echo "   $backupFile\n";
        }
        
        if ($failed === 0) {
            echo "\n✅ Otimizações aplicadas com sucesso!\n";
            echo "💡 Execute: php bin/optimize_indexes.php analyze\n";
        }
        
        echo "\n";
        
        return $failed === 0;
    }

    /**
     * Atualiza estatísticas das tabelas
     */
    public function analyzeTablesInfo()
    {
        echo "📊 ATUALIZANDO ESTATÍSTICAS DAS TABELAS\n";
        echo str_repeat("=", 60) . "\n\n";

        $tables = [
            'users', 'payments', 'content_items', 'content_events',
            'support_messages', 'jobs', 'news', 'series', 'uploads',
            'vouchers', 'audit_log', 'avatar_gallery', 'categories',
            'packages', 'news_categories', 'user_content_status'
        ];

        foreach ($tables as $table) {
            echo "⏳ Analisando $table... ";
            try {
                // Usa query() ao invés de exec() para evitar problemas com unbuffered queries
                $stmt = $this->db->query("ANALYZE TABLE $table");
                $stmt->closeCursor(); // Fecha o cursor imediatamente
                echo "✅\n";
            } catch (\PDOException $e) {
                echo "❌ {$e->getMessage()}\n";
            }
        }

        echo "\n✅ Estatísticas atualizadas!\n\n";
    }

    /**
     * Gera relatório de performance
     */
    public function generateReport()
    {
        echo "📈 RELATÓRIO DE PERFORMANCE DO BANCO DE DADOS\n";
        echo str_repeat("=", 60) . "\n\n";

        // 1. Tamanho das tabelas e índices
        echo "💾 TAMANHO DAS TABELAS E ÍNDICES:\n";
        $stmt = $this->db->prepare("
            SELECT 
                TABLE_NAME as tabela,
                ROUND(DATA_LENGTH / 1024 / 1024, 2) AS dados_mb,
                ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS indices_mb,
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = :db
            ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
            LIMIT 10
        ");
        $stmt->execute(['db' => $this->dbName]);
        
        echo sprintf("%-25s %12s %12s %12s\n", "Tabela", "Dados (MB)", "Índices (MB)", "Total (MB)");
        echo str_repeat("-", 65) . "\n";
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo sprintf(
                "%-25s %12s %12s %12s\n",
                $row['tabela'],
                $row['dados_mb'],
                $row['indices_mb'],
                $row['total_mb']
            );
        }
        echo "\n";

        // 2. Contagem de registros
        echo "📝 REGISTROS NAS TABELAS PRINCIPAIS:\n";
        $tables = ['users', 'content_items', 'content_events', 'payments', 'series', 'news'];
        
        foreach ($tables as $table) {
            try {
                $count = $this->db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
                echo sprintf("  %-20s %s\n", $table . ":", number_format($count, 0, ',', '.') . " registros");
            } catch (\PDOException $e) {
                echo sprintf("  %-20s %s\n", $table . ":", "Erro ao contar");
            }
        }
        echo "\n";

        // 3. Índices por tabela
        echo "📑 TOTAL DE ÍNDICES POR TABELA:\n";
        $stmt = $this->db->prepare("
            SELECT 
                TABLE_NAME as tabela,
                COUNT(DISTINCT INDEX_NAME) as total_indices
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = :db
            GROUP BY TABLE_NAME
            ORDER BY total_indices DESC
            LIMIT 10
        ");
        $stmt->execute(['db' => $this->dbName]);
        
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            echo sprintf("  %-25s %d índices\n", $row['tabela'], $row['total_indices']);
        }
        echo "\n";

        echo str_repeat("=", 60) . "\n";
    }

    /**
     * Obtém índices de uma tabela
     */
    private function getTableIndexes($table)
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = :db 
            AND TABLE_NAME = :table
            AND INDEX_NAME != 'PRIMARY'
        ");
        $stmt->execute(['db' => $this->dbName, 'table' => $table]);
        
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}

// ====================================================================
// EXECUÇÃO DO SCRIPT
// ====================================================================

if (php_sapi_name() !== 'cli') {
    die("Este script deve ser executado via CLI\n");
}

// Verificação básica de requisitos
if (!class_exists('App\Core\Database')) {
    die("❌ ERRO: Classe Database não encontrada. Verifique se o autoloader está funcionando.\n");
}

$command = $argv[1] ?? 'help';

try {
    $optimizer = new IndexOptimizer();
} catch (\Exception $e) {
    die("❌ ERRO ao inicializar: {$e->getMessage()}\n");
}

switch ($command) {
    case 'check':
        $optimizer->checkIndexes();
        break;
    
    case 'backup':
        $optimizer->backupCurrentIndexes();
        break;
        
    case 'apply':
        echo "⚠️  ATENÇÃO: Esta operação irá criar novos índices no banco de dados.\n";
        echo "           Um backup dos índices atuais será criado automaticamente.\n";
        echo "           Pode levar alguns minutos e afetar a performance temporariamente.\n\n";
        echo "Deseja continuar? (s/N): ";
        
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        fclose($handle);
        
        if (trim(strtolower($line)) === 's') {
            $optimizer->applyOptimizations();
        } else {
            echo "❌ Operação cancelada.\n";
        }
        break;
        
    case 'analyze':
        $optimizer->analyzeTablesInfo();
        break;
        
    case 'report':
        $optimizer->generateReport();
        break;
        
    case 'all':
        echo "🔧 EXECUTANDO OTIMIZAÇÃO COMPLETA\n\n";
        $optimizer->checkIndexes();
        echo "\n";
        $optimizer->applyOptimizations();
        echo "\n";
        $optimizer->analyzeTablesInfo();
        echo "\n";
        $optimizer->generateReport();
        break;
        
    default:
        echo "📚 USO DO SCRIPT DE OTIMIZAÇÃO DE ÍNDICES\n";
        echo str_repeat("=", 60) . "\n\n";
        echo "Comandos disponíveis:\n\n";
        echo "  check    - Verifica quais índices já estão aplicados\n";
        echo "  backup   - Cria backup dos índices atuais\n";
        echo "  apply    - Aplica as otimizações de índices (faz backup automático)\n";
        echo "  analyze  - Atualiza estatísticas das tabelas\n";
        echo "  report   - Gera relatório de performance\n";
        echo "  all      - Executa todas as operações acima\n";
        echo "\nExemplos:\n\n";
        echo "  php bin/optimize_indexes.php check\n";
        echo "  php bin/optimize_indexes.php backup\n";
        echo "  php bin/optimize_indexes.php apply\n";
        echo "  php bin/optimize_indexes.php analyze\n";
        echo "  php bin/optimize_indexes.php report\n";
        echo "\n";
        break;
        echo "  php bin/optimize_indexes.php report\n";
        echo "\n";
        break;
}
