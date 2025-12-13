<?php
// ACTION: Delete User
require_once __DIR__ . '/../../src/Database.php';

// Cek sesi dan role
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("Akses Ditolak!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $currentAdminId = $_SESSION['user']['id'];

    // Mencegah menghapus diri sendiri
    if ($userId == $currentAdminId) {
        header('Location: ?page=dashboard&action=manage_users&error=self_delete');
        exit;
    }

    try {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        // 1. Hapus Tiket User (Manual Cascade)
        // Hapus tiket yg dibuat user ini
        $stmtTickets = $pdo->prepare("DELETE FROM tickets WHERE user_id = :uid");
        $stmtTickets->execute(['uid' => $userId]);

        // 2. Hapus Komentar Tiket (Jika ada tabel comments, perlu dihapus dulu, tapi user_id di comments mgkn ada)
        // Cek tabel ticket_comments schema?
        // Step 723 showed migrate_comments.php: FOREIGN KEY (user_id) REFERENCES users(id).
        // So I must delete comments by this user too.

        $stmtComments = $pdo->prepare("DELETE FROM ticket_comments WHERE user_id = :uid");
        $stmtComments->execute(['uid' => $userId]);

        // 3. Hapus User
        $stmtUser = $pdo->prepare("DELETE FROM users WHERE id = :uid");
        $stmtUser->execute(['uid' => $userId]);

        $pdo->commit();

        header('Location: ?page=dashboard&action=manage_users&success=user_deleted');
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Gagal menghapus user: " . $e->getMessage());
    }
} else {
    header('Location: ?page=dashboard&action=manage_users');
    exit;
}
