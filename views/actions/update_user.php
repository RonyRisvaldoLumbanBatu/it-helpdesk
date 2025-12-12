<?php
session_start();
require_once __DIR__ . '/../../src/Database.php';

// Validasi Akses Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses Ditolak.");
}

$id = $_POST['user_id'] ?? '';
$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$role = $_POST['role'] ?? 'user';
$password = $_POST['password'] ?? '';

if (empty($id) || empty($name) || empty($username)) {
    header('Location: ?page=dashboard&action=manage_users&error=empty_fields');
    exit;
}

try {
    $pdo = Database::getInstance();

    // 1. Update Data Dasar
    $sql = "UPDATE users SET name = :name, username = :username, role = :role WHERE id = :id";
    $params = [
        'name' => $name,
        'username' => $username,
        'role' => $role,
        'id' => $id
    ];

    // 2. Jika Password Diisi, Update Password Juga
    if (!empty($password)) {
        $sql = "UPDATE users SET name = :name, username = :username, role = :role, password = :pass WHERE id = :id";
        $params['pass'] = password_hash($password, PASSWORD_BCRYPT);
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header('Location: ?page=dashboard&action=manage_users&success=user_updated');
    exit;

} catch (Exception $e) {
    // Handle Duplicate Entry (Username Conflict)
    if (str_contains($e->getMessage(), 'Duplicate entry')) {
        header('Location: ?page=dashboard&action=manage_users&error=username_exists');
    } else {
        die("Error DB: " . $e->getMessage());
    }
}
?>