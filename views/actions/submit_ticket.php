<?php
// ACTION: handle_ticket_submission
// Menangani proses submit form ganti password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/ValidationHelper.php';

    // CSRF Token Validation
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid security token. Please try again.");
    }

    // Pastikan user login
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    $email = $_POST['email'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $userId = $_SESSION['user']['id'];

    // === VALIDASI KETAT ===
    $validations = [
        'email' => ValidationHelper::validateEmail($email),
        'reason' => ValidationHelper::validateLength($reason, 10, 500, 'Alasan')
    ];

    $validationResult = ValidationHelper::validateMultiple($validations);
    if (!$validationResult['valid']) {
        $errorHtml = ValidationHelper::formatErrors($validationResult['errors']);
        die("
            <div style='max-width: 600px; margin: 50px auto; padding: 20px; background: #fee2e2; border: 1px solid #fecaca; border-radius: 8px;'>
                <h3 style='color: #991b1b; margin-top: 0;'>❌ Validasi Gagal</h3>
                $errorHtml
                <a href='?page=dashboard&action=change_password' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px;'>Kembali</a>
            </div>
        ");
    }
    // === END VALIDASI ===

    // Sanitize input
    $email = ValidationHelper::sanitizeString($email);
    $reason = ValidationHelper::sanitizeString($reason);

    try {
        $pdo = Database::getInstance();

        $subject = "Permintaan Reset Password Email: $email";
        $description = "User mengajukan reset password email: $email.\nAlasan: $reason";

        $sql = "INSERT INTO tickets (user_id, subject, description, status) VALUES (:uid, :subject, :desc, 'pending')";
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            'uid' => $userId,
            'subject' => $subject,
            'desc' => $description
        ]);

        // Redirect sukses
        header('Location: ?page=dashboard&action=change_password&success=1');
        exit;

    } catch (Exception $e) {
        die("Gagal menyimpan tiket: " . $e->getMessage());
    }
}
