<?php
// ACTION: handle_ticket_submission
// Menangani proses submit form ganti password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';

    // Pastikan user login
    if (!isset($_SESSION['user'])) {
        header('Location: ?page=login');
        exit;
    }

    $email = $_POST['email'] ?? '';
    $reason = $_POST['reason'] ?? '';
    $userId = $_SESSION['user']['id'];

    if (empty($email) || empty($reason)) {
        die("Email dan Alasan wajib diisi!");
    }

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
