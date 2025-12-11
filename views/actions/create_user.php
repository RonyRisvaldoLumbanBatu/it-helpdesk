<?php
// ACTION: Create User
// Hanya Admin yang boleh akses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';

    // Cek Sesi login & Role Admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("Akses ditolak!");
    }

    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    // Validasi sederhana
    if (empty($name) || empty($username) || empty($password)) {
        die("Semua field wajib diisi!");
    }

    // Hash Password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo = Database::getInstance();

        // Cek username duplikat
        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmtChk->execute([$username]);
        if ($stmtChk->fetchColumn() > 0) {
            die("Username '$username' sudah terpakai! Silakan pilih yang lain.");
        }

        // Insert
        $sql = "INSERT INTO users (name, username, password, role) VALUES (:name, :username, :pass, :role)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'name' => $name,
            'username' => $username,
            'pass' => $hashedPassword,
            'role' => $role
        ]);

        header('Location: ?page=dashboard&action=manage_users&success=user_created');
        exit;

    } catch (Exception $e) {
        die("Gagal menambah user: " . $e->getMessage());
    }
}
