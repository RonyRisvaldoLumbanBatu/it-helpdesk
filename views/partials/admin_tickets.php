<?php
require_once __DIR__ . '/../../src/Database.php';

// QUERY: Ambil data tiket
$statusFilter = $_GET['status'] ?? 'all';
$sql = "SELECT t.*, u.name as user_name, u.username 
        FROM tickets t 
        JOIN users u ON t.user_id = u.id ";

$params = [];
if ($statusFilter !== 'all') {
    $sql .= "WHERE t.status = :status ";
    $params['status'] = $statusFilter;
}

$sql .= "ORDER BY t.created_at DESC";

try {
    $pdo = Database::getInstance();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tickets = $stmt->fetchAll();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<div class="card-header"
    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Daftar Tiket Masuk</h2>
    <div>
        <a href="?page=dashboard&action=incoming_tickets&status=all"
            class="btn <?php echo $statusFilter == 'all' ? 'btn-primary' : 'btn-secondary'; ?>"
            style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;">Semua</a>
        <a href="?page=dashboard&action=incoming_tickets&status=pending"
            class="btn <?php echo $statusFilter == 'pending' ? 'btn-primary' : 'btn-secondary'; ?>"
            style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;">Pending</a>
        <a href="?page=dashboard&action=incoming_tickets&status=resolved"
            class="btn <?php echo $statusFilter == 'resolved' ? 'btn-primary' : 'btn-secondary'; ?>"
            style="padding: 5px 10px; font-size: 0.8rem; text-decoration: none;">Selesai</a>
    </div>
</div>

<div style="background: white; border-radius: 8px; border: 1px solid var(--border); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
            <tr>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">TANGGAL
                </th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">PEMOHON
                </th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">SUBJEK
                </th>
                <th style="padding: 12px 16px; text-align: left; font-size: 0.85rem; color: var(--text-muted);">STATUS
                </th>
                <th style="padding: 12px 16px; text-align: right; font-size: 0.85rem; color: var(--text-muted);">AKSI
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($tickets) > 0): ?>
                <?php foreach ($tickets as $t): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 12px 16px; font-size: 0.9rem;">
                            <?php echo date('d M Y H:i', strtotime($t['created_at'])); ?>
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.9rem;">
                            <strong><?php echo htmlspecialchars($t['user_name']); ?></strong><br>
                            <span
                                style="font-size: 0.8rem; color: var(--text-muted);">@<?php echo htmlspecialchars($t['username']); ?></span>
                        </td>
                        <td style="padding: 12px 16px; font-size: 0.9rem;">
                            <?php echo htmlspecialchars($t['subject']); ?>
                            <div
                                style="font-size: 0.8rem; color: var(--text-muted); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo htmlspecialchars($t['description']); ?>
                            </div>
                        </td>
                        <td style="padding: 12px 16px;">
                            <?php
                            $statusColor = 'grey';
                            if ($t['status'] == 'pending')
                                $statusColor = '#f59e0b'; // Orange
                            if ($t['status'] == 'in_progress')
                                $statusColor = '#3b82f6'; // Blue
                            if ($t['status'] == 'resolved')
                                $statusColor = '#10b981'; // Green
                            if ($t['status'] == 'rejected')
                                $statusColor = '#ef4444'; // Red
                            ?>
                            <span
                                style="background: <?php echo $statusColor; ?>20; color: <?php echo $statusColor; ?>; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600; text-transform: uppercase;">
                                <?php echo $t['status']; ?>
                            </span>
                        </td>
                        <td style="padding: 12px 16px; text-align: right;">
                            <?php if ($t['status'] === 'pending' || $t['status'] === 'in_progress'): ?>
                                <div style="display: flex; gap: 5px; justify-content: flex-end;">
                                    <!-- Tombol Selesai -->
                                    <form action="?page=update_ticket" method="POST"
                                        onsubmit="return confirm('Tandai tiket ini sebagai Selesai?');">
                                        <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit"
                                            style="background: #10b981; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;"
                                            title="Selesai">
                                            <i class="ri-check-line"></i>
                                        </button>
                                    </form>

                                    <!-- Tombol Tolak -->
                                    <form action="?page=update_ticket" method="POST" onsubmit="return confirm('Tolak tiket ini?');">
                                        <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit"
                                            style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-size: 0.8rem;"
                                            title="Tolak">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <span style="color: var(--text-muted); font-size: 0.8rem;">-</span>
                            <?php endif; ?>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="padding: 30px; text-align: center; color: var(--text-muted);">
                        Belum ada tiket masuk.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>