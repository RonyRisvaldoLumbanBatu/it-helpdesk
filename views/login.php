<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IT Helpdesk</title>
    <link rel="icon" href="assets/images/logo.png" type="image/png">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('assets/images/bg_campus.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .auth-wrapper {
            background: rgba(15, 23, 42, 0.6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo {
            width: 80px;
            height: auto;
            margin-bottom: 15px;
        }

        .auth-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 0.95rem;
            color: #64748b;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.8);
        }

        .demo-info {
            background: rgba(224, 231, 255, 0.8);
            color: #4338ca;
            padding: 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-msg {
            background: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <img src="assets/images/logo.png" alt="Logo" class="auth-logo">
                <div class="auth-title">IT Helpdesk</div>
                <div class="auth-subtitle">Masuk untuk mengakses layanan</div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-msg">
                    <?php
                    if ($_GET['error'] == 'google_domain')
                        echo "Gunakan email kampus (@satyaterrabhinneka.ac.id)!";
                    else
                        echo "Username atau Password salah!";
                    ?>
                </div>
            <?php endif; ?>




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

            <div style="display:flex; align-items:center; margin: 25px 0;">
                <hr style="flex:1; border:0; border-top: 1px solid #cbd5e1;">
                <span style="padding: 0 10px; color: #64748b; font-size: 0.8rem; font-weight: 500;">ATAU MASUK
                    DENGAN</span>
                <hr style="flex:1; border:0; border-top: 1px solid #cbd5e1;">
            </div>

            <!-- Google Sign-In Button -->
            <div style="margin-bottom: 20px; display: flex; justify-content: center;">
                <div id="g_id_onload"
                    data-client_id="1072069249100-ia66cdqti4ehb80hgdd4rjmet05b849l.apps.googleusercontent.com"
                    data-context="signin" data-ux_mode="popup" data-callback="handleCredentialResponse"
                    data-auto_prompt="false">
                </div>

                <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
                    data-text="sign_in_with" data-size="large" data-logo_alignment="left" data-width="340">
                </div>
            </div>
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
    <div style="margin-top: 20px; text-align: center; position: fixed; bottom: 10px; width: 100%;">
        <a href="?page=dev_login"
            style="color: #94a3b8; font-size: 0.8rem; text-decoration: none; padding: 5px; border: 1px dashed #cbd5e1; border-radius: 4px;">🛠
            Login Testing (HP)</a>
    </div>
</body>

</html>