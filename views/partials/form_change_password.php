<div style="max-width: 600px;">
    <div class="auth-card" style="max-width: 100%; box-shadow: none; border: 1px solid var(--border); padding: 2rem;">
        <h2 class="mb-4">Pengajuan Reset Password Email</h2>
        <form action="" method="POST">
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