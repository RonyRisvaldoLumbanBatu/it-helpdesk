<?php
require_once __DIR__ . '/src/Database.php';

try {
    $pdo = Database::getInstance();

    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT,
        link VARCHAR(255),
        is_read TINYINT(1) DEFAULT 0,
        type VARCHAR(50) DEFAULT 'info',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Tabel 'notifications' berhasil dibuat atau sudah ada.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
