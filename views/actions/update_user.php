<?php
session_start();
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/ValidationHelper.php';

// CSRF Token Validation
if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid security token. Please try again.");
}

// Validasi Akses Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses Ditolak.");
}

$id = $_POST['user_id'] ?? '';
$name = $_POST['name'] ?? '';
$username = $_POST['username'] ?? '';
$role = $_POST['role'] ?? 'user';
$password = $_POST['password'] ?? '';

// === VALIDASI KETAT ===
$validations = [
    'name' => ValidationHelper::validateName($name),
    'username' => ValidationHelper::validateUsername($username),
    'role' => ValidationHelper::validateRole($role)
];

// Validasi password hanya jika diisi
if (!empty($password)) {
    $validations['password'] = ValidationHelper::validatePassword($password);
}

$validationResult = ValidationHelper::validateMultiple($validations);
if (!$validationResult['valid']) {
    $errorHtml = ValidationHelper::formatErrors($validationResult['errors']);
    die("
        <div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px;'>
            <h3 style='color: #991b1b; margin-top: 0;'>❌ Validasi Gagal</h3>
            $errorHtml
            <a href='?page=dashboard&action=manage_users' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px;'>Kembali</a>
        </div>
    ");
}
// === END VALIDASI ===

// Sanitize input
$name = ValidationHelper::sanitizeString($name);
$username = ValidationHelper::sanitizeString($username);

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
        $params['pass'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
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