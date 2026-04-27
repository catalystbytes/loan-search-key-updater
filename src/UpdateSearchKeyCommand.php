<?php

declare(strict_types=1);

class UpdateSearchKeyCommand
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function execute(): int
    {
        $start = microtime(true);

        $this->db->beginTransaction();

        try {
            $sql = "
                UPDATE dim_loan loan
                INNER JOIN dim_user user ON loan.created_by = user.id
                SET loan.search_key = CONCAT_WS(' ',
                    user.first_name,
                    user.last_name,
                    user.email,
                    user.mobile
                )
                WHERE loan.search_key IS NULL OR loan.search_key = '';
            ";

            $affectedRows = $this->db->exec($sql);

            $this->db->commit();

            $duration = round(microtime(true) - $start, 4);

            $this->log("[SUCCESS] Updated {$affectedRows} rows | Time: {$duration}s");

            return $affectedRows;

        } catch (Throwable $e) {
            $this->db->rollBack();

            $this->log("[ERROR] " . $e->getMessage());

            throw $e;
        }
    }

    private function log(string $message): void
    {
        $logFile = __DIR__ . '/../logs/search-key.log';
        $date = date('Y-m-d H:i:s');

        file_put_contents($logFile, "[{$date}] {$message}\n", FILE_APPEND);
    }
}
