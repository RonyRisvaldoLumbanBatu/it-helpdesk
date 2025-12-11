<div class="auth-card" style="max-width: 100%; box-shadow: none; border: 1px solid var(--border); padding: 2rem;">
    <h2 class="mb-4">Pengajuan Reset Password Email</h2>

    <?php if (isset($_GET['success'])): ?>
        <div
            style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bbf7d0;">
            <i class="ri-checkbox-circle-fill"></i> Tiket berhasil dikirim! Tim IT akan segera memprosesnya.
        </div>
    <?php endif; ?>

    <form action="?page=submit_ticket" method="POST">

        <div class="form-group mb-4">
            <label>Email Kantor</label>
            <input type="email" class="form-control" name="email" required placeholder="nama@perusahaan.com">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">*Gunakan email kantor yang
                valid</div>
        </div>

        <div class="form-group mb-4">
            <label>Alasan Pengajuan</label>
            <textarea class="form-control" name="reason" rows="3"
                placeholder="Jelaskan kenapa perlu reset password..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="ri-send-plane-fill" style="margin-right: 8px;"></i> Kirim Pengajuan
        </button>
    </form>
</div>
</div>