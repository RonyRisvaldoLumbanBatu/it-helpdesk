<?php
// views/actions/fetch_comments.php

require_once __DIR__ . '/../../src/Database.php';

$ticketId = $_GET['ticket_id'] ?? 0;
// Note: In production, verify user has access to this ticket (security)
// For now we assume if they can hit this, they are logged in (checked by index.php)

try {
    $pdo = Database::getInstance();

    // Get Current User ID for styling 'Me' vs 'Other'
    $currentUserId = $_SESSION['user']['id'];

    $stmtC = $pdo->prepare("
        SELECT c.*, u.name as user_name, u.role as user_role 
        FROM ticket_comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.ticket_id = :tid 
        ORDER BY c.created_at ASC
    ");
    $stmtC->execute(['tid' => $ticketId]);
    $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);

    if (empty($comments)) {
        echo '<div style="text-align: center; color: var(--text-muted); font-style: italic; padding: 20px; background: #f8fafc; border-radius: 8px;">Belum ada balasan untuk tiket ini.</div>';
    } else {
        foreach ($comments as $c) {
            $isMe = ($c['user_id'] == $currentUserId);
            $roleClass = $isMe ? 'me' : 'other';
            $bubbleBg = $isMe ? '#3b82f6' : '#f1f5f9';

            // Output HTML directly (Matching exactly what is in ticket_detail.php)
            ?>
            <div class="chat-container <?php echo $roleClass; ?>">
                <!-- AVATAR LEFT (Other) -->
                <?php if (!$isMe): ?>
                    <div class="chat-avatar other">
                        <?php echo strtoupper(substr($c['user_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>

                <div class="chat-bubble-wrapper">
                    <div class="chat-bubble <?php echo $roleClass; ?>">
                        <!-- NAME -->
                        <div class="chat-header">
                            <?php echo htmlspecialchars($c['user_name']); ?>
                        </div>
                        <!-- BODY -->
                        <div class="chat-body"><?php echo nl2br(htmlspecialchars(trim($c['comment']))); ?></div>
                        <!-- DATE -->
                        <div class="chat-footer">
                            <?php echo date('d M H:i', strtotime($c['created_at'])); ?>
                        </div>
                    </div>
                </div>

                <!-- AVATAR RIGHT (Me) -->
                <?php if ($isMe): ?>
                    <div class="chat-avatar me">
                        <?php echo strtoupper(substr($c['user_name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        }
    }

} catch (Exception $e) {
    echo "Error loading comments.";
}
