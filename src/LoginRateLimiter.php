<?php

declare(strict_types=1);

/**
 * LoginRateLimiter
 *
 * Brute-force protection untuk endpoint login. Mencatat setiap percobaan
 * (sukses/gagal) per kombinasi identifier (username) + IP, lalu mengunci
 * sementara apabila kegagalan dalam window tertentu mencapai batas.
 *
 * Default: 5 kegagalan per 15 menit per (username, ip).
 *
 * Pemakaian:
 *   $limiter = new LoginRateLimiter(Database::getInstance());
 *   if ($limiter->isLocked($username, $ip)) {
 *       // tolak login & beri pesan tunggu
 *   }
 *   // ... validasi password ...
 *   if ($passwordOk) {
 *       $limiter->recordSuccess($username, $ip);
 *   } else {
 *       $limiter->recordFailure($username, $ip);
 *   }
 */
class LoginRateLimiter
{
    public function __construct(
        private PDO $pdo,
        private int $maxAttempts = 5,
        private int $windowSeconds = 900,
    ) {
    }

    public function isLocked(string $identifier, string $ip): bool
    {
        return $this->getAttemptsCount($identifier, $ip) >= $this->maxAttempts;
    }

    /**
     * Hitung kegagalan dalam jendela waktu aktif untuk pasangan (identifier, ip).
     */
    public function getAttemptsCount(string $identifier, string $ip): int
    {
        $sql = $this->isSqlite()
            ? "SELECT COUNT(*) FROM login_attempts
                 WHERE identifier = :id AND ip = :ip AND successful = 0
                   AND attempted_at >= datetime('now', :window)"
            : "SELECT COUNT(*) FROM login_attempts
                 WHERE identifier = :id AND ip = :ip AND successful = 0
                   AND attempted_at >= (NOW() - INTERVAL :window SECOND)";

        $stmt = $this->pdo->prepare($sql);
        $params = ['id' => $identifier, 'ip' => $ip];
        $params['window'] = $this->isSqlite()
            ? '-' . $this->windowSeconds . ' seconds'
            : $this->windowSeconds;
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Detik tersisa sampai user bisa coba lagi (0 jika tidak terkunci).
     */
    public function secondsUntilUnlock(string $identifier, string $ip): int
    {
        if (!$this->isLocked($identifier, $ip)) {
            return 0;
        }

        $sql = $this->isSqlite()
            ? "SELECT MIN(attempted_at) FROM login_attempts
                 WHERE identifier = :id AND ip = :ip AND successful = 0
                   AND attempted_at >= datetime('now', :window)"
            : "SELECT MIN(attempted_at) FROM login_attempts
                 WHERE identifier = :id AND ip = :ip AND successful = 0
                   AND attempted_at >= (NOW() - INTERVAL :window SECOND)";

        $stmt = $this->pdo->prepare($sql);
        $params = ['id' => $identifier, 'ip' => $ip];
        $params['window'] = $this->isSqlite()
            ? '-' . $this->windowSeconds . ' seconds'
            : $this->windowSeconds;
        $stmt->execute($params);

        $oldest = $stmt->fetchColumn();
        if (!$oldest) {
            return 0;
        }

        $oldestTs = is_numeric($oldest) ? (int) $oldest : strtotime((string) $oldest);
        if ($oldestTs === false) {
            return 0;
        }

        $remaining = ($oldestTs + $this->windowSeconds) - time();

        return max(0, $remaining);
    }

    public function recordFailure(string $identifier, string $ip): void
    {
        $this->record($identifier, $ip, false);
    }

    /**
     * Catat sukses & bersihkan kegagalan dalam window agar user tidak terkunci sia-sia.
     */
    public function recordSuccess(string $identifier, string $ip): void
    {
        $this->record($identifier, $ip, true);

        $sql = $this->isSqlite()
            ? "DELETE FROM login_attempts
                 WHERE identifier = :id AND ip = :ip AND successful = 0
                   AND attempted_at >= datetime('now', :window)"
            : "DELETE FROM login_attempts
                 WHERE identifier = :id AND ip = :ip AND successful = 0
                   AND attempted_at >= (NOW() - INTERVAL :window SECOND)";

        $stmt = $this->pdo->prepare($sql);
        $params = ['id' => $identifier, 'ip' => $ip];
        $params['window'] = $this->isSqlite()
            ? '-' . $this->windowSeconds . ' seconds'
            : $this->windowSeconds;
        $stmt->execute($params);
    }

    private function record(string $identifier, string $ip, bool $success): void
    {
        $sql = 'INSERT INTO login_attempts (identifier, ip, successful) VALUES (:id, :ip, :ok)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $identifier,
            'ip' => $ip,
            'ok' => $success ? 1 : 0,
        ]);
    }

    private function isSqlite(): bool
    {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
    }
}
