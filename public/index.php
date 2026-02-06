<?php
session_start();

// ========================================
// CSRF PROTECTION FUNCTIONS
// ========================================
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Generate CSRF Token untuk halaman ini
$csrfToken = generateCsrfToken();
// ========================================

// ========================================
// ROUTING CONFIGURATION
// ========================================
$routes = [
    // Public Routes (No Auth Required)
    'login' => [
        'view' => 'login',
        'auth' => false,
        'redirect_if_logged_in' => 'dashboard'
    ],
    
    // Special Handlers (Custom Logic)
    'auth_check' => [
        'handler' => 'handleAuthCheck'
    ],
    'auth_google' => [
        'action' => 'auth_google'
    ],
    'logout' => [
        'handler' => 'handleLogout'
    ],
    
    // Protected Routes (Auth Required)
    'dashboard' => [
        'view' => 'dashboard',
        'auth' => true,
        'handler' => 'handleDashboard'
    ],
    
    // Action Routes (Auth Required)
    'submit_ticket' => [
        'action' => 'submit_ticket',
        'auth' => true
    ],
    'update_ticket' => [
        'action' => 'update_ticket',
        'auth' => true
    ],
    'add_comment' => [
        'action' => 'add_comment',
        'auth' => true
    ],
    'fetch_comments' => [
        'action' => 'fetch_comments',
        'auth' => true
    ],
    'api_search' => [
        'action' => 'api_search',
        'auth' => true
    ],
    'api_notifications' => [
        'action' => 'api_notifications',
        'auth' => true
    ],
    'create_user' => [
        'action' => 'create_user',
        'auth' => true
    ],
    'update_user' => [
        'action' => 'update_user',
        'auth' => true
    ],
    'delete_user' => [
        'action' => 'delete_user',
        'auth' => true
    ]
];

// ========================================
// HELPER FUNCTIONS
// ========================================
function isAuthenticated() {
    return isset($_SESSION['user']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ?page=login');
        exit;
    }
}

function view($viewName) {
    global $csrfToken, $currentUser, $content, $ticket, $comments; // Make variables available to views
    require_once __DIR__ . '/../views/' . $viewName . '.php';
}

function loadAction($actionName) {
    require_once __DIR__ . '/../views/actions/' . $actionName . '.php';
}

// ========================================
// ROUTE HANDLERS
// ========================================
function handleAuthCheck() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ?page=login');
        exit;
    }
    
    require_once __DIR__ . '/../src/Database.php';

    // CSRF Token Validation
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($submittedToken)) {
        die("Invalid security token. Please try again.");
    }

    $username = $_POST['username'] ?? '';

    try {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['password'], $user['password'])) {
            unset($user['password']);
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

function handleLogout() {
    session_destroy();
    header('Location: ?page=login');
    exit;
}

function handleDashboard() {
    global $csrfToken, $currentUser, $content, $ticket, $comments;
    requireAuth();
    
    $currentUser = $_SESSION['user'];
    $content = $_GET['action'] ?? 'home';
    
    // Fetch ticket detail if needed
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
                $ticket = null;
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
    } else {
        // Initialize empty untuk view lain
        $ticket = null;
        $comments = [];
    }
    
    view('dashboard');
}

// ========================================
// ROUTING DISPATCHER
// ========================================
$page = $_GET['page'] ?? 'login';

// Check if route exists
if (!isset($routes[$page])) {
    $page = 'login'; // Default fallback
}

$route = $routes[$page];

// Check authentication requirement
if (isset($route['auth']) && $route['auth'] === true) {
    requireAuth();
}

// Redirect if already logged in (for login page)
if (isset($route['redirect_if_logged_in']) && isAuthenticated()) {
    header('Location: ?page=' . $route['redirect_if_logged_in']);
    exit;
}

// Execute route
if (isset($route['handler'])) {
    // Custom handler function
    $handlerFunction = $route['handler'];
    $handlerFunction();
} elseif (isset($route['action'])) {
    // Load action file
    loadAction($route['action']);
} elseif (isset($route['view'])) {
    // Load view file
    view($route['view']);
} else {
    // Fallback
    view('login');
}


