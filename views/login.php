<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Helpdesk</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
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

            <!-- Google Sign-In Button -->
            <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                <div id="g_id_onload"
                     data-client_id="1072069249100-ia66cdqti4ehb80hgdd4rjmet05b849l.apps.googleusercontent.com"
                     data-context="signin"
                     data-ux_mode="popup"
                     data-callback="handleCredentialResponse"
                     data-auto_prompt="false">
                </div>

                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="sign_in_with"
                     data-size="large"
                     data-logo_alignment="left">
                </div>
            </div>

            <div style="display:flex; align-items:center; margin-bottom: 20px;">
                <hr style="flex:1; border:0; border-top: 1px solid #ddd;">
                <span style="padding: 0 10px; color: #888; font-size: 0.8rem;">ATAU</span>
                <hr style="flex:1; border:0; border-top: 1px solid #ddd;">
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg">
                    <?php 
                    if($_GET['error'] == 'google_domain') echo "Gunakan email kampus (@satyaterrabhinneka.ac.id)!";
                    else echo "Username atau Password salah!"; 
                    ?>
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

    <script>
        function handleCredentialResponse(response) {
            // Kirim token JWT ke backend
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '?page=auth_google';

            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'credential';
            hiddenField.value = response.credential;

            form.appendChild(hiddenField);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>

</html>