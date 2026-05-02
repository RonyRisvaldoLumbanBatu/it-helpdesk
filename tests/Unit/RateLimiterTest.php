<?php

declare(strict_types=1);

namespace Tests\Unit;

use PDO;
use PHPUnit\Framework\TestCase;
use LoginRateLimiter;

/**
 * Smoke test untuk LoginRateLimiter (brute-force protection).
 *
 * Memakai SQLite in-memory + skema kompatibel.
 */
final class RateLimiterTest extends TestCase
{
    private PDO $pdo;
    private \LoginRateLimiter $limiter;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo->exec(<<<'SQL'
            CREATE TABLE login_attempts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                identifier TEXT NOT NULL,
                ip TEXT NOT NULL,
                successful INTEGER NOT NULL DEFAULT 0,
                attempted_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        SQL);

        $this->limiter = new \LoginRateLimiter($this->pdo, maxAttempts: 3, windowSeconds: 900);
    }

    public function testBelumDikuncipadaKondisiAwal(): void
    {
        $this->assertFalse($this->limiter->isLocked('rony', '127.0.0.1'));
        $this->assertSame(0, $this->limiter->getAttemptsCount('rony', '127.0.0.1'));
    }

    public function testKunciSetelahMencapaiBatas(): void
    {
        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->assertFalse($this->limiter->isLocked('rony', '127.0.0.1'));

        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->assertTrue($this->limiter->isLocked('rony', '127.0.0.1'));
    }

    public function testSuksesMeresetKegagalan(): void
    {
        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->limiter->recordSuccess('rony', '127.0.0.1');

        $this->assertSame(0, $this->limiter->getAttemptsCount('rony', '127.0.0.1'));
        $this->assertFalse($this->limiter->isLocked('rony', '127.0.0.1'));
    }

    public function testKunciTerpisahPerKombinasiUserDanIp(): void
    {
        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->limiter->recordFailure('rony', '127.0.0.1');
        $this->limiter->recordFailure('rony', '127.0.0.1');

        $this->assertTrue($this->limiter->isLocked('rony', '127.0.0.1'));
        $this->assertFalse($this->limiter->isLocked('rony', '10.0.0.1'));
        $this->assertFalse($this->limiter->isLocked('admin', '127.0.0.1'));
    }
}
