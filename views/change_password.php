<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Ganti Password | IT Helpdesk</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="auth-container">
        <div class="auth-header">
            <div class="logo-placeholder">🔒</div>
            <h1>Reset Password</h1>
            <p>Ajukan permohonan reset password email kantor Anda</p>
        </div>

        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email Kantor</label>
                <input type="email" id="email" name="email" class="form-input" required
                    placeholder="nama@perusahaan.com">
            </div>

            <div class="form-group">
                <label for="reason">Alasan Pengajuan</label>
                <input type="text" id="reason" name="reason" class="form-input" required
                    placeholder="Contoh: Lupa password, Akun terkunci">
            </div>

            <button type="submit" class="btn-primary">Kirim Pengajuan</button>
        </form>
    </div>
</body>

</html>