<?php
// ACTION: handle_google_login
require_once __DIR__ . '/../../src/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['credential'])) {
    $jwt = $_POST['credential'];

    // Verifikasi Token ke Google (Tanpa Library Berat)
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $jwt;
    $response = file_get_contents($url);

    if ($response === FALSE) {
        die("Gagal verifikasi ke Google.");
    }

    $payload = json_decode($response, true);

    if (isset($payload['email'])) {
        $email = $payload['email'];
        $googleId = $payload['sub'];
        $name = $payload['name'];

        $picture = $payload['picture'] ?? null;

        // VALIDASI DOMAIN KAMPUS
        $allowedDomains = ['satyaterrabhinneka.ac.id', 'students.satyaterrabhinneka.ac.id'];
        $emailParts = explode('@', $email);
        $domain = end($emailParts);

        if (!in_array($domain, $allowedDomains)) {
            // Jika email bukan kampus, tolak!
            header('Location: ?page=login&error=google_domain');
            exit;
        }

        // CEK USER DI DATABASE
        try {
            $pdo = Database::getInstance();

            // Cek apakah email sudah terdaftar
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                // User Lama: Login langsung
                // Update avatar dan google_id
                $upd = $pdo->prepare("UPDATE users SET google_id = :gid, avatar_url = :pic WHERE id = :uid");
                $upd->execute(['gid' => $googleId, 'pic' => $picture, 'uid' => $user['id']]);

                // Update session data
                $user['avatar_url'] = $picture;
                $_SESSION['user'] = $user;
                header('Location: ?page=dashboard');
                exit;

            } else {
                // User Baru (Valid Domain Kampus): Auto Register
                $username = strstr($email, '@', true); // Ambil nama depan email sebagai username
                // Cek username duplikat (jika ada budi@staff dan budi@student)
                $chkUser = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :u");
                $chkUser->execute(['u' => $username]);
                if ($chkUser->fetchColumn() > 0) {
                    $username .= rand(100, 999); // Tambah angka random
                }

                // Default Password Random (karena login via Google)
                $randomPass = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);

                // Tentukan Role berdasarkan Domain
                $newRole = 'user'; // Default
                if (str_ends_with($email, '@satyaterrabhinneka.ac.id')) {
                    $newRole = 'staff';
                } elseif (str_ends_with($email, '@students.satyaterrabhinneka.ac.id')) {
                    $newRole = 'mahasiswa';
                }

                $sqlInsert = "INSERT INTO users (name, username, email, password, google_id, role, avatar_url) 
                              VALUES (:nm, :usr, :eml, :pass, :gid, :role, :pic)";
                $stmtIns = $pdo->prepare($sqlInsert);
                $stmtIns->execute([
                    'nm' => $name,
                    'usr' => $username,
                    'eml' => $email,
                    'pass' => $randomPass,
                    'gid' => $googleId,
                    'role' => $newRole,
                    'pic' => $picture
                ]);

                // Ambil data user yg baru dibuat
                $newUserStmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
                $newUserStmt->execute(['email' => $email]);
                $newUser = $newUserStmt->fetch();

                $_SESSION['user'] = $newUser;
                header('Location: ?page=dashboard&welcome=1');
                exit;
            }

        } catch (Exception $e) {
            die("Database Error: " . $e->getMessage());
        }

    } else {
        die("Invalid Google Token");
    }
}
