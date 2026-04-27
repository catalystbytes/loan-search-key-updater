<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../src/UpdateSearchKeyCommand.php';

$config = require __DIR__ . '/../config/env.php';

try {
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=utf8mb4",
        $config['DB_HOST'],
        $config['DB_NAME']
    );

    $pdo = new PDO(
        $dsn,
        $config['DB_USER'],
        $config['DB_PASS'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true,
        ]
    );

    $command = new UpdateSearchKeyCommand($pdo);
    $rows = $command->execute();

    echo "[DONE] Rows updated: {$rows}\n";

} catch (Throwable $e) {
    error_log($e->getMessage());
    echo "[FAILED] Check logs\n";
    exit(1);
}
