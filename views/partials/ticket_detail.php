<?php
// Calculate colors/badges based on status
$statusConfig = [
    'pending' => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fdba74', 'icon' => 'ri-time-line'],
    'in_progress' => ['bg' => '#eff6ff', 'text' => '#1d4ed8', 'border' => '#93c5fd', 'icon' => 'ri-loader-4-line'],
    'resolved' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#86efac', 'icon' => 'ri-checkbox-circle-line'],
    'rejected' => ['bg' => '#fef2f2', 'text' => '#b91c1c', 'border' => '#fca5a5', 'icon' => 'ri-close-circle-line'],
];
$config = $statusConfig[$ticket['status']] ?? $statusConfig['pending'];
?>

<div style="max-width: 900px; margin: 0 auto; padding-bottom: 50px;">

    <!-- Breadcrumb / Back Button -->
    <div style="margin-bottom: 20px;">
        <a href="?page=dashboard&action=<?php echo ($currentUser['role'] === 'admin') ? 'incoming_tickets' : 'my_tickets'; ?>"
            style="text-decoration: none; color: var(--secondary); display: inline-flex; align-items: center; font-weight: 500; font-size: 0.9rem; transition: 0.2s;">
            <i class="ri-arrow-left-line" style="margin-right: 5px;"></i> Kembali ke Daftar
        </a>
    </div>

    <?php if (isset($ticket)): ?>

        <!-- Main Card -->
        <div
            style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid var(--border); overflow: hidden;">

            <style>
                /* --- GENERAL TICKET STYLES --- */
                .ticket-title {
                    font-size: 1.75rem;
                    font-weight: 700;
                    color: var(--text-main);
                    margin: 0;
                    line-height: 1.3;
                    word-wrap: break-word;
                    word-break: break-word;
                }

                .ticket-header-flex {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-start;
                    gap: 20px;
                }

                .ticket-card-padding {
                    padding: 24px 32px;
                }

                .local-description-box {
                    background: #fafafa;
                    border: 1px solid var(--border);
                    border-radius: 8px;
                    padding: 20px;
                    line-height: 1.7;
                    color: #334155;
                    font-size: 1rem;
                    white-space: pre-wrap;
                    font-family: 'Inter', sans-serif;
                    text-align: left;
                    word-wrap: break-word;
                }

                /* --- CHAT SYSTEM STYLES --- */
                .chat-container {
                    display: flex;
                    gap: 12px;
                    margin-bottom: 4px;
                    width: 100%;
                }

                .chat-container.me {
                    justify-content: flex-end;
                }

                .chat-container.other {
                    justify-content: flex-start;
                }

                .chat-avatar {
                    flex-shrink: 0;
                    width: 38px;
                    height: 38px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: bold;
                    font-size: 0.9rem;
                    align-self: flex-start;
                    margin-top: 4px;
                }

                .chat-avatar.me {
                    background: #bfdbfe;
                    color: #1d4ed8;
                }

                .chat-avatar.other {
                    background: #e0e7ff;
                    color: #4338ca;
                }

                .chat-bubble-wrapper {
                    max-width: 75%;
                    min-width: auto;
                    display: flex;
                    flex-direction: column;
                }

                .chat-container.me .chat-bubble-wrapper {
                    align-items: flex-end;
                }

                .chat-container.other .chat-bubble-wrapper {
                    align-items: flex-start;
                }

                .chat-bubble {
                    padding: 10px 20px 12px;
                    border-radius: 16px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                    width: 100%;
                }

                .chat-bubble.me {
                    background: #3b82f6;
                    color: white;
                    border-bottom-right-radius: 2px;
                }

                .chat-bubble.other {
                    background: #f1f5f9;
                    color: #1e293b;
                    border-bottom-left-radius: 2px;
                }

                .chat-header {
                    margin-bottom: 0px;
                    font-size: 0.8rem;
                    font-weight: bold;
                }

                .chat-bubble.me .chat-header {
                    color: #bfdbfe;
                }

                .chat-bubble.other .chat-header {
                    color: #64748b;
                }

                .chat-body {
                    font-size: 0.95rem;
                    line-height: 1.6;
                    word-wrap: break-word;
                    text-align: justify !important;
                }

                .chat-footer {
                    margin-top: 8px;
                    font-size: 0.65rem;
                    opacity: 0.85;
                }

                .chat-bubble.me .chat-footer {
                    color: #bfdbfe;
                    text-align: right;
                }

                .chat-bubble.other .chat-footer {
                    color: #64748b;
                    text-align: left;
                }

                /* MOBILE TWEAKS */
                @media (max-width: 600px) {
                    .ticket-header-flex {
                        flex-direction: column;
                        align-items: flex-start;
                    }

                    .ticket-title {
                        font-size: 1.25rem !important;
                    }

                    .ticket-card-padding {
                        padding: 20px 15px !important;
                    }

                    .local-description-box {
                        padding: 15px !important;
                        font-size: 0.95rem !important;
                    }
                }
            </style>

            <!-- Header Section -->
            <div class="ticket-card-padding" style="border-bottom: 1px solid var(--border); background: #fdfdfd;">
                <div class="ticket-header-flex">
                    <div style="width: 100%;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; flex-wrap: wrap;">
                            <span
                                style="font-family: monospace; color: var(--text-muted); font-size: 0.9rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                                #<?php echo $ticket['id']; ?>
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">
                                <?php echo date('d F Y, H:i', strtotime($ticket['created_at'])); ?> WIB
                            </span>
                        </div>
                        <h1 class="ticket-title">
                            <?php echo htmlspecialchars($ticket['subject']); ?>
                        </h1>
                    </div>

                    <!-- Status Badge -->
                    <div
                        style="background: <?php echo $config['bg']; ?>; color: <?php echo $config['text']; ?>; border: 1px solid <?php echo $config['border']; ?>; padding: 6px 16px; border-radius: 99px; display: flex; align-items: center; gap: 6px; white-space: nowrap; font-weight: 600; font-size: 0.85rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); flex-shrink: 0;">
                        <i class="<?php echo $config['icon']; ?>"></i>
                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                    </div>
                </div>

                <!-- Requester Info (Admin only) -->
                <?php if ($currentUser['role'] === 'admin'): ?>
                    <div style="margin-top: 16px; display: flex; align-items: center; gap: 10px;">
                        <div
                            style="width: 32px; height: 32px; background: #e0e7ff; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem;">
                            <?php echo strtoupper(substr($ticket['requester_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-main);">
                                <?php echo htmlspecialchars($ticket['requester_name']); ?>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                <?php echo htmlspecialchars($ticket['requester_email']); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Content Body -->
            <div style="padding: 32px;">
                <h3
                    style="font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-file-text-line" style="color: var(--secondary);"></i> Deskripsi Tiket
                </h3>

                <?php
                // Clean Description Logic
                $cleanDesc = str_replace(['. ', ".\r\n", ".\n"], [' ', "\r\n", "\n"], $ticket['description']);
                $cleanDesc = rtrim($cleanDesc, '.');
                ?>
                <div class="local-description-box"><?php echo htmlspecialchars($cleanDesc); ?></div>

                <!-- Comments Section -->
                <div style="margin-top: 40px; border-top: 1px solid var(--border); padding-top: 30px;">
                    <h3
                        style="font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                        <i class="ri-chat-history-line" style="color: var(--secondary);"></i> Diskusi / Balasan
                    </h3>

                    <!-- Comments List -->
                    <div id="chat-history-container" class="comments-list"
                        style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 30px;">
                        <?php if (empty($comments)): ?>
                            <div
                                style="text-align: center; color: var(--text-muted); font-style: italic; padding: 20px; background: #f8fafc; border-radius: 8px;">
                                Belum ada balasan untuk tiket ini.
                            </div>
                        <?php else: ?>
                            <?php foreach ($comments as $c): ?>
                                <?php
                                $isMe = ($c['user_id'] == $currentUser['id']);
                                $roleClass = $isMe ? 'me' : 'other';
                                ?>
                                <div class="chat-container <?php echo $roleClass; ?>">

                                    <!-- AVATAR LEFT (Other) -->
                                    <?php if (!$isMe): ?>
                                        <div class="chat-avatar other">
                                            <?php echo strtoupper(substr($c['user_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="chat-bubble-wrapper">
                                        <div class="chat-bubble <?php echo $roleClass; ?>">
                                            <!-- NAME -->
                                            <div class="chat-header">
                                                <?php echo htmlspecialchars($c['user_name']); ?>
                                            </div>
                                            <!-- BODY (Compact one-liner to avoid whitespace) -->
                                            <div class="chat-body"><?php echo nl2br(htmlspecialchars(trim($c['comment']))); ?></div>
                                            <!-- DATE -->
                                            <div class="chat-footer">
                                                <?php echo date('d M H:i', strtotime($c['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- AVATAR RIGHT (Me) -->
                                    <?php if ($isMe): ?>
                                        <div class="chat-avatar me">
                                            <?php echo strtoupper(substr($c['user_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- AJAX AUTO REFRESH SCRIPT -->
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            const ticketId = <?php echo $ticket['id']; ?>;
                            const container = document.getElementById("chat-history-container");

                            function loadComments() {
                                fetch('?page=fetch_comments&ticket_id=' + ticketId)
                                    .then(response => response.text())
                                    .then(data => {
                                        // Update content only if different? 
                                        // For simplicity and to avoid glitch, we just replace.
                                        // A better way is to verify if content changed length, but simple replace is OK for MVP.
                                        container.innerHTML = data;
                                    })
                                    .catch(err => console.error("Error fetching comments:", err));
                            }

                            // Refresh every 3 seconds
                            setInterval(loadComments, 3000);
                        });
                    </script>

                    <!-- Reply Form -->
                    <?php if ($ticket['status'] !== 'resolved' && $ticket['status'] !== 'rejected'): ?>
                        <form action="?page=add_comment" method="POST"
                            style="background: #fff; border: 1px solid var(--border); padding: 15px; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);">
                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

                            <label
                                style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--text-main); font-size: 0.9rem;">Tulis
                                Balasan</label>

                            <textarea name="comment" required placeholder="Ketik pesan Anda..."
                                style="width: 100%; border: 1px solid #cbd5e1; padding: 12px; outline: none; resize: vertical; min-height: 100px; font-family: inherit; font-size: 0.95rem; border-radius: 8px; transition: border-color 0.2s; background: #f8fafc;"></textarea>

                            <div style="margin-top: 10px; display: flex; justify-content: flex-end;">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 24px; border-radius: 8px;">
                                    <i class="ri-send-plane-fill" style="margin-right: 8px;"></i> Kirim
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div
                            style="text-align: center; padding: 15px; background: #f8fafc; border-radius: 8px; color: var(--text-muted);">
                            <i class="ri-lock-line"></i> Tiket ini telah ditutup. Tidak dapat menambah komentar baru.
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Admin Actions Footer -->
            <?php if ($currentUser['role'] === 'admin'): ?>
                <div style="background: #f8fafc; border-top: 1px solid var(--border); padding: 20px 32px;">
                    <h4
                        style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                        Update Status Tiket</h4>
                    <form action="?page=update_ticket" method="POST"
                        style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                        <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">

                        <div style="position: relative;">
                            <select name="status"
                                style="appearance: none; background: white; border: 1px solid var(--border); padding: 10px 40px 10px 15px; border-radius: 6px; font-size: 0.95rem; color: var(--text-main); outline: none; cursor: pointer; min-width: 200px;">
                                <option value="pending" <?php echo ($ticket['status'] == 'pending') ? 'selected' : ''; ?>>🕒
                                    Pending</option>
                                <option value="in_progress" <?php echo ($ticket['status'] == 'in_progress') ? 'selected' : ''; ?>>
                                    🛠️ Sedang Diproses</option>
                                <option value="resolved" <?php echo ($ticket['status'] == 'resolved') ? 'selected' : ''; ?>>✅
                                    Selesai</option>
                                <option value="rejected" <?php echo ($ticket['status'] == 'rejected') ? 'selected' : ''; ?>>🚫
                                    Ditolak</option>
                            </select>
                            <i class="ri-arrow-down-s-line"
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none;"></i>
                        </div>

                        <button type="submit" class="btn btn-primary"
                            style="padding: 10px 24px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                            <i class="ri-save-line" style="margin-right: 6px;"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            <?php endif; ?>

        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div
                style="background: #fee2e2; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="ri-error-warning-line" style="font-size: 3rem; color: #ef4444;"></i>
            </div>
            <h3 style="color: var(--text-main); font-size: 1.5rem; margin-bottom: 10px;">Tiket Tidak Ditemukan</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Tiket yang Anda cari mungkin telah dihapus atau Anda
                tidak memiliki akses.</p>
            <a href="?page=dashboard" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
    <?php endif; ?>
</div>