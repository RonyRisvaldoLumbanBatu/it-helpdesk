<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IT Helpdesk</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Icon Library -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <i class="ri-customer-service-2-fill" style="margin-right: 10px;"></i> Helpdesk
                <span
                    style="font-size: 0.7em; background: #e0e7ff; padding: 2px 6px; border-radius: 4px; margin-left: auto; color: var(--primary);">
                    <?php echo ucfirst($currentUser['role']); ?>
                </span>
            </div>
            <nav class="sidebar-nav">
                <!-- MENU UMUM (Semua Role) -->
                <a href="?page=dashboard" class="nav-item <?php echo (!isset($_GET['action'])) ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line" style="margin-right: 10px;"></i> Overview
                </a>

                <!-- MENU KHUSUS USER -->
                <?php if ($currentUser['role'] === 'user'): ?>
                    <a href="?page=dashboard&action=change_password"
                        class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'change_password') ? 'active' : ''; ?>">
                        <i class="ri-lock-password-line" style="margin-right: 10px;"></i> Ganti Password Email
                    </a>
                    <a href="?page=dashboard&action=my_tickets"
                        class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'my_tickets') ? 'active' : ''; ?>">
                        <i class="ri-ticket-line" style="margin-right: 10px;"></i> Tiket Saya
                    </a>
                <?php endif; ?>


                <!-- MENU KHUSUS ADMIN -->
                <?php if ($currentUser['role'] === 'admin'): ?>
                    <div
                        style="font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; margin: 1.5rem 0 0.5rem 1rem; font-weight: 600;">
                        Admin Area</div>
                    <a href="?page=dashboard&action=manage_users" class="nav-item">
                        <i class="ri-user-settings-line" style="margin-right: 10px;"></i> Kelola User
                    </a>
                    <a href="?page=dashboard&action=incoming_tickets" class="nav-item">
                        <i class="ri-inbox-archive-line" style="margin-right: 10px;"></i> Tiket Masuk
                    </a>
                    <a href="?page=dashboard&action=reports" class="nav-item">
                        <i class="ri-file-chart-line" style="margin-right: 10px;"></i> Laporan
                    </a>
                <?php endif; ?>

            </nav>
            <div class="sidebar-footer">
                <a href="?page=logout" class="nav-item" style="color: #ef4444;">
                    <i class="ri-logout-box-line" style="margin-right: 10px;"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="top-header">
                <div class="user-info">
                    <span>Halo, <strong><?php echo $currentUser['name']; ?></strong></span>
                </div>
            </header>

            <div class="content-body">
                <?php
                if (!isset($content) || $content == 'home') {
                    echo '<h2>Dashboard ' . ucfirst($currentUser['role']) . '</h2>';

                    if ($currentUser['role'] === 'admin') {
                        // LOAD REAL STATS
                        require_once __DIR__ . '/../src/Database.php';
                        $pdo = Database::getInstance();

                        $stmtPending = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'pending'");
                        $countPending = $stmtPending->fetchColumn();

                        $stmtResolved = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'resolved'");
                        $countResolved = $stmtResolved->fetchColumn();

                        echo '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px;">
                                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <h3 style="color: #f59e0b; font-size: 2rem;">' . NumberFormat($countPending) . '</h3>
                                    <p style="color: #64748b;">Tiket Pending</p>
                                </div>
                                <div style="background: white; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                    <h3 style="color: #10b981; font-size: 2rem;">' . NumberFormat($countResolved) . '</h3>
                                    <p style="color: #64748b;">Tiket Selesai</p>
                                </div>
                              </div>';
                    } else {
                        echo '<p class="text-muted">Selamat datang di layanan mandiri IT Helpdesk.</p>';
                        echo '<div style="margin-top: 20px; padding: 20px; background: #e0e7ff; border-radius: 8px; border: 1px solid #c7d2fe;">
                                <strong>Status Terkini:</strong> Sistem berjalan normal. Tidak ada gangguan dilaporkan.
                              </div>';
                    }
                } elseif ($content == 'change_password') {
                    require_once __DIR__ . '/partials/form_change_password.php';
                } elseif ($content == 'my_tickets') {
                    require_once __DIR__ . '/partials/user_tickets.php';
                } elseif ($content == 'incoming_tickets' && $currentUser['role'] === 'admin') {
                    require_once __DIR__ . '/partials/admin_tickets.php';
                } else {
                    echo "<h2>Halaman " . htmlspecialchars($content) . "</h2>";
                    echo "<p>Fitur ini belum diimplementasikan di versi demo.</p>";
                }

                function NumberFormat($num)
                {
                    return number_format($num);
                }
                ?>
            </div>
        </main>
    </div>
</body>

</html>