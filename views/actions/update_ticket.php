<?php
// ACTION: update_ticket
// Menangani perubahan status tiket oleh Admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';
    require_once __DIR__ . '/../../src/NotificationService.php';

    // CSRF Token Validation
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid security token. Please try again.");
    }

    // Cek Login & Role Admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        die("Akses ditolak!");
    }

    $ticketId = $_POST['ticket_id'] ?? null;
    $newStatus = $_POST['status'] ?? null;

    // Validasi input
    $allowedStatuses = ['pending', 'in_progress', 'resolved', 'rejected'];
    if (!$ticketId || !in_array($newStatus, $allowedStatuses)) {
        die("Data tidak valid!");
    }

    try {
        $pdo = Database::getInstance();

        // Ambil data tiket untuk notifikasi
        $stmtTicket = $pdo->prepare("SELECT user_id, subject FROM tickets WHERE id = :id");
        $stmtTicket->execute(['id' => $ticketId]);
        $ticket = $stmtTicket->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            die("Tiket tidak ditemukan!");
        }

        // Update status tiket
        $sql = "UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $newStatus, 'id' => $ticketId]);

        // Kirim notifikasi ke pemilik tiket tentang perubahan status
        $notificationService = new NotificationService($pdo);
        $notificationService->notifyTicketStatusChanged(
            $ticketId,
            $ticket['user_id'],
            $newStatus,
            $ticket['subject']
        );

        // Redirect kembali ke daftar tiket
        header('Location: ?page=dashboard&action=incoming_tickets&success=status_updated');
        exit;

    } catch (Exception $e) {
        die("Gagal update tiket: " . $e->getMessage());
    }
}
