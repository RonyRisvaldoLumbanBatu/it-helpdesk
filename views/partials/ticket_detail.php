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
        <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid var(--border); overflow: hidden;">
            
            <!-- Header Section -->
            <div style="padding: 24px 32px; border-bottom: 1px solid var(--border); background: #fdfdfd;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                            <span style="font-family: monospace; color: var(--text-muted); font-size: 0.9rem; background: #f1f5f9; padding: 2px 8px; border-radius: 4px;">
                                #<?php echo $ticket['id']; ?>
                            </span>
                            <span style="color: var(--text-muted); font-size: 0.85rem;">
                                <?php echo date('d F Y, H:i', strtotime($ticket['created_at'])); ?> WIB
                            </span>
                        </div>
                        <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--text-main); margin: 0; line-height: 1.3;">
                            <?php echo htmlspecialchars($ticket['subject']); ?>
                        </h1>
                    </div>
                    
                    <!-- Status Badge -->
                    <div style="background: <?php echo $config['bg']; ?>; color: <?php echo $config['text']; ?>; border: 1px solid <?php echo $config['border']; ?>; padding: 6px 16px; border-radius: 99px; display: flex; align-items: center; gap: 6px; white-space: nowrap; font-weight: 600; font-size: 0.85rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                        <i class="<?php echo $config['icon']; ?>"></i>
                        <?php echo ucfirst(str_replace('_', ' ', $ticket['status'])); ?>
                    </div>
                </div>

                <!-- Requester Info (Admin only) -->
                <?php if ($currentUser['role'] === 'admin'): ?>
                <div style="margin-top: 16px; display: flex; align-items: center; gap: 10px;">
                    <div style="width: 32px; height: 32px; background: #e0e7ff; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem;">
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
                <h3 style="font-size: 1rem; font-weight: 600; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-file-text-line" style="color: var(--secondary);"></i> Deskripsi Tiket
                </h3>
                <div style="background: #fafafa; border: 1px solid var(--border); border-radius: 8px; padding: 20px; line-height: 1.7; color: #334155; font-size: 1rem; white-space: pre-wrap;font-family: 'Inter', sans-serif;">
<?php echo htmlspecialchars($ticket['description']); ?>
                </div>

                <!-- Placeholder for future Comments/Reply -->
                <div style="margin-top: 30px; padding: 20px; text-align: center; border: 2px dashed #e2e8f0; border-radius: 8px; color: #94a3b8;">
                    <i class="ri-chat-smile-2-line" style="font-size: 2rem; margin-bottom: 8px; display: block;"></i>
                    <p>Fitur Balasan & Komentar akan segera hadir.</p>
                </div>
            </div>

            <!-- Admin Actions Footer -->
            <?php if ($currentUser['role'] === 'admin'): ?>
            <div style="background: #f8fafc; border-top: 1px solid var(--border); padding: 20px 32px;">
                <h4 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">Update Status Tiket</h4>
                <form action="?page=update_ticket" method="POST" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                    
                    <div style="position: relative;">
                        <select name="status" style="appearance: none; background: white; border: 1px solid var(--border); padding: 10px 40px 10px 15px; border-radius: 6px; font-size: 0.95rem; color: var(--text-main); outline: none; cursor: pointer; min-width: 200px;">
                            <option value="pending" <?php echo ($ticket['status'] == 'pending') ? 'selected' : ''; ?>>🕒 Pending</option>
                            <option value="in_progress" <?php echo ($ticket['status'] == 'in_progress') ? 'selected' : ''; ?>>🛠️ Sedang Diproses</option>
                            <option value="resolved" <?php echo ($ticket['status'] == 'resolved') ? 'selected' : ''; ?>>✅ Selesai</option>
                            <option value="rejected" <?php echo ($ticket['status'] == 'rejected') ? 'selected' : ''; ?>>🚫 Ditolak</option>
                        </select>
                        <i class="ri-arrow-down-s-line" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none;"></i>
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 10px 24px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                        <i class="ri-save-line" style="margin-right: 6px;"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
            <?php endif; ?>

        </div>

    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px;">
            <div style="background: #fee2e2; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="ri-error-warning-line" style="font-size: 3rem; color: #ef4444;"></i>
            </div>
            <h3 style="color: var(--text-main); font-size: 1.5rem; margin-bottom: 10px;">Tiket Tidak Ditemukan</h3>
            <p style="color: var(--text-muted); margin-bottom: 20px;">Tiket yang Anda cari mungkin telah dihapus atau Anda tidak memiliki akses.</p>
            <a href="?page=dashboard" class="btn btn-primary">Kembali ke Dashboard</a>
        </div>
    <?php endif; ?>
</div>