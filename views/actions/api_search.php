<?php
// Simple API for Live Search
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

require_once __DIR__ . '/../../src/Database.php';

$userId = $_SESSION['user']['id'];
$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = Database::getInstance();
    $sql = "SELECT id, subject, status, created_at FROM tickets 
            WHERE user_id = :uid 
            AND (subject LIKE :q OR description LIKE :q) 
            ORDER BY created_at DESC LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'uid' => $userId,
        'q' => "%$query%"
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format details
    foreach ($results as &$row) {
        $row['formatted_date'] = date('d M', strtotime($row['created_at']));
    }

    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
