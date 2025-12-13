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
            <div class="sidebar-header" style="display: flex; align-items: center; justify-content: center;">
                <img src="assets/images/logo.png" alt="Logo" style="width: 36px; height: auto; margin-right: 10px;">
                <span
                    style="color: #1e293b; font-weight: 800; letter-spacing: -0.5px; font-size: 1.2rem;">Helpdesk</span>
            </div>
            <nav class="sidebar-nav">
                <!-- MENU UMUM (Semua Role) -->
                <a href="?page=dashboard" class="nav-item <?php echo (!isset($_GET['action'])) ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line" style="margin-right: 10px;"></i> Overview
                </a>

                <!-- MENU KHUSUS USER (Staff, Mahasiswa, User Biasa) -->
                <?php if ($currentUser['role'] !== 'admin'): ?>
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
                <button class="mobile-toggle-btn" onclick="toggleSidebar()">
                    <i class="ri-menu-line"></i>
                </button>

                <div class="user-info" style="position: relative;">
                    <?php
                    $firstName = explode(' ', $currentUser['name'])[0];
                    $avatar = !empty($currentUser['avatar_url']) ? $currentUser['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($currentUser['name']) . "&background=eff6ff&color=1d4ed8";
                    ?>

                    <div onclick="toggleProfileMenu()"
                        style="cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 5px; border-radius: 8px; transition: background 0.2s;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                        <img src="<?php echo $avatar; ?>" alt="Profile"
                            style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                        <div style="display: flex; flex-direction: column; line-height: 1.2;">
                            <span
                                style="font-weight: 600; color: #334155; font-size: 0.95rem;"><?php echo htmlspecialchars($firstName); ?></span>
                            <!-- Role Badge moved here for cleaner look, or just keep it -->
                        </div>

                        <span
                            style="font-size: 0.75rem; background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; padding: 2px 8px; border-radius: 20px; text-transform: capitalize; font-weight: 600;">
                            <?php echo $currentUser['role']; ?>
                        </span>

                        <i class="ri-arrow-down-s-line" style="color: #64748b;"></i>
                    </div>

                    <!-- Dropdown Menu -->
                    <div id="profile-menu"
                        style="display: none; position: absolute; right: 0; top: 55px; background: white; border: 1px solid #e2e8f0; border-radius: 12px; width: 240px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 100; overflow: hidden; animation: fadeIn 0.1s ease-out;">
                        <div style="padding: 16px; background: #ffff;">
                            <div style="font-weight: 700; color: #0f172a; font-size: 0.95rem;">
                                <?php echo htmlspecialchars($currentUser['name']); ?>
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                <?php echo htmlspecialchars($currentUser['username']); ?>
                            </div>
                        </div>
                    </div>
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
                } elseif ($content == 'manage_users' && $currentUser['role'] === 'admin') {
                    require_once __DIR__ . '/partials/manage_users.php';
                } elseif ($content == 'reports' && $currentUser['role'] === 'admin') {
                    require_once __DIR__ . '/partials/reports.php';
                } elseif ($content == 'ticket_detail') {
                    require_once __DIR__ . '/partials/ticket_detail.php';
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
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        function toggleProfileMenu() {
            const menu = document.getElementById('profile-menu');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        }

        // Close when clicking outside
        document.addEventListener('click', function (event) {
            const menu = document.getElementById('profile-menu');
            const trigger = document.querySelector('.user-info');
            // Check if menu exists before accessing style
            if (menu && menu.style.display === 'block' && !trigger.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</body>

</html>