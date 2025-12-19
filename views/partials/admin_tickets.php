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

<!-- Main Card Container -->
<div
    style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); overflow: hidden; margin-bottom: 30px;">

    <!-- Card Header (Blue) -->
    <div
        style="padding: 20px 24px; background: var(--primary); border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: white;">Daftar Tiket Masuk</h2>
        <div style="display: flex; gap: 8px;">
            <a href="?page=dashboard&action=incoming_tickets&status=all" class="btn"
                style="padding: 6px 14px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; <?php echo $statusFilter == 'all' ? 'background: white; color: var(--primary); font-weight: 600;' : 'background: rgba(255,255,255,0.2); color: white;'; ?>">
                Semua
            </a>
            <a href="?page=dashboard&action=incoming_tickets&status=pending" class="btn"
                style="padding: 6px 14px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; <?php echo $statusFilter == 'pending' ? 'background: white; color: var(--primary); font-weight: 600;' : 'background: rgba(255,255,255,0.2); color: white;'; ?>">
                Pending
            </a>
            <a href="?page=dashboard&action=incoming_tickets&status=resolved" class="btn"
                style="padding: 6px 14px; font-size: 0.85rem; text-decoration: none; border-radius: 6px; <?php echo $statusFilter == 'resolved' ? 'background: white; color: var(--primary); font-weight: 600;' : 'background: rgba(255,255,255,0.2); color: white;'; ?>">
                Selesai
            </a>
        </div>
    </div>

    <div class="local-responsive-table" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
            <thead style="background: #f8fafc; color: #64748b; border-bottom: 1px solid #e2e8f0;">
                <tr>
                    <th
                        style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        Tanggal</th>
                    <th
                        style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        Pemohon</th>
                    <th
                        style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        Subjek</th>
                    <th
                        style="padding: 16px 24px; text-align: left; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                        Status</th>
                    <th style="padding: 16px 24px; text-align: right; font-size: 0.9rem; font-weight: 600;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($tickets) > 0): ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 16px 24px; font-size: 0.9rem; color: #64748b;">
                                <?php echo date('d M Y, H:i', strtotime($t['created_at'])); ?>
                            </td>
                            <td style="padding: 16px 24px; font-size: 0.9rem;">
                                <div style="display: flex; align-items: center;">
                                    <div
                                        style="width: 36px; height: 36px; background: #e0e7ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4338ca; font-weight: 700; font-size: 0.9rem; margin-right: 12px; border: 2px solid white; box-shadow: 0 0 0 1px #c7d2fe; flex-shrink: 0;">
                                        <?php echo strtoupper(substr($t['user_name'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b;">
                                            <?php echo htmlspecialchars($t['user_name']); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #94a3b8;">
                                            @<?php echo htmlspecialchars($t['username']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 24px; font-size: 0.9rem;">
                                <a href="?page=dashboard&action=ticket_detail&id=<?php echo $t['id']; ?>"
                                    style="text-decoration: none; color: var(--primary); font-weight: 600; display: block; margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($t['subject']); ?>
                                </a>
                                <div
                                    style="font-size: 0.85rem; color: #64748b; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <?php echo htmlspecialchars($t['description']); ?>
                                </div>
                            </td>
                            <td style="padding: 16px 24px;">
                                <?php
                                $statusColor = 'grey';
                                $statusBg = '#f3f4f6';
                                if ($t['status'] == 'pending') {
                                    $statusColor = '#d97706';
                                    $statusBg = '#fffbeb';
                                }
                                if ($t['status'] == 'in_progress') {
                                    $statusColor = '#2563eb';
                                    $statusBg = '#eff6ff';
                                }
                                if ($t['status'] == 'resolved') {
                                    $statusColor = '#16a34a';
                                    $statusBg = '#f0fdf4';
                                }
                                if ($t['status'] == 'rejected') {
                                    $statusColor = '#dc2626';
                                    $statusBg = '#fef2f2';
                                }
                                ?>
                                <span
                                    style="background: <?php echo $statusBg; ?>; color: <?php echo $statusColor; ?>; padding: 6px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                    <?php echo $t['status']; ?>
                                </span>
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <?php if ($t['status'] === 'pending' || $t['status'] === 'in_progress'): ?>
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <!-- Tombol Selesai -->
                                        <form action="?page=update_ticket" method="POST"
                                            onsubmit="return confirm('Tandai tiket ini sebagai Selesai?');">
                                            <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit"
                                                style="width: 32px; height: 32px; border-radius: 50%; border: none; background: #dcfce7; color: #166534; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                                onmouseover="this.style.background='#bbf7d0'; this.style.transform='translateY(-2px)'"
                                                onmouseout="this.style.background='#dcfce7'; this.style.transform='none'"
                                                title="Selesai">
                                                <i class="ri-check-line"></i>
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak -->
                                        <form action="?page=update_ticket" method="POST"
                                            onsubmit="return confirm('Tolak tiket ini?');">
                                            <input type="hidden" name="ticket_id" value="<?php echo $t['id']; ?>">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit"
                                                style="width: 32px; height: 32px; border-radius: 50%; border: none; background: #fee2e2; color: #b91c1c; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                                onmouseover="this.style.background='#fecaca'; this.style.transform='translateY(-2px)'"
                                                onmouseout="this.style.background='#fee2e2'; this.style.transform='none'"
                                                title="Tolak">
                                                <i class="ri-close-line"></i>
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #cbd5e1;">-</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 40px; text-align: center; color: #64748b;">
                            <div style="font-size: 3rem; margin-bottom: 10px; opacity: 0.3;"><i
                                    class="ri-inbox-archive-line"></i></div>
                            Belum ada tiket masuk sesuai filter.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>