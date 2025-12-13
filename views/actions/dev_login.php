<?php
// ACTION: Bypass Login for Testing
require_once __DIR__ . '/../../src/Database.php';

try {
    $pdo = Database::getInstance();
    // Ambil user role admin pertama
    $stmt = $pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1");
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: ?page=dashboard');
        exit;
    } else {
        die("Tidak ada user admin untuk testing. Buat user dulu.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
