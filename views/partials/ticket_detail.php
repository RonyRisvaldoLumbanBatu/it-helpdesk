<div class="card" style="max-width: 800px; margin: 0 auto; border-top: 4px solid var(--primary);">
    <?php if (isset($ticket)): ?>
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: start;">
            <div>
                <h4 style="margin: 0; color: #64748b; font-size: 0.9rem;">#<?php echo $ticket['id']; ?> |
                    <?php echo date('d M Y H:i', strtotime($ticket['created_at'])); ?></h4>
                <h2 style="margin: 10px 0; color: #1e293b;"><?php echo htmlspecialchars($ticket['subject']); ?></h2>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="status-badge status-<?php echo $ticket['status']; ?>">
                        <?php echo ucfirst($ticket['status']); ?>
                    </span>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <span style="font-size: 0.9rem; color: #64748b;">
                            Oleh: <strong><?php echo htmlspecialchars($ticket['requester_name']); ?></strong>
                            (<?php echo htmlspecialchars($ticket['requester_email']); ?>)
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="text-align: right;">
                <a href="?page=dashboard&action=<?php echo ($currentUser['role'] === 'admin') ? 'incoming_tickets' : 'my_tickets'; ?>"
                    style="text-decoration: none; color: #64748b; font-size: 0.9rem; border: 1px solid #cbd5e1; padding: 5px 10px; border-radius: 4px; display: inline-flex; align-items: center;">
                    <i class="ri-arrow-left-line" style="margin-right: 5px;"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body" style="margin-top: 20px;">
            <p style="color: #64748b; font-weight: 600; margin-bottom: 5px;">Deskripsi Masalah:</p>
            <div
                style="background: #f8fafc; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; color: #334155; line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
            </div>
        </div>

        <?php if ($currentUser['role'] === 'admin'): ?>
            <div class="card-footer" style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <h4 style="margin-bottom: 10px; color: #334155;">Tindakan Admin</h4>
                <form action="?page=update_ticket" method="POST" style="display: flex; gap: 10px; align-items: center;">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                    <select name="status" style="padding: 8px; border-radius: 4px; border: 1px solid #cbd5e1;">
                        <option value="pending" <?php echo ($ticket['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="in_progress" <?php echo ($ticket['status'] == 'in_progress') ? 'selected' : ''; ?>>In
                            Progress</option>
                        <option value="resolved" <?php echo ($ticket['status'] == 'resolved') ? 'selected' : ''; ?>>Resolved
                        </option>
                        <option value="rejected" <?php echo ($ticket['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected
                        </option>
                    </select>
                    <button type="submit" class="btn btn-primary" style="padding: 8px 20px;">Update Status</button>
                </form>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div style="text-align: center; padding: 40px;">
            <i class="ri-error-warning-line" style="font-size: 3rem; color: #cbd5e1;"></i>
            <h3>Tiket tidak ditemukan</h3>
            <p>Atau Anda tidak memiliki akses ke tiket ini.</p>
            <a href="?page=dashboard" class="btn btn-primary" style="margin-top: 10px;">Kembali ke Dashboard</a>
        </div>
    <?php endif; ?>
</div>