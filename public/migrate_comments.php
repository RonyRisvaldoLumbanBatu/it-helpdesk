<?php
// Migration for Ticket Comments
require_once __DIR__ . '/../src/Database.php';

try {
    $pdo = Database::getInstance();

    echo "<h1>Migrasi Tabel Komentar</h1>";

    // Create Table ticket_comments
    echo "<li>Membuat tabel ticket_comments... ";
    $sql = "CREATE TABLE IF NOT EXISTS ticket_comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_id INT NOT NULL,
        user_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB;";

    $pdo->exec($sql);
    echo "<span style='color:green'>BERHASIL</span></li>";

    echo "<br><strong>Migrasi Selesai!</strong>";
    echo "<br><a href='index.php'>Kembali ke Aplikasi</a>";

} catch (Exception $e) {
    die("Error Critical: " . $e->getMessage());
}
?>