<?php
// QUERY: Ambil tiket spesifik milik user yang sedang login
require_once __DIR__ . '/../../src/Database.php';

try {
    $pdo = Database::getInstance();
    $userId = $_SESSION['user']['id'];
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? 'all';

    $sql = "SELECT * FROM tickets WHERE user_id = :uid";
    $params = ['uid' => $userId];

    if ($status !== 'all') {
        $sql .= " AND status = :status";
        $params['status'] = $status;
    }

    if (!empty($search)) {
        $sql .= " AND (subject LIKE :search OR description LIKE :search)";
        $params['search'] = "%$search%";
    }

    $sql .= " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $myTickets = $stmt->fetchAll();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<div
    style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; gap: 16px;">
    <div>
        <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 4px;">Riwayat Tiket Saya</h2>
        <p style="color: #64748b; font-size: 0.95rem;">Pantau status dan perkembangan laporan Anda.</p>
    </div>

    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <!-- Filter Tabs -->
        <div style="background: #f1f5f9; padding: 4px; border-radius: 8px; display: flex;">
            <a href="?page=dashboard&action=my_tickets&status=all"
                style="padding: 6px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; <?php echo $status == 'all' ? 'background: white; color: var(--primary); box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #64748b;'; ?>">
                Semua
            </a>
            <a href="?page=dashboard&action=my_tickets&status=pending"
                style="padding: 6px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; <?php echo $status == 'pending' ? 'background: white; color: var(--primary); box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #64748b;'; ?>">
                Menunggu
            </a>
            <a href="?page=dashboard&action=my_tickets&status=resolved"
                style="padding: 6px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s; <?php echo $status == 'resolved' ? 'background: white; color: var(--primary); box-shadow: 0 1px 2px rgba(0,0,0,0.05);' : 'color: #64748b;'; ?>">
                Selesai
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" style="position: relative; width: 250px;">
            <input type="hidden" name="page" value="dashboard">
            <input type="hidden" name="action" value="my_tickets">
            <input type="text" name="search" placeholder="Cari tiket..."
                value="<?php echo htmlspecialchars($search); ?>"
                style="width: 100%; padding: 8px 12px 8px 36px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; outline: none; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='#e2e8f0'">
            <i class="ri-search-line"
                style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8;"></i>
        </form>
    </div>
</div>

<style>
    /* DESKTOP TABLE STYLES */
    .desktop-view {
        display: block;
    }

    .mobile-view {
        display: none;
    }

    .ticket-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .ticket-table th {
        background: #f8fafc;
        padding: 16px 24px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .ticket-table th:first-child {
        border-top-left-radius: 12px;
    }

    .ticket-table th:last-child {
        border-top-right-radius: 12px;
    }

    .ticket-table td {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        background: white;
        transition: background 0.2s;
    }

    .ticket-table tr:last-child td:first-child {
        border-bottom-left-radius: 12px;
    }

    .ticket-table tr:last-child td:last-child {
        border-bottom-right-radius: 12px;
    }

    .ticket-row:hover td {
        background: #f8fafc;
    }

    @media (max-width: 768px) {
        .desktop-view {
            display: none;
        }

        .mobile-view {
            display: block;
        }

        .ticket-card {
            background: white;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
    }
</style>

<!-- =========================
     1. DESKTOP VIEW
========================== -->
<div class="desktop-view"
    style="box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border-radius: 12px; background: white; border: 1px solid #e2e8f0;">
    <table class="ticket-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 55%;">Info Tiket</th>
                <th style="width: 20%;">Status</th>
                <th style="width: 10%;"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($myTickets) > 0): ?>
                <?php foreach ($myTickets as $t): ?>
                    <?php
                    // Status Colors
                    $s = $t['status'];
                    $color = '#64748b';
                    $icon = 'ri-time-line';
                    $bg = '#f1f5f9';
                    $status_label = $s; // Default fallback
            
                    if ($s == 'pending') {
                        $color = '#d97706';
                        $icon = 'ri-time-line';
                        $bg = '#fffbeb';
                        $status_label = 'Menunggu';
                    }
                    if ($s == 'in_progress') {
                        $color = '#2563eb';
                        $icon = 'ri-loader-4-line';
                        $bg = '#eff6ff';
                        $status_label = 'Diproses';
                    }
                    if ($s == 'resolved') {
                        $color = '#16a34a';
                        $icon = 'ri-checkbox-circle-line';
                        $bg = '#f0fdf4';
                        $status_label = 'Selesai';
                    }
                    if ($s == 'rejected') {
                        $color = '#dc2626';
                        $icon = 'ri-close-circle-line';
                        $bg = '#fef2f2';
                        $status_label = 'Ditolak';
                    }
                    ?>
                    <tr class="ticket-row"
                        onclick="window.location.href='?page=dashboard&action=ticket_detail&id=<?php echo $t['id']; ?>'"
                        style="cursor: pointer;">

                        <!-- DATE -->
                        <td>
                            <div style="font-weight: 600; color: #1e293b; font-size: 0.95rem;">
                                <?php echo date('d M Y', strtotime($t['created_at'])); ?>
                            </div>
                            <div
                                style="color: #94a3b8; font-size: 0.85rem; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                                <i class="ri-time-line" style="font-size: 0.9rem;"></i>
                                <?php echo date('H:i', strtotime($t['created_at'])); ?>
                            </div>
                        </td>

                        <!-- INFO -->
                        <td>
                            <div style="font-weight: 700; color: var(--primary); font-size: 1rem; margin-bottom: 6px;">
                                <?php echo htmlspecialchars($t['subject']); ?>
                            </div>
                            <div
                                style="color: #64748b; font-size: 0.9rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($t['description']); ?>
                            </div>
                        </td>

                        <!-- STATUS -->
                        <td>
                            <span
                                style="display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; background: <?php echo $bg; ?>; color: <?php echo $color; ?>; text-transform: uppercase; border: 1px solid <?php echo $color; ?>30;">
                                <i class="<?php echo $icon; ?>" style="margin-right: 6px; font-size: 1rem;"></i>
                                <?php echo $status_label; ?>
                            </span>
                        </td>

                        <!-- ACTION -->
                        <td style="text-align: right;">
                            <div
                                style="width: 36px; height: 36px; border-radius: 50%; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #64748b; transition: all 0.2s;">
                                <i class="ri-arrow-right-s-line" style="font-size: 1.25rem;"></i>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 60px 20px;">
                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png"
                            alt="Empty" style="width: 150px; opacity: 0.5; margin-bottom: 20px;">
                        <h3 style="color: #1e293b; font-size: 1.1rem; margin-bottom: 8px;">Belum ada tiket ditemukan</h3>
                        <p style="color: #64748b; font-size: 0.9rem;">Coba ubah filter atau buat tiket baru.</p>
                        <a href="?page=submit_ticket" class="btn btn-primary" style="margin-top: 20px;">Buat Tiket Baru</a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- =========================
     2. MOBILE VIEW (CARDS)
========================== -->
<div class="mobile-view">
    <?php if (count($myTickets) > 0): ?>
        <?php foreach ($myTickets as $t): ?>
            <?php
            // Reuse Colors
            $s = $t['status'];
            $color = '#64748b';
            $icon = 'ri-time-line';
            $bg = '#f1f5f9';
            $status_label = $s; // Default fallback
    
            if ($s == 'pending') {
                $color = '#d97706';
                $icon = 'ri-time-line';
                $bg = '#fffbeb';
                $status_label = 'Menunggu';
            }
            if ($s == 'in_progress') {
                $color = '#2563eb';
                $icon = 'ri-loader-4-line';
                $bg = '#eff6ff';
                $status_label = 'Diproses';
            }
            if ($s == 'resolved') {
                $color = '#16a34a';
                $icon = 'ri-checkbox-circle-line';
                $bg = '#f0fdf4';
                $status_label = 'Selesai';
            }
            if ($s == 'rejected') {
                $color = '#dc2626';
                $icon = 'ri-close-circle-line';
                $bg = '#fef2f2';
                $status_label = 'Ditolak';
            }
            ?>
            <div class="ticket-card"
                onclick="window.location.href='?page=dashboard&action=ticket_detail&id=<?php echo $t['id']; ?>'">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                    <span
                        style="font-size: 0.8rem; font-weight: 600; color: #94a3b8; display: flex; align-items: center; gap: 4px;">
                        <i class="ri-calendar-line"></i> <?php echo date('d M Y', strtotime($t['created_at'])); ?>
                    </span>
                    <span
                        style="display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 99px; font-size: 0.7rem; font-weight: 700; background: <?php echo $bg; ?>; color: <?php echo $color; ?>; text-transform: uppercase;">
                        <?php echo $status_label; ?>
                    </span>
                </div>

                <div style="font-weight: 700; color: #1e293b; font-size: 1rem; margin-bottom: 6px; line-height: 1.4;">
                    <?php echo htmlspecialchars($t['subject']); ?>
                </div>
                <div
                    style="color: #64748b; font-size: 0.85rem; line-height: 1.5; margin-bottom: 12px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    <?php echo htmlspecialchars($t['description']); ?>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <span
                        style="font-size: 0.85rem; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 4px;">
                        Lihat Detail <i class="ri-arrow-right-line"></i>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div
            style="text-align: center; padding: 40px; background: white; border-radius: 12px; border: 1px solid #e2e8f0; border-style: dashed;">
            <i class="ri-file-search-line" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px;"></i>
            <p style="color: #64748b; font-size: 0.95rem;">Tidak ada tiket yang ditemukan.</p>
        </div>
    <?php endif; ?>
</div>