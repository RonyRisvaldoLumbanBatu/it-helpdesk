<?php
// QUERY: Ambil tiket spesifik milik user yang sedang login
require_once __DIR__ . '/../../src/Database.php';

try {
    $pdo = Database::getInstance();
    $userId = $_SESSION['user']['id'];

    $sql = "SELECT * FROM tickets WHERE user_id = :uid ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $userId]);
    $myTickets = $stmt->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<div class="card-header" style="margin-bottom: 20px;">
    <h2>Riwayat Tiket Saya</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Pantau status pengajuan support Anda di sini.</p>
</div>

<div style="background: white; border-radius: 8px; border: 1px solid var(--border); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
            <tr>
                <th
                    style="padding: 12px 16px; text-align: left; width: 150px; font-size: 0.85rem; color: var(--text-muted);">
                    TANGGAL</th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">SUBJEK
                </th>
                <th
                    style="padding: 12px 16px; text-align: left; width: 120px; font-size: 0.85rem; color: var(--text-muted);">
                    STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($myTickets) > 0): ?>
                <?php foreach ($myTickets as $t): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 16px; font-size: 0.9rem; color: var(--text-muted);">
                            <?php echo date('d M Y', strtotime($t['created_at'])); ?><br>
                            <small><?php echo date('H:i', strtotime($t['created_at'])); ?></small>
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.9rem;">
                            <strong><?php echo htmlspecialchars($t['subject']); ?></strong>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">
                                <?php echo htmlspecialchars($t['description']); ?>
                            </div>
                        </td>
                        <td style="padding: 12px 16px;">
                            <?php
                            $statusColor = 'grey';
                            $statusLabel = $t['status'];

                            if ($t['status'] == 'pending') {
                                $statusColor = '#f59e0b';
                                $statusLabel = 'Menunggu';
                            }
                            if ($t['status'] == 'in_progress') {
                                $statusColor = '#3b82f6';
                                $statusLabel = 'Diproses';
                            }
                            if ($t['status'] == 'resolved') {
                                $statusColor = '#10b981';
                                $statusLabel = 'Selesai';
                            }
                            if ($t['status'] == 'rejected') {
                                $statusColor = '#ef4444';
                                $statusLabel = 'Ditolak';
                            }
                            ?>
                            <span
                                style="background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                <?php echo $statusLabel; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="padding: 40px; text-align: center;">
                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="Empty"
                            style="width: 64px; opacity: 0.5; margin-bottom: 10px;">
                        <p style="color: var(--text-muted);">Belum ada riwayat tiket.</p>
                        <a href="?page=dashboard&action=change_password" class="btn btn-primary"
                            style="margin-top: 10px; display: inline-block; text-decoration: none;">Buat Tiket Baru</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>