<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - IT Helpdesk</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <!-- Icon Library -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
</head>

<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <!-- Toggle Button (Left Side) -->
                <button onclick="toggleSidebar()" class="mobile-close-btn">
                    <i class="ri-menu-line"></i>
                </button>

                <div class="brand-pill">
                    <img src="assets/images/logo.png" alt="Logo">
                    <span>Helpdesk</span>
                </div>
            </div>
            <style>
                .mobile-close-btn {
                    display: none;
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    color: #64748b;
                    cursor: pointer;
                    padding: 0;
                }

                @media (max-width: 768px) {
                    .mobile-close-btn {
                        display: block;
                    }
                }
            </style>
            <nav class="sidebar-nav">
                <!-- MENU UMUM (Semua Role) -->
                <a href="?page=dashboard" class="nav-item <?php echo (!isset($_GET['action'])) ? 'active' : ''; ?>">
                    <i class="ri-dashboard-line" style="margin-right: 10px; color: #60a5fa;"></i> Overview
                </a>

                <!-- MENU KHUSUS USER (Staff, Mahasiswa, User Biasa) -->
                <?php if ($currentUser['role'] !== 'admin'): ?>
                    <a href="?page=dashboard&action=change_password"
                        class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'change_password') ? 'active' : ''; ?>">
                        <i class="ri-lock-password-line" style="margin-right: 10px; color: #facc15;"></i> Ganti Password
                        Email
                    </a>
                    <a href="?page=dashboard&action=my_tickets"
                        class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'my_tickets') ? 'active' : ''; ?>">
                        <i class="ri-ticket-line" style="margin-right: 10px; color: #4ade80;"></i> Tiket Saya
                    </a>
                <?php endif; ?>


                <!-- MENU KHUSUS ADMIN -->
                <?php if ($currentUser['role'] === 'admin'): ?>
                    <div
                        style="font-size: 0.75rem; text-transform: uppercase; color: rgba(255,255,255,0.4); margin: 1.5rem 0 0.5rem 1rem; font-weight: 600;">
                        Admin Area</div>
                    <a href="?page=dashboard&action=manage_users" class="nav-item">
                        <i class="ri-user-settings-line" style="margin-right: 10px; color: #fcd34d;"></i> Kelola User
                    </a>
                    <a href="?page=dashboard&action=incoming_tickets" class="nav-item">
                        <i class="ri-inbox-archive-line" style="margin-right: 10px; color: #f87171;"></i> Tiket Masuk
                    </a>
                    <a href="?page=dashboard&action=reports" class="nav-item">
                        <i class="ri-file-chart-line" style="margin-right: 10px; color: #c084fc;"></i> Laporan
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

                <!-- SEARCH BAR (Visual Only for now) -->
                <div class="header-search">
                    <i class="ri-search-line"></i>
                    <input type="text" placeholder="Cari tiket atau bantuan...">
                </div>

                <div class="header-right">
                    <!-- NOTIFICATION BELL -->
                    <div class="notification-btn">
                        <i class="ri-notification-3-line"></i>
                        <span class="badget-dot"></span>
                    </div>

                    <div class="user-info" style="position: relative;">
                        <?php
                        $firstName = explode(' ', $currentUser['name'])[0];
                        $avatar = !empty($currentUser['avatar_url']) ? $currentUser['avatar_url'] : "https://ui-avatars.com/api/?name=" . urlencode($currentUser['name']) . "&background=1d4ed8&color=ffffff";
                        ?>

                        <div onclick="toggleProfileMenu()"
                            style="cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 5px; border-radius: 8px; transition: background 0.2s;"
                            onmouseover="this.style.background='#f8fafc'"
                            onmouseout="this.style.background='transparent'">
                            <img src="<?php echo $avatar; ?>" alt="Profile"
                                style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                            <div class="user-details" style="display: flex; flex-direction: column; line-height: 1.2;">
                                <span
                                    style="font-weight: 600; color: #334155; font-size: 0.95rem;"><?php echo htmlspecialchars($firstName); ?></span>
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

                        // 1. Fetch KPI Stats
                        $totalTickets = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();
                        $countPending = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'pending'")->fetchColumn();
                        // $countProcess removed as requested
                        $countResolved = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'resolved'")->fetchColumn();

                        // 2. Fetch Recent Activities (Latest 5 Tickets)
                        $recentTickets = $pdo->query("SELECT t.*, u.name as requester_name FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

                        // RENDER UI
                        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; margin-bottom: 30px;">
                                <!-- Total -->
                                <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6; box-shadow: 0 4px 6px rgba(0,0,0,0.02); text-align: center;">
                                    <div style="font-size: 3rem; font-weight: 700; color: #3b82f6; line-height: 1.2;">' . number_format($totalTickets) . '</div>
                                    <div style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Total Tiket</div>
                                </div>

                                <!-- Pending -->
                                <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #f97316; box-shadow: 0 4px 6px rgba(0,0,0,0.02); text-align: center;">
                                    <div style="font-size: 3rem; font-weight: 700; color: #f97316; line-height: 1.2;">' . number_format($countPending) . '</div>
                                    <div style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Menunggu</div>
                                </div>

                                <!-- Resolved -->
                                <div style="background: white; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; border-top: 4px solid #10b981; box-shadow: 0 4px 6px rgba(0,0,0,0.02); text-align: center;">
                                    <div style="font-size: 3rem; font-weight: 700; color: #10b981; line-height: 1.2;">' . number_format($countResolved) . '</div>
                                    <div style="color: #64748b; font-size: 0.95rem; font-weight: 500;">Selesai</div>
                                </div>
                              </div>';

                        // Recent Activity Table
                        echo '<div style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">';
                        echo '<div style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                                <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b;">Tiket Terbaru Masuk</h3>
                                <a href="?page=dashboard&action=incoming_tickets" style="font-size: 0.85rem; color: var(--primary); font-weight: 600; text-decoration: none;">Lihat Semua &rarr;</a>
                              </div>';

                        if (count($recentTickets) > 0) {
                            echo '<table style="width: 100%; border-collapse: collapse;">
                                    <thead style="background: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <tr>
                                            <th style="padding: 15px 20px; text-align: left; font-weight: 600;">Subjek</th>
                                            <th style="padding: 15px 20px; text-align: left; font-weight: 600;">Pengirim</th>
                                            <th style="padding: 15px 20px; text-align: left; font-weight: 600;">Status</th>
                                            <th style="padding: 15px 20px; text-align: right; font-weight: 600;">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody style="font-size: 0.9rem; color: #334155;">';

                            $statusMap = [
                                'pending' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'label' => 'Pending'],
                                'in_progress' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'label' => 'Proses'],
                                'resolved' => ['bg' => '#ecfdf5', 'text' => '#047857', 'label' => 'Selesai'],
                                'rejected' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'label' => 'Ditolak']
                            ];

                            foreach ($recentTickets as $ticket) {
                                $st = $statusMap[$ticket['status']] ?? ['bg' => '#f1f5f9', 'text' => '#64748b', 'label' => $ticket['status']];
                                echo '<tr style="border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.1s;" onclick="window.location.href=\'?page=dashboard&action=ticket_detail&id=' . $ticket['id'] . '\'" onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'transparent\'">
                                        <td style="padding: 15px 20px; max-width: 300px;">
                                            <div style="font-weight: 600; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' . htmlspecialchars($ticket['subject']) . '</div>
                                            <div style="font-size: 0.8rem; color: #94a3b8;">#' . $ticket['id'] . '</div>
                                        </td>
                                        <td style="padding: 15px 20px;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <div style="width: 24px; height: 24px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; color: #64748b;">' . strtoupper(substr($ticket['requester_name'], 0, 1)) . '</div>
                                                ' . htmlspecialchars($ticket['requester_name']) . '
                                            </div>
                                        </td>
                                        <td style="padding: 15px 20px;">
                                            <span style="background: ' . $st['bg'] . '; color: ' . $st['text'] . '; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">' . $st['label'] . '</span>
                                        </td>
                                        <td style="padding: 15px 20px; text-align: right; color: #64748b; font-size: 0.85rem;">
                                            ' . date('d M, H:i', strtotime($ticket['created_at'])) . '
                                        </td>
                                      </tr>';
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<div style="padding: 40px; text-align: center; color: #64748b;">Belum ada tiket masuk.</div>';
                        }
                        echo '</div>'; // End Table Wrapper
                
                    } else {
                        // ============================================
                        // USER DASHBOARD (RICH UI)
                        // ============================================
                
                        // 1. Fetch User Stats
                        require_once __DIR__ . '/../src/Database.php';
                        $pdo = Database::getInstance();
                        $uid = $currentUser['id'];

                        // Stats Queries
                        $totalT = $pdo->query("SELECT COUNT(*) FROM tickets WHERE user_id = $uid")->fetchColumn();
                        $activeT = $pdo->query("SELECT COUNT(*) FROM tickets WHERE user_id = $uid AND status IN ('pending', 'in_progress')")->fetchColumn();
                        $solvedT = $pdo->query("SELECT COUNT(*) FROM tickets WHERE user_id = $uid AND status = 'resolved'")->fetchColumn();

                        // Last Ticket
                        $lastTicket = $pdo->query("SELECT * FROM tickets WHERE user_id = $uid ORDER BY created_at DESC LIMIT 1")->fetch();

                        // 2. Render UI
                        echo '<div style="margin-top: 10px;">';

                        // HERO SECTION
                        echo '<div style="background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                                <h2 style="margin: 0; font-size: 1.8rem;">Halo, ' . htmlspecialchars(explode(' ', $currentUser['name'])[0]) . '! 👋</h2>
                                <p style="margin: 8px 0 20px 0; opacity: 0.9;">Ada yang bisa kami bantu? Tim IT siap mendukung produktivitas Anda.</p>
                                <a href="?page=dashboard&action=change_password" class="btn" style="background: white; color: var(--primary); border: none; font-weight: 700; padding: 10px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    <i class="ri-add-circle-fill"></i> Buat Tiket Baru
                                </a>
                              </div>';

                        // STATS GRID (Keep as is)
                        // ... (I will assume I don't need to rewrite stats grid if I don't touch lines)
                        // Wait, I am replacing a block. I should encompass lines.
                        // I will skip replacing STATS GRID here to save tokens if possible, but I need to be careful with range.
                        // Actually, I'll just look at the target range around the Hero Section.
                        // And I need to insert the Case for 'create_ticket' earlier in the file?
                        // No, the content loading is handled via $content variable logic.
                        // Wait, `dashboard.php` handles `$content` logic?
                        // Line 61 in `index.php`: `$content = $_GET['action'] ?? 'home';`.
                        // Then `dashboard.php` line ~275 logic:
                        /*
                        if ($content === 'home') { ... } 
                        elseif ($content === 'my_tickets') { ... } 
                        */

                        // So I need to add `elseif ($content === 'create_ticket')` block.
                
                        /* Let's do the JS first at the bottom of file */
                        /* Then the Hero Button update */
                        /* Then the Controller update */

                        /* I will use 3 chunks */

                        /* Chunk 1: Hero Button */
                        /* Chunk 2: Controller Logic */
                        /* Chunk 3: JS Scripts */

                        /* Wait, I should do ONE replace call if possible or 3. */
                        /* I'll use AllowMultiple: true */


                        // STATS GRID
                        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
                                <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; border-top: 4px solid #3b82f6; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <div style="font-size: 2.5rem; font-weight: 700; color: #3b82f6;">' . $totalT . '</div>
                                    <div style="color: #64748b; font-size: 0.9rem;">Total Tiket Saya</div>
                                </div>
                                <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; border-top: 4px solid #eab308; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <div style="font-size: 2.5rem; font-weight: 700; color: #eab308;">' . $activeT . '</div>
                                    <div style="color: #64748b; font-size: 0.9rem;">Sedang Diproses</div>
                                </div>
                                <div style="background: white; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; border-top: 4px solid #10b981; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <div style="font-size: 2.5rem; font-weight: 700; color: #10b981;">' . $solvedT . '</div>
                                    <div style="color: #64748b; font-size: 0.9rem;">Masalah Selesai</div>
                                </div>
                              </div>';

                        // LATEST TICKET SECTION
                        echo '<h3 style="font-size: 1.1rem; color: #334155; margin-bottom: 15px;">Aktivitas Terakhir</h3>';
                        if ($lastTicket) {
                            $statusColors = [
                                'pending' => '#f59e0b',
                                'in_progress' => '#3b82f6',
                                'resolved' => '#10b981',
                                'rejected' => '#ef4444'
                            ];
                            $color = $statusColors[$lastTicket['status']] ?? '#64748b';

                            echo '<div onclick="window.location.href=\'?page=dashboard&action=ticket_detail&id=' . $lastTicket['id'] . '\'" 
                                       style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px; display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: transform 0.2s;"
                                       onmouseover="this.style.borderColor=\'#cbd5e1\'; this.style.transform=\'translateY(-2px)\'"
                                       onmouseout="this.style.borderColor=\'#e2e8f0\'; this.style.transform=\'none\'">
                                    <div>
                                        <div style="font-weight: 600; color: #1e293b; font-size: 1rem; margin-bottom: 4px;">' . htmlspecialchars($lastTicket['subject']) . '</div>
                                        <div style="color: #64748b; font-size: 0.85rem;">Updated: ' . date('d M H:i', strtotime($lastTicket['updated_at'])) . '</div>
                                    </div>
                                    <span style="background: ' . $color . '20; color: ' . $color . '; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem; text-transform: uppercase;">
                                        ' . $lastTicket['status'] . '
                                    </span>
                                  </div>';
                        } else {
                            echo '<div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 20px; text-align: center; color: #64748b;">
                                    Belum ada aktivitas tiket.
                                  </div>';
                        }

                        echo '</div>'; // End Wrapper
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
    <!-- SEARCH & NOTIFICATION SCRIPTS -->
    <script>
        // --- SEARCH FUNCTIONALITY (LIVE PREVIEW) ---
        const searchInput = document.querySelector('.header-search input');
        const searchContainer = document.querySelector('.header-search');

        if (searchInput && searchContainer) {
            // Create Results Dropdown
            const searchResults = document.createElement('div');
            searchResults.id = 'search-results-dropdown';
            searchResults.style.cssText = 'display:none; position:absolute; top:100%; left:0; right:0; background:white; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); z-index:1000; overflow:hidden; margin-top:5px;';
            searchContainer.appendChild(searchResults);

            let debounceTimer;

            searchInput.addEventListener('input', function (e) {
                clearTimeout(debounceTimer);
                const query = e.target.value.trim();

                if (query.length < 2) {
                    searchResults.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch('?page=api_search&q=' + encodeURIComponent(query))
                        .then(res => res.json())
                        .then(data => {
                            searchResults.innerHTML = '';
                            if (data.length > 0) {
                                searchResults.style.display = 'block';
                                data.forEach(ticket => {
                                    const item = document.createElement('div');
                                    item.style.cssText = 'padding:10px 15px; border-bottom:1px solid #f1f5f9; cursor:pointer; transition:background 0.2s;';
                                    item.onmouseover = () => item.style.background = '#f8fafc';
                                    item.onmouseout = () => item.style.background = 'white';

                                    // Status Badge Color
                                    let statusColor = '#64748b';
                                    if (ticket.status === 'in_progress') statusColor = '#facc15';
                                    if (ticket.status === 'resolved') statusColor = '#10b981';

                                    item.innerHTML = `
                                        <div style="font-weight:600; font-size:0.9rem; color:#1e293b; margin-bottom:2px;">${ticket.subject}</div>
                                        <div style="display:flex; justify-content:space-between; align-items:center;">
                                            <span style="font-size:0.75rem; color:#64748b;">${ticket.formatted_date}</span>
                                            <span style="font-size:0.7rem; font-weight:700; color:${statusColor}; text-transform:uppercase;">${ticket.status}</span>
                                        </div>
                                    `;
                                    item.onclick = () => window.location.href = '?page=dashboard&action=ticket_detail&id=' + ticket.id;
                                    searchResults.appendChild(item);
                                });
                            } else {
                                searchResults.style.display = 'none';
                            }
                        })
                        .catch(err => console.error(err));
                }, 300);
            });

            // Enter Key still works for full list
            searchInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    const query = e.target.value.trim();
                    if (query) {
                        window.location.href = '?page=dashboard&action=my_tickets&search=' + encodeURIComponent(query);
                    }
                }
            });

            // Hide on click outside
            document.addEventListener('click', function (e) {
                if (!searchContainer.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });
        }

        // --- NOTIFICATION FUNCTIONALITY (Real-Time Sync) ---
        const notifyBtn = document.querySelector('.notification-btn');
        const badgeDot = document.querySelector('.badget-dot');
        // Ensure badge dot is hidden by default if not strictly 0 in CSS
        if (badgeDot) badgeDot.style.display = 'none';

        if (notifyBtn) {
            const notifDropdown = document.createElement('div');
            notifDropdown.id = 'notification-dropdown';
            notifDropdown.style.cssText = 'display: none; position: absolute; background: white; border: none; border-radius: 12px; width: 320px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); z-index: 1000; overflow: hidden; animation: fadeIn 0.15s ease-out;';
            document.body.appendChild(notifDropdown);

            function renderNotifications(list, unreadCount) {
                // Header
                let html = `
                    <div style="padding: 16px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: #1e293b;">Notifikasi ${unreadCount > 0 ? `(${unreadCount})` : ''}</span>
                        <span id="mark-all-read" style="font-size: 0.75rem; color: var(--primary); font-weight: 600; cursor: pointer;">Tandai dibaca</span>
                    </div>
                    <div style="max-height: 350px; overflow-y: auto;">
                `;

                if (list.length === 0) {
                    html += `
                        <div style="padding: 20px; text-align: center; color: #64748b;">
                            <i class="ri-notification-off-line" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p style="margin-top: 10px; font-size: 0.9rem;">Belum ada notifikasi baru.</p>
                        </div>
                    `;
                } else {
                    list.forEach(notif => {
                        const isRead = notif.is_read == 1;
                        const bg = isRead ? 'white' : '#feffff'; // Very subtle tint for unread
                        const hover = isRead ? '#f8fafc' : '#f0f9ff';
                        const iconBg = isRead ? '#f1f5f9' : '#eff6ff';
                        const iconColor = isRead ? '#64748b' : 'var(--primary)';
                        const iconClass = notif.type === 'success' ? 'ri-check-double-line' : 'ri-customer-service-2-fill';

                        html += `
                        <div onclick="window.location.href='${notif.link}'"
                             style="padding: 12px 16px; border-bottom: 1px solid #f8fafc; cursor: pointer; display: flex; gap: 12px; background: ${bg}; transition: background 0.2s;" 
                             onmouseover="this.style.background='${hover}'" onmouseout="this.style.background='${bg}'">
                            
                            <div style="width: 36px; height: 36px; background: ${iconBg}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: ${iconColor}; flex-shrink: 0;">
                                <i class="${iconClass}"></i>
                            </div>
                            
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; color: #334155; line-height: 1.4;">
                                    ${notif.message}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px;">${notif.time_ago}</div>
                            </div>

                            ${!isRead ? `<div style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%; margin-top: 6px;"></div>` : ''}
                        </div>
                        `;
                    });
                }

                html += `</div>
                    <div style="padding: 12px; text-align: center; border-top: 1px solid #f1f5f9;">
                        <a href="#" style="font-size: 0.85rem; color: var(--primary); font-weight: 600; text-decoration: none;">Lihat Semua</a>
                    </div>`;

                notifDropdown.innerHTML = html;

                // Re-attach Mark Read Listener
                const markBtn = document.getElementById('mark-all-read');
                if (markBtn) {
                    markBtn.onclick = (e) => {
                        e.stopPropagation();
                        fetch('?page=api_notifications', {
                            method: 'POST',
                            body: JSON.stringify({ action: 'mark_read' })
                        }).then(() => fetchNotifications()); // Refresh
                    };
                }
            }

            function fetchNotifications() {
                fetch('?page=api_notifications')
                    .then(res => res.json())
                    .then(data => {
                        if (badgeDot) {
                            badgeDot.style.display = data.unread_count > 0 ? 'block' : 'none';
                        }
                        // Only render if dropdown is open OR just store it? 
                        // For simplicity, re-render always so it's fresh when opened
                        renderNotifications(data.data, data.unread_count);
                    })
                    .catch(err => console.error('Notif Error:', err));
            }

            // Poll every 10 seconds
            fetchNotifications();
            setInterval(fetchNotifications, 10000);

            // Toggle
            notifyBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (notifDropdown.style.display === 'none') {
                    notifDropdown.style.display = 'block';

                    const rect = notifyBtn.getBoundingClientRect();
                    const isMobile = window.innerWidth < 768;

                    if (isMobile) {
                        // Mobile: Center and fit screen
                        notifDropdown.style.width = '92vw';
                        notifDropdown.style.left = '4vw';
                        notifDropdown.style.right = 'auto';
                        notifDropdown.style.top = '70px'; // Fixed top height
                    } else {
                        // Desktop: Align to button
                        notifDropdown.style.width = '320px';
                        notifDropdown.style.top = (rect.bottom + 15) + 'px';
                        notifDropdown.style.left = 'auto';
                        notifDropdown.style.right = (window.innerWidth - rect.right) + 'px';
                    }
                } else {
                    notifDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function (e) {
                if (!notifyBtn.contains(e.target) && !notifDropdown.contains(e.target)) {
                    notifDropdown.style.display = 'none';
                }
            });
        }
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