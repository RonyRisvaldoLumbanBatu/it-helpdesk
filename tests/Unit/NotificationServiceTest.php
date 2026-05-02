<?php

declare(strict_types=1);

namespace Tests\Unit;

use PDO;
use PHPUnit\Framework\TestCase;
use NotificationService;

/**
 * Smoke test untuk NotificationService.
 *
 * Memakai SQLite in-memory + tabel notifications minimal agar tidak butuh
 * MySQL saat CI. Yang diuji adalah perilaku service-nya, bukan SQL spesifik MySQL.
 */
final class NotificationServiceTest extends TestCase
{
    private PDO $pdo;
    private \NotificationService $service;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo->exec(<<<'SQL'
            CREATE TABLE notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                message TEXT NOT NULL,
                link TEXT NOT NULL,
                type TEXT NOT NULL DEFAULT 'info',
                is_read INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        SQL);

        $this->service = new \NotificationService($this->pdo);
    }

    public function testCreateNotificationMenyimpanKeDatabase(): void
    {
        $ok = $this->service->createNotification(1, 'Halo', 'pesan uji', '?page=dashboard', 'info');
        $this->assertTrue($ok);

        $rows = $this->pdo->query('SELECT * FROM notifications')->fetchAll();
        $this->assertCount(1, $rows);
        $this->assertSame('Halo', $rows[0]['title']);
        $this->assertSame('info', $rows[0]['type']);
    }

    public function testTipeNotifikasiInvalidDifallbackKeInfo(): void
    {
        $this->service->createNotification(1, 'Tes', 'pesan', '?p=x', 'tipe-aneh');
        $row = $this->pdo->query('SELECT type FROM notifications LIMIT 1')->fetch();
        $this->assertSame('info', $row['type']);
    }

    public function testGetUnreadCountDanMarkAsRead(): void
    {
        $this->service->createNotification(7, 'A', 'a', '?', 'info');
        $this->service->createNotification(7, 'B', 'b', '?', 'info');
        $this->service->createNotification(99, 'C', 'c', '?', 'info');

        $this->assertSame(2, $this->service->getUnreadCount(7));
        $this->assertSame(1, $this->service->getUnreadCount(99));
        $this->assertSame(0, $this->service->getUnreadCount(123));

        $first = $this->pdo->query('SELECT id FROM notifications WHERE user_id = 7 ORDER BY id ASC LIMIT 1')->fetch();
        $this->assertTrue($this->service->markAsRead((int) $first['id']));
        $this->assertSame(1, $this->service->getUnreadCount(7));
    }

    public function testMarkAllAsReadHanyaUntukUserTerkait(): void
    {
        $this->service->createNotification(7, 'A', 'a', '?', 'info');
        $this->service->createNotification(7, 'B', 'b', '?', 'info');
        $this->service->createNotification(99, 'C', 'c', '?', 'info');

        $this->service->markAllAsRead(7);

        $this->assertSame(0, $this->service->getUnreadCount(7));
        $this->assertSame(1, $this->service->getUnreadCount(99));
    }

    public function testNotifyNewCommentMembuatRecordDenganLinkTiket(): void
    {
        $this->service->notifyNewComment(42, 7, 'Rony', 'komentar uji yang singkat');

        $row = $this->pdo->query('SELECT * FROM notifications LIMIT 1')->fetch();
        $this->assertStringContainsString('#42', $row['title']);
        $this->assertStringContainsString('id=42', $row['link']);
        $this->assertStringContainsString('Rony', $row['message']);
    }

    public function testNotifyTicketStatusChangedMemilihTipeYangBenar(): void
    {
        $this->service->notifyTicketStatusChanged(1, 7, 'resolved', 'Printer rusak');
        $this->service->notifyTicketStatusChanged(2, 7, 'rejected', 'Permintaan duplikat');
        $this->service->notifyTicketStatusChanged(3, 7, 'in_progress', 'Wifi lambat');

        $rows = $this->pdo->query('SELECT type FROM notifications ORDER BY id ASC')->fetchAll();
        $this->assertSame('success', $rows[0]['type']);
        $this->assertSame('error', $rows[1]['type']);
        $this->assertSame('info', $rows[2]['type']);
    }
}
