<?php
// ACTION: Create User
// Hanya Admin yang boleh akses

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/ValidationHelper.php';

    // CSRF Token Validation
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid security token. Please try again.");
    }

    // Cek Sesi login & Role Admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("Akses ditolak!");
    }

    $name = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $password = $_POST['password'] ?? '';

    // === VALIDASI KETAT ===
    $validations = [
        'name' => ValidationHelper::validateName($name),
        'username' => ValidationHelper::validateUsername($username),
        'password' => ValidationHelper::validatePassword($password),
        'role' => ValidationHelper::validateRole($role)
    ];

    // Check jika ada error validasi
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

    try {
        $pdo = Database::getInstance();

        // Cek username duplikat
        $stmtChk = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmtChk->execute([$username]);
        if ($stmtChk->fetchColumn() > 0) {
            die("
                <div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px;'>
                    <h3 style='color: #991b1b; margin-top: 0;'>❌ Username Sudah Terpakai</h3>
                    <p>Username '<strong>" . htmlspecialchars($username) . "</strong>' sudah digunakan. Silakan pilih username lain.</p>
                    <a href='?page=dashboard&action=manage_users' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px;'>Kembali</a>
                </div>
            ");
        }

        // Sanitize input sebelum insert
        $name = ValidationHelper::sanitizeString($name);
        $username = ValidationHelper::sanitizeString($username);

        // Hash Password dengan BCRYPT
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

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
