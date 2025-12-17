<?php
session_start();
require_once __DIR__ . '/../../src/Database.php';

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

    // 3. Insert Notification Logic
    // If Admin replies to User, Notify User
    // If User replies to Admin, maybe Notify Admin (future feature)
    // Here: If currentUser != ticketOwner, Notify ticketOwner
    if ($currentUser['id'] != $ticket['user_id']) {
        $notifSql = "INSERT INTO notifications (user_id, title, message, link, type) VALUES (:uid, :title, :msg, :link, 'info')";
        $stmtNotif = $pdo->prepare($notifSql);

        // Truncate comment for message
        $shortComment = strlen($comment) > 50 ? substr($comment, 0, 50) . '...' : $comment;
        $title = "Komentar Baru pada #" . $ticketId;
        $message = "<b>" . htmlspecialchars($currentUser['name']) . "</b>: " . htmlspecialchars($shortComment);
        $link = "?page=dashboard&action=ticket_detail&id=" . $ticketId;

        $stmtNotif->execute([
            'uid' => $ticket['user_id'],
            'title' => $title,
            'msg' => $message,
            'link' => $link
        ]);
    }

    // 4. Optional: Update 'updated_at' di tabel tickets
    // $pdo->prepare("UPDATE tickets SET updated_at = NOW() WHERE id = :id")->execute(['id' => $ticketId]);

    header("Location: ?page=dashboard&action=ticket_detail&id=$ticketId&success=comment_posted");
    exit;

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>