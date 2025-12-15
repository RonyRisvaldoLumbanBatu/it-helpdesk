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
        // --- FETCH TICKET DETAIL ---
        if ($content === 'ticket_detail') {
            $ticketId = $_GET['id'] ?? 0;
            require_once __DIR__ . '/../src/Database.php';
            try {
                $pdo = Database::getInstance();
                $stmt = $pdo->prepare("
                    SELECT t.*, u.name as requester_name, u.email as requester_email 
                    FROM tickets t 
                    JOIN users u ON t.user_id = u.id 
                    WHERE t.id = :id
                ");
                $stmt->execute(['id' => $ticketId]);
                $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

                // Access Control
                if (!$ticket || ($currentUser['role'] !== 'admin' && $ticket['user_id'] != $currentUser['id'])) {
                    $ticket = null; // Access denied / Not found
                    $comments = [];
                } else {
                    // Fetch Comments
                    $stmtC = $pdo->prepare("
                        SELECT c.*, u.name as user_name, u.role as user_role 
                        FROM ticket_comments c 
                        JOIN users u ON c.user_id = u.id 
                        WHERE c.ticket_id = :tid 
                        ORDER BY c.created_at ASC
                    ");
                    $stmtC->execute(['tid' => $ticketId]);
                    $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);
                }
            } catch (Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
        // ---------------------------

        require_once __DIR__ . '/../views/dashboard.php';
        break;

    case 'submit_ticket':
        require_once __DIR__ . '/../views/actions/submit_ticket.php';
        break;

    case 'update_ticket':
        require_once __DIR__ . '/../views/actions/update_ticket.php';
        break;



    case 'add_comment':
        require_once __DIR__ . '/../views/actions/add_comment.php';
        break;

    case 'create_user':
        require_once __DIR__ . '/../views/actions/create_user.php';
        break;

    case 'update_user':
        require_once __DIR__ . '/../views/actions/update_user.php';
        break;

    case 'delete_user':
        require_once __DIR__ . '/../views/actions/delete_user.php';
        break;

    case 'auth_google':
        require_once __DIR__ . '/../views/actions/auth_google.php';
        break;




    default:
        view('login');
        break;
}

