<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Helpdesk</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-msg {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }

        .demo-info {
            margin-bottom: 20px;
            background: #e0e7ff;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #4338ca;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-title">IT Helpdesk</div>
                <div class="auth-subtitle">Masuk untuk mengakses layanan</div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg">
                    Username atau Password salah!
                </div>
            <?php endif; ?>

            <div class="demo-info">
                <strong>Default Accounts (Database):</strong><br>
                Admin: admin / password123<br>
                User: user / password123
            </div>


            <form action="?page=auth_check" method="POST">
                <div class="form-group mb-4">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Masukkan ID Anda" required>
                </div>

                <div class="form-group mb-4">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-full">Masuk Dashboard</button>
            </form>
        </div>
    </div>
</body>

</html>