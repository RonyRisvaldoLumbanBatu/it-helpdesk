<?php
// MIGRATION SCRIPT: Add email and google_id columns to users table
require_once __DIR__ . '/../src/Database.php';

echo "<h1>Migrasi Database untuk Google Login</h1>";

try {
    $pdo = Database::getInstance();

    // 1. Cek apakah kolom email sudah ada
    try {
        $pdo->query("SELECT email FROM users LIMIT 1");
        echo "<p>✅ Kolom 'email' sudah ada.</p>";
    } catch (Exception $e) {
        // Jika error, berarti kolom belum ada -> Buat kolom
        $sql = "ALTER TABLE users ADD COLUMN email VARCHAR(100) UNIQUE AFTER name";
        $pdo->exec($sql);
        echo "<p>✨ Berhasil menambahkan kolom 'email'.</p>";
    }

    // 2. Cek apakah kolom google_id sudah ada
    try {
        $pdo->query("SELECT google_id FROM users LIMIT 1");
        echo "<p>✅ Kolom 'google_id' sudah ada.</p>";
    } catch (Exception $e) {
        // Jika error, berarti kolom belum ada -> Buat kolom
        $sql = "ALTER TABLE users ADD COLUMN google_id VARCHAR(255) UNIQUE AFTER email";
        $pdo->exec($sql);
        echo "<p>✨ Berhasil menambahkan kolom 'google_id'.</p>";
    }

    // 3. Update Existing Default Users (Optional)
    // Update admin default agar punya email (agar tidak error constraint unique nanti)
    $stmt = $pdo->prepare("UPDATE users SET email = 'admin@satyaterrabhinneka.ac.id' WHERE username = 'admin' AND email IS NULL");
    $stmt->execute();

    $stmt = $pdo->prepare("UPDATE users SET email = 'user@students.satyaterrabhinneka.ac.id' WHERE username = 'user' AND email IS NULL");
    $stmt->execute();

    echo "<h3>Semua langkah migrasi selesai!</h3>";
    echo "<a href='index.php'>Kembali ke Login</a>";

} catch (Exception $e) {
    echo "<h3>Gagal Migrasi: </h3>" . $e->getMessage();
}
