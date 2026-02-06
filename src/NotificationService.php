<?php

/**
 * NotificationService
 * 
 * Service layer untuk mengelola notifikasi user
 * Memisahkan logika notifikasi agar bisa dipakai ulang di berbagai tempat
 */
class NotificationService
{
    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo ?? Database::getInstance();
    }

    /**
     * Buat notifikasi baru untuk user
     * 
     * @param int $userId - ID user yang akan menerima notifikasi
     * @param string $title - Judul notifikasi
     * @param string $message - Isi pesan notifikasi (bisa pakai HTML)
     * @param string $link - Link yang akan dibuka saat notifikasi diklik
     * @param string $type - Tipe notifikasi: 'info', 'success', 'warning', 'error'
     * @return bool - True jika berhasil, false jika gagal
     */
    public function createNotification($userId, $title, $message, $link, $type = 'info')
    {
        try {
            $validTypes = ['info', 'success', 'warning', 'error'];
            $type = in_array($type, $validTypes) ? $type : 'info';

            $sql = "INSERT INTO notifications (user_id, title, message, link, type) 
                    VALUES (:uid, :title, :msg, :link, :type)";
            
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute([
                'uid' => $userId,
                'title' => $title,
                'msg' => $message,
                'link' => $link,
                'type' => $type
            ]);
        } catch (Exception $e) {
            error_log("NotificationService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Buat notifikasi untuk komentar baru di tiket
     * 
     * @param int $ticketId - ID tiket
     * @param int $recipientUserId - ID user yang akan menerima notifikasi
     * @param string $commenterName - Nama user yang berkomentar
     * @param string $comment - Isi komentar
     * @return bool
     */
    public function notifyNewComment($ticketId, $recipientUserId, $commenterName, $comment)
    {
        // Truncate comment jika terlalu panjang
        $shortComment = strlen($comment) > 50 ? substr($comment, 0, 50) . '...' : $comment;
        
        $title = "Komentar Baru pada #" . $ticketId;
        $message = "<b>" . htmlspecialchars($commenterName) . "</b>: " . htmlspecialchars($shortComment);
        $link = "?page=dashboard&action=ticket_detail&id=" . $ticketId;

        return $this->createNotification($recipientUserId, $title, $message, $link, 'info');
    }

    /**
     * Buat notifikasi untuk status tiket yang berubah
     * 
     * @param int $ticketId - ID tiket
     * @param int $recipientUserId - ID user yang akan menerima notifikasi
     * @param string $newStatus - Status baru tiket
     * @param string $ticketSubject - Subjek tiket
     * @return bool
     */
    public function notifyTicketStatusChanged($ticketId, $recipientUserId, $newStatus, $ticketSubject)
    {
        $statusLabels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Diproses',
            'resolved' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        $statusLabel = $statusLabels[$newStatus] ?? $newStatus;
        
        $title = "Status Tiket #" . $ticketId . " Diperbarui";
        $message = "Tiket \"<b>" . htmlspecialchars($ticketSubject) . "</b>\" sekarang berstatus: <b>" . $statusLabel . "</b>";
        $link = "?page=dashboard&action=ticket_detail&id=" . $ticketId;

        $type = ($newStatus === 'resolved') ? 'success' : (($newStatus === 'rejected') ? 'error' : 'info');

        return $this->createNotification($recipientUserId, $title, $message, $link, $type);
    }

    /**
     * Buat notifikasi untuk tiket baru (untuk admin)
     * 
     * @param int $ticketId - ID tiket baru
     * @param int $adminUserId - ID admin yang akan menerima notifikasi
     * @param string $requesterName - Nama user yang membuat tiket
     * @param string $ticketSubject - Subjek tiket
     * @return bool
     */
    public function notifyNewTicket($ticketId, $adminUserId, $requesterName, $ticketSubject)
    {
        $title = "Tiket Baru #" . $ticketId;
        $message = "<b>" . htmlspecialchars($requesterName) . "</b> membuat tiket baru: \"" . htmlspecialchars($ticketSubject) . "\"";
        $link = "?page=dashboard&action=ticket_detail&id=" . $ticketId;

        return $this->createNotification($adminUserId, $title, $message, $link, 'warning');
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     * 
     * @param int $notificationId - ID notifikasi
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['id' => $notificationId]);
        } catch (Exception $e) {
            error_log("NotificationService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tandai semua notifikasi user sebagai sudah dibaca
     * 
     * @param int $userId - ID user
     * @return bool
     */
    public function markAllAsRead($userId)
    {
        try {
            $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :uid AND is_read = 0";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute(['uid' => $userId]);
        } catch (Exception $e) {
            error_log("NotificationService Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ambil notifikasi user (belum dibaca)
     * 
     * @param int $userId - ID user
     * @param int $limit - Jumlah maksimal notifikasi yang diambil
     * @return array
     */
    public function getUnreadNotifications($userId, $limit = 10)
    {
        try {
            $sql = "SELECT * FROM notifications 
                    WHERE user_id = :uid AND is_read = 0 
                    ORDER BY created_at DESC 
                    LIMIT :limit";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("NotificationService Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Hitung jumlah notifikasi yang belum dibaca
     * 
     * @param int $userId - ID user
     * @return int
     */
    public function getUnreadCount($userId)
    {
        try {
            $sql = "SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['uid' => $userId]);
            
            return (int) $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("NotificationService Error: " . $e->getMessage());
            return 0;
        }
    }
}
