<?php

require_once __DIR__ . '/../../src/Database.php';

/**
 * Migrasi: tabel login_attempts.
 *
 * Dipakai oleh LoginRateLimiter untuk brute-force protection pada endpoint
 * `?page=auth_check`. Tiap percobaan login (sukses/gagal) tercatat agar
 * limiter bisa menghitung kegagalan dalam jendela waktu tertentu.
 */

try {
    $pdo = Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        identifier VARCHAR(100) NOT NULL,
        ip VARCHAR(45) NOT NULL,
        successful TINYINT(1) NOT NULL DEFAULT 0,
        attempted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_identifier_ip_time (identifier, ip, attempted_at),
        INDEX idx_attempted_at (attempted_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Tabel 'login_attempts' berhasil dibuat atau sudah ada.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
