<?php
session_start();
require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/NotificationService.php';

// CSRF Token Validation
if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("Invalid security token. Please try again.");
}

// Cek Login
if (!isset($_SESSION['user'])) {
    header('Location: ?page=login');
    exit;
}

$currentUser = $_SESSION['user'];
$ticketId = $_POST['ticket_id'] ?? 0;
$comment = trim($_POST['comment'] ?? '');

if (empty($ticketId) || empty($comment)) {
    header("Location: ?page=dashboard&action=ticket_detail&id=$ticketId&error=empty_comment");
    exit;
}

try {
    $pdo = Database::getInstance();

    // 1. Validasi Akses & Keberadaan Tiket
    $stmt = $pdo->prepare("SELECT user_id, subject FROM tickets WHERE id = :id");
    $stmt->execute(['id' => $ticketId]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        die("Tiket tidak ditemukan.");
    }

    // Hanya Pemilik Tiket atau Admin yang boleh komen
    if ($currentUser['role'] !== 'admin' && $currentUser['id'] != $ticket['user_id']) {
        die("Akses Ditolak.");
    }

    // 2. Insert Komentar
    $sql = "INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (:tid, :uid, :msg)";
    $stmtIns = $pdo->prepare($sql);
    $stmtIns->execute([
        'tid' => $ticketId,
        'uid' => $currentUser['id'],
        'msg' => $comment
    ]);

    // 3. Kirim Notifikasi (Menggunakan NotificationService)
    // Jika yang berkomentar bukan pemilik tiket, notify pemilik tiket
    if ($currentUser['id'] != $ticket['user_id']) {
        $notificationService = new NotificationService($pdo);
        $notificationService->notifyNewComment(
            $ticketId,
            $ticket['user_id'],
            $currentUser['name'],
            $comment
        );
    }

    // 4. Optional: Update 'updated_at' di tabel tickets
    // $pdo->prepare("UPDATE tickets SET updated_at = NOW() WHERE id = :id")->execute(['id' => $ticketId]);

    header("Location: ?page=dashboard&action=ticket_detail&id=$ticketId&success=comment_posted");
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>