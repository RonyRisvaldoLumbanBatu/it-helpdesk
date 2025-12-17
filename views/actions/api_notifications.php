<?php
// Session already started in index.php
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../src/Database.php';
$userId = $_SESSION['user']['id'];
$pdo = Database::getInstance();

// ACTION: MARK AS READ
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action']) && $data['action'] === 'mark_read') {
        // Mark all as read for user
        $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = :uid";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);
        echo json_encode(['success' => true]);
        exit;
    }
}

// ACTION: FETCH NOTIFICATIONS (GET)
try {
    // Fetch unread count
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0");
    $stmtCount->execute(['uid' => $userId]);
    $unreadCount = $stmtCount->fetchColumn();

    // Fetch latest 10 items
    $sql = "SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT 10";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['uid' => $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format relative time (simple version)
    foreach ($notifications as &$notif) {
        $time = strtotime($notif['created_at']);
        $diff = time() - $time;
        if ($diff < 60)
            $notif['time_ago'] = 'Baru saja';
        elseif ($diff < 3600)
            $notif['time_ago'] = floor($diff / 60) . ' menit yang lalu';
        elseif ($diff < 86400)
            $notif['time_ago'] = floor($diff / 3600) . ' jam yang lalu';
        else
            $notif['time_ago'] = floor($diff / 86400) . ' hari yang lalu';
    }

    echo json_encode([
        'unread_count' => (int) $unreadCount,
        'data' => $notifications
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
