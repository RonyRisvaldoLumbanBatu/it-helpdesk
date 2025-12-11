<?php
// ACTION: update_ticket
// Menangani perubahan status tiket oleh Admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../src/Database.php';

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

        $sql = "UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $newStatus, 'id' => $ticketId]);

        // Redirect kembali ke daftar tiket
        header('Location: ?page=dashboard&action=incoming_tickets&success=status_updated');
        exit;

    } catch (Exception $e) {
        die("Gagal update tiket: " . $e->getMessage());
    }
}
