<?php
// Migration for New Roles: staff, mahasiswa
require_once __DIR__ . '/../src/Database.php';

try {
    $pdo = Database::getInstance();

    echo "<h1>Migrasi Role Database</h1>";

    // 1. Alter Table ENUM
    echo "<li>Mengubah struktur tabel users (tambah role staff & mahasiswa)... ";
    try {
        // Note: We keep 'user' for backward compatibility or general generic users
        $sql = "ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'staff', 'mahasiswa') DEFAULT 'mahasiswa'";
        $pdo->exec($sql);
        echo "<span style='color:green'>BERHASIL</span></li>";
    } catch (Exception $e) {
        echo "<span style='color:red'>GAGAL (Mungkin sudah ada?): " . $e->getMessage() . "</span></li>";
    }

    // 2. Update Role berdasarkan Email Domain (Opsional, jika data email valid)
    echo "<li>Update user lama dengan domain @satyaterrabhinneka.ac.id menjadi 'staff'... ";
    $sql = "UPDATE users SET role = 'staff' WHERE email LIKE '%@satyaterrabhinneka.ac.id'";
    $rows = $pdo->exec($sql);
    echo "Updated: $rows users.</li>";

    echo "<li>Update user lama dengan domain @students.satyaterrabhinneka.ac.id menjadi 'mahasiswa'... ";
    $sql = "UPDATE users SET role = 'mahasiswa' WHERE email LIKE '%@students.satyaterrabhinneka.ac.id'";
    $rows = $pdo->exec($sql);
    echo "Updated: $rows users.</li>";

    echo "<br><strong>Migrasi Selesai!</strong>";
    echo "<br><a href='index.php'>Kembali ke Aplikasi</a>";

} catch (Exception $e) {
    die("Error Critical: " . $e->getMessage());
}
?>