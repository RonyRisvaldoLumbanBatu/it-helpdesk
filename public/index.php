<?php
session_start();

// Simple Router
$page = $_GET['page'] ?? 'login';

// --- AUTHENTICATION LOGIC ---
if ($page === 'auth_check' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Hardcoded Users untuk sementara
    // Format: username => [password, role, display_name]
    $users = [
        'admin' => ['password' => 'admin123', 'role' => 'admin', 'name' => 'Administrator'],
        'user' => ['password' => 'user123', 'role' => 'user', 'name' => 'User Staff']
    ];

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user'] = $users[$username];
        header('Location: ?page=dashboard');
        exit;
    } else {
        // Login gagal
        header('Location: ?page=login&error=1');
        exit;
    }
}

if ($page === 'logout') {
    session_destroy();
    header('Location: ?page=login');
    exit;
}
// -----------------------------

function view($viewName)
{
    require_once __DIR__ . '/../views/' . $viewName . '.php';
}

switch ($page) {
    case 'login':
        // Jika sudah login, lempar ke dashboard
        if (isset($_SESSION['user'])) {
            header('Location: ?page=dashboard');
            exit;
        }
        view('login');
        break;

    case 'dashboard':
        // Cek apakah sudah login
        if (!isset($_SESSION['user'])) {
            header('Location: ?page=login');
            exit;
        }

        $currentUser = $_SESSION['user'];

        $content = 'home';
        if (isset($_GET['action'])) {
            $content = $_GET['action'];
        }

        require_once __DIR__ . '/../views/dashboard.php';
        break;

    default:
        view('login');
        break;
}
