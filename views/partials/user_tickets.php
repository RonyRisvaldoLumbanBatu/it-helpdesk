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
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border);
    }

    .ticket-table thead {
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
    }

    .ticket-table th,
    .ticket-table td {
        padding: 16px;
        text-align: left;
    }

    .ticket-table tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.2s;
    }

    .ticket-table tr:hover {
        background: #f8fafc;
    }

    /* MOBILE CARD STYLES */
    @media (max-width: 768px) {
        .desktop-view {
            display: none;
        }

        .mobile-view {
            display: block;
            width: 100%;
            overflow-x: hidden;
            padding: 1px;
        }

        .m-ticket-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .m-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .m-date {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        .m-status {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 99px;
            border: 1px solid;
            white-space: nowrap;
        }

        .m-card-body {
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }

        .m-subject {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
            line-height: 1.3;
            /* Truncate 2 lines Title */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-wrap: break-word;
            word-break: break-word;
            /* Not break-all for title readability */
        }

        .m-desc {
            font-size: 0.8rem;
            color: #64748b;
            /* Truncate 2 lines Desc */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }
    }
</style>

<!-- =========================
     1. DESKTOP VIEW (TABLE)
========================== -->
<div class="desktop-view">
    <div class="table-responsive">
        <table class="ticket-table">
            <thead>
                <tr>
                    <th style="width: 20%;">Tanggal</th>
                    <th style="width: 60%;">Subjek & Kendala</th>
                    <th style="width: 20%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($myTickets) > 0): ?>
                    <?php foreach ($myTickets as $t): ?>
                        <tr onclick="window.location.href='?page=dashboard&action=ticket_detail&id=<?php echo $t['id']; ?>'"
                            style="cursor: pointer;">

                            <!-- DATE -->
                            <td>
                                <div style="font-weight: 500; color: var(--text-main);">
                                    <?php echo date('d M Y', strtotime($t['created_at'])); ?>
                                </div>
                                <small style="color: var(--text-muted);">
                                    Jam <?php echo date('H:i', strtotime($t['created_at'])); ?>
                                </small>
                            </td>

                            <!-- SUBJECT -->
                            <td>
                                <div style="font-size: 1rem; font-weight: 600; color: var(--primary); margin-bottom: 4px;">
                                    <?php echo htmlspecialchars($t['subject']); ?>
                                </div>
                                <div
                                    style="font-size: 0.9rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px; display: block;">
                                    <?php echo htmlspecialchars($t['description']); ?>
                                </div>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <?php
                                $statusConfig = [
                                    'pending' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fdba74', 'label' => 'Menunggu'],
                                    'in_progress' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#93c5fd', 'label' => 'Diproses'],
                                    'resolved' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#86efac', 'label' => 'Selesai'],
                                    'rejected' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fca5a5', 'label' => 'Ditolak'],
                                ];
                                $config = $statusConfig[$t['status']] ?? $statusConfig['pending'];
                                ?>
                                <span
                                    style="background: <?php echo $config['bg']; ?>; color: <?php echo $config['text']; ?>; border: 1px solid <?php echo $config['border']; ?>; padding: 4px 10px; border-radius: 99px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                    <?php echo $t['status']; ?>
                                </span>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 40px !important;">
                            <p style="color: var(--text-muted);">Belum ada riwayat tiket saya.</p>
                            <a href="?page=submit_ticket" class="btn btn-primary" style="margin-top: 10px;">Buat Tiket
                                Baru</a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- =========================
     2. MOBILE VIEW (PURE CARDS)
========================== -->
<div class="mobile-view">
    <?php if (count($myTickets) > 0): ?>
        <?php foreach ($myTickets as $t): ?>
            <?php
            $config = $statusConfig[$t['status']] ?? $statusConfig['pending'];
            ?>
            <div class="m-ticket-card"
                onclick="window.location.href='?page=dashboard&action=ticket_detail&id=<?php echo $t['id']; ?>'">
                <!-- HEADER: Date & Status -->
                <div class="m-card-header">
                    <div class="m-date">
                        <?php echo date('d M Y', strtotime($t['created_at'])); ?>
                        <span
                            style="opacity: 0.6; margin-left: 4px;"><?php echo date('H:i', strtotime($t['created_at'])); ?></span>
                    </div>
                    <div class="m-status"
                        style="background: <?php echo $config['bg']; ?>; color: <?php echo $config['text']; ?>; border-color: <?php echo $config['border']; ?>;">
                        <?php echo $t['status']; ?>
                    </div>
                </div>

                <!-- BODY: Content -->
                <div class="m-card-body">
                    <div class="m-subject">
                        <?php echo htmlspecialchars($t['subject']); ?>
                    </div>
                    <div class="m-desc">
                        <?php echo htmlspecialchars($t['description']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div
            style="text-align: center; padding: 40px; background: white; border-radius: 10px; border: 1px solid var(--border);">
            <p style="color: var(--text-muted);">Belum ada riwayat tiket.</p>
            <a href="?page=submit_ticket" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Buat Baru</a>
        </div>
    <?php endif; ?>
</div>