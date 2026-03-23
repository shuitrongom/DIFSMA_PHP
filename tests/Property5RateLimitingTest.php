<?php
/**
 * Feature: dif-cms-php-migration, Property 5: rate limiting tras 5 intentos fallidos
 *
 * Validates: Requirements 1.7
 *
 * Para cualquier IP, tras 5 intentos fallidos en 15 min, el 6to intento es bloqueado.
 * También verifica: 4 intentos → no bloqueado; intentos > 15 min → no bloqueado.
 * 100 iteraciones con IPs aleatorias.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Determina si una IP está bloqueada por rate limiting.
 *
 * Cuenta los intentos fallidos de la IP en los últimos 15 minutos.
 * Si hay 5 o más, retorna true (bloqueado).
 */
function is_rate_limited(PDO $pdo, string $ip): bool
{
    try {
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) FROM login_attempts
             WHERE ip = ? AND attempted_at > datetime('now', '-15 minutes')"
        );
        $stmt->execute([$ip]);
        $count = (int) $stmt->fetchColumn();
        return $count >= 5;
    } catch (\PDOException $e) {
        return false;
    }
}

class Property5RateLimitingTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->pdo->exec(
            'CREATE TABLE login_attempts (
                id          INTEGER PRIMARY KEY AUTOINCREMENT,
                ip          VARCHAR(45) NOT NULL,
                attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Inserta N intentos recientes (dentro de los últimos 15 min) para la IP dada. */
    private function insertRecentAttempts(string $ip, int $count): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO login_attempts (ip, attempted_at) VALUES (?, datetime('now', '-1 minutes'))"
        );
        for ($i = 0; $i < $count; $i++) {
            $stmt->execute([$ip]);
        }
    }

    /** Inserta N intentos antiguos (más de 15 min) para la IP dada. */
    private function insertOldAttempts(string $ip, int $count): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO login_attempts (ip, attempted_at) VALUES (?, datetime('now', '-20 minutes'))"
        );
        for ($i = 0; $i < $count; $i++) {
            $stmt->execute([$ip]);
        }
    }

    /** Genera una IP aleatoria (IPv4 o IPv6 corta). */
    private function randomIp(): string
    {
        $type = random_int(0, 1);
        if ($type === 0) {
            // IPv4
            return implode('.', [
                random_int(1, 254),
                random_int(0, 255),
                random_int(0, 255),
                random_int(1, 254),
            ]);
        }
        // IPv6 abreviado
        return implode(':', array_map(
            fn() => sprintf('%04x', random_int(0, 0xffff)),
            range(1, 8)
        ));
    }

    /** Limpia todos los intentos de la tabla entre iteraciones. */
    private function clearAttempts(): void
    {
        $this->pdo->exec('DELETE FROM login_attempts');
    }

    // -------------------------------------------------------------------------
    // Property 5 — 100 iteraciones
    // -------------------------------------------------------------------------

    /**
     * **Validates: Requirements 1.7**
     *
     * Para cualquier IP, tras exactamente 5 intentos fallidos recientes,
     * is_rate_limited() debe retornar true (el 6to intento es bloqueado).
     */
    public function testProperty5a_FiveAttemptsBlocksIp(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $this->clearAttempts();
            $ip = $this->randomIp();

            $this->insertRecentAttempts($ip, 5);

            if (is_rate_limited($this->pdo, $ip) !== true) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 5a failed: is_rate_limited() returned false after 5 recent attempts in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.7**
     *
     * Para cualquier IP, con más de 5 intentos fallidos recientes,
     * is_rate_limited() debe retornar true.
     */
    public function testProperty5b_MoreThanFiveAttemptsBlocksIp(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $this->clearAttempts();
            $ip = $this->randomIp();
            $extraAttempts = random_int(6, 20);

            $this->insertRecentAttempts($ip, $extraAttempts);

            if (is_rate_limited($this->pdo, $ip) !== true) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 5b failed: is_rate_limited() returned false with >5 recent attempts in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.7**
     *
     * Para cualquier IP, con 4 o menos intentos fallidos recientes,
     * is_rate_limited() debe retornar false (no bloqueado).
     */
    public function testProperty5c_FourOrFewerAttemptsDoNotBlock(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $this->clearAttempts();
            $ip = $this->randomIp();
            $attempts = random_int(0, 4);

            $this->insertRecentAttempts($ip, $attempts);

            if (is_rate_limited($this->pdo, $ip) !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 5c failed: is_rate_limited() returned true with ≤4 recent attempts in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.7**
     *
     * Para cualquier IP, intentos más antiguos de 15 minutos no cuentan
     * para el rate limiting — is_rate_limited() debe retornar false.
     */
    public function testProperty5d_OldAttemptsDoNotBlock(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $this->clearAttempts();
            $ip = $this->randomIp();

            // 5 intentos viejos (fuera de la ventana de 15 min)
            $this->insertOldAttempts($ip, 5);

            if (is_rate_limited($this->pdo, $ip) !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 5d failed: is_rate_limited() returned true for attempts older than 15 min in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.7**
     *
     * El rate limiting es por IP: bloquear una IP no afecta a otras IPs.
     */
    public function testProperty5e_RateLimitingIsPerIp(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $this->clearAttempts();

            $blockedIp = $this->randomIp();
            // Generar una IP diferente garantizada
            do {
                $cleanIp = $this->randomIp();
            } while ($cleanIp === $blockedIp);

            // IP bloqueada: 5 intentos recientes
            $this->insertRecentAttempts($blockedIp, 5);
            // IP limpia: sin intentos

            $blockedResult = is_rate_limited($this->pdo, $blockedIp);
            $cleanResult   = is_rate_limited($this->pdo, $cleanIp);

            if ($blockedResult !== true || $cleanResult !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 5e failed: rate limiting leaked between IPs in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.7**
     *
     * Intentos viejos (>15 min) + intentos recientes: solo los recientes cuentan.
     * Con 5 recientes + N viejos → bloqueado.
     * Con 4 recientes + N viejos → no bloqueado.
     */
    public function testProperty5f_OnlyRecentAttemptsCountForWindow(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $this->clearAttempts();
            $ip = $this->randomIp();
            $oldCount = random_int(1, 10);

            // Caso A: 4 recientes + muchos viejos → no bloqueado
            $this->insertRecentAttempts($ip, 4);
            $this->insertOldAttempts($ip, $oldCount);
            if (is_rate_limited($this->pdo, $ip) !== false) {
                $failures++;
            }

            $this->clearAttempts();

            // Caso B: 5 recientes + muchos viejos → bloqueado
            $this->insertRecentAttempts($ip, 5);
            $this->insertOldAttempts($ip, $oldCount);
            if (is_rate_limited($this->pdo, $ip) !== true) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 5f failed: old attempts incorrectly affected rate limiting in {$failures}/100 iterations."
        );
    }
}
