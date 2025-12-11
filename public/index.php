<?php
session_start();

// Simple Router
$page = $_GET['page'] ?? 'login';

// --- AUTHENTICATION LOGIC ---
if ($page === 'auth_check' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../src/Database.php';

    $username = $_POST['username'] ?? '';
    // $passwordInput = $_POST['password'] ?? ''; 

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi password database
        if ($user && password_verify($_POST['password'], $user['password'])) {
            unset($user['password']); // Hapus hash dari session
            $_SESSION['user'] = $user;
            header('Location: ?page=dashboard');
            exit;
        } else {
            header('Location: ?page=login&error=1');
            exit;
        }
    } catch (Exception $e) {
        die("Error DB: " . $e->getMessage());
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
        if (isset($_SESSION['user'])) {
            header('Location: ?page=dashboard');
            exit;
        }
        view('login');
        break;

    case 'dashboard':
        if (!isset($_SESSION['user'])) {
            header('Location: ?page=login');
            exit;
        }
        $currentUser = $_SESSION['user'];
        $content = $_GET['action'] ?? 'home';
        require_once __DIR__ . '/../views/dashboard.php';
        break;

    case 'submit_ticket':
        require_once __DIR__ . '/../views/actions/submit_ticket.php';
        break;

    case 'update_ticket':
        require_once __DIR__ . '/../views/actions/update_ticket.php';
        break;


    default:
        view('login');
        break;
}

