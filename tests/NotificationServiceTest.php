<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class NotificationServiceTest extends TestCase
{
    private PDO $pdo;
    private NotificationService $service;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(
            'CREATE TABLE notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                message TEXT NOT NULL,
                link TEXT NOT NULL,
                type TEXT NOT NULL DEFAULT "info",
                is_read INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )'
        );

        $this->service = new NotificationService($this->pdo);
    }

    public function testCreateNotificationPersistsDataAndNormalizesType(): void
    {
        $result = $this->service->createNotification(1, 'Hello', 'Message', '/link', 'custom');

        $this->assertTrue($result);

        $row = $this->pdo->query('SELECT * FROM notifications')->fetch(PDO::FETCH_ASSOC);
        $this->assertSame('info', $row['type']);
        $this->assertSame('Hello', $row['title']);
        $this->assertSame('Message', $row['message']);
        $this->assertSame('/link', $row['link']);
        $this->assertSame(0, (int) $row['is_read']);
    }

    public function testNotifyNewCommentEscapesAndTruncatesComment(): void
    {
        $comment = str_repeat('x', 60);
        $this->service->notifyNewComment(15, 2, '<Admin>', $comment);

        $row = $this->pdo->query('SELECT * FROM notifications')->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('Komentar Baru pada #15', $row['title']);
        $this->assertSame('?page=dashboard&action=ticket_detail&id=15', $row['link']);
        $this->assertStringContainsString('&lt;Admin&gt;', $row['message']);
        $this->assertStringContainsString(str_repeat('x', 50) . '...', $row['message']);
    }

    public function testNotifyTicketStatusChangedSetsTypeAndLabel(): void
    {
        $this->service->notifyTicketStatusChanged(3, 1, 'resolved', 'Perbaiki <router>');

        $row = $this->pdo->query('SELECT * FROM notifications')->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('success', $row['type']);
        $this->assertSame('Status Tiket #3 Diperbarui', $row['title']);
        $this->assertStringContainsString('Selesai', $row['message']);
        $this->assertStringContainsString('Perbaiki &lt;router&gt;', $row['message']);
    }

    public function testNotifyNewTicketCreatesWarningNotification(): void
    {
        $this->service->notifyNewTicket(7, 9, '<User>', 'Jaringan down');

        $row = $this->pdo->query('SELECT * FROM notifications')->fetch(PDO::FETCH_ASSOC);

        $this->assertSame('warning', $row['type']);
        $this->assertSame('Tiket Baru #7', $row['title']);
        $this->assertStringContainsString('&lt;User&gt;', $row['message']);
        $this->assertStringContainsString('Jaringan down', $row['message']);
    }

    public function testMarkAsReadUpdatesNotification(): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO notifications (user_id, title, message, link, type) VALUES (?,?,?,?,?)');
        $stmt->execute([1, 'Title', 'Message', '/link', 'info']);
        $id = (int) $this->pdo->lastInsertId();

        $updated = $this->service->markAsRead($id);

        $this->assertTrue($updated);
        $this->assertSame(1, (int) $this->pdo->query('SELECT is_read FROM notifications WHERE id = ' . $id)->fetchColumn());
    }

    public function testMarkAllAsReadUpdatesOnlyTargetUser(): void
    {
        $insert = $this->pdo->prepare('INSERT INTO notifications (user_id, title, message, link, type, is_read) VALUES (?,?,?,?,?,?)');
        $insert->execute([5, 'Title', 'Message', '/link', 'info', 0]);
        $insert->execute([5, 'Other', 'Message', '/link', 'info', 0]);
        $insert->execute([6, 'Third', 'Message', '/link', 'info', 0]);

        $result = $this->service->markAllAsRead(5);

        $this->assertTrue($result);
        $this->assertSame(0, (int) $this->pdo->query('SELECT is_read FROM notifications WHERE user_id = 6')->fetchColumn());
        $this->assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM notifications WHERE user_id = 5 AND is_read = 0')->fetchColumn());
    }

    public function testGetUnreadNotificationsReturnsLatestFirstWithinLimit(): void
    {
        $insert = $this->pdo->prepare('INSERT INTO notifications (user_id, title, message, link, type, is_read, created_at) VALUES (?,?,?,?,?,?,?)');
        $insert->execute([8, 'First', 'Message', '/1', 'info', 0, '2024-01-01 10:00:00']);
        $insert->execute([8, 'Second', 'Message', '/2', 'info', 0, '2024-01-02 10:00:00']);
        $insert->execute([8, 'Third', 'Message', '/3', 'info', 0, '2024-01-03 10:00:00']);
        $insert->execute([8, 'Read one', 'Message', '/4', 'info', 1, '2024-01-04 10:00:00']);

        $notifications = $this->service->getUnreadNotifications(8, 2);

        $this->assertCount(2, $notifications);
        $this->assertSame('Third', $notifications[0]['title']);
        $this->assertSame('Second', $notifications[1]['title']);
    }

    public function testGetUnreadCountCountsOnlyUnread(): void
    {
        $insert = $this->pdo->prepare('INSERT INTO notifications (user_id, title, message, link, type, is_read) VALUES (?,?,?,?,?,?)');
        $insert->execute([10, 'Unread 1', 'Message', '/1', 'info', 0]);
        $insert->execute([10, 'Unread 2', 'Message', '/2', 'info', 0]);
        $insert->execute([10, 'Read', 'Message', '/3', 'info', 1]);

        $count = $this->service->getUnreadCount(10);

        $this->assertSame(2, $count);
    }
}
