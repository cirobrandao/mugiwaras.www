<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config/bootstrap.php';

use App\Core\Database;

$rootUser = env('DB_ROOT_USERNAME');
$rootPass = env('DB_ROOT_PASSWORD');
$dbHost = env('DB_HOST', '127.0.0.1');
$dbPort = (int)env('DB_PORT', '3306');
$dbName = env('DB_DATABASE', 'mws_app');
$dbUser = env('DB_USERNAME', 'mws_user');
$dbPass = env('DB_PASSWORD', '');

if ($rootUser && $rootPass) {
    $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $dbHost, $dbPort);
    $pdo = new PDO($dsn, $rootUser, $rootPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("CREATE USER IF NOT EXISTS '$dbUser'@'%' IDENTIFIED BY '$dbPass'");
    $pdo->exec("GRANT ALL PRIVILEGES ON `$dbName`.* TO '$dbUser'@'%'");
}

$pdo = Database::connection();
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) UNIQUE, applied_at DATETIME)");

$sqlDir = dirname(__DIR__) . '/sql';
$files = glob($sqlDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    if (basename($file) === 'schema.sql') {
        continue;
    }
    $stmt = $pdo->prepare('SELECT COUNT(*) AS c FROM migrations WHERE filename = :f');
    $stmt->execute(['f' => basename($file)]);
    $row = $stmt->fetch();
    if ((int)$row['c'] > 0) {
        continue;
    }
    $sql = file_get_contents($file);
    $pdo->exec($sql);
    $ins = $pdo->prepare('INSERT INTO migrations (filename, applied_at) VALUES (:f, NOW())');
    $ins->execute(['f' => basename($file)]);
}

echo "Database initialized.\n";
