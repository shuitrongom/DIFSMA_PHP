<?php
/**
 * Feature: dif-cms-php-migration, Property 2: credenciales inválidas nunca inician sesión
 *
 * Validates: Requirements 1.3
 *
 * Para cualquier par usuario/contraseña que no coincida con el admin registrado,
 * auth() retorna false — 100 iteraciones.
 *
 * Strategy:
 *   - We extract the authentication logic from admin/login.php into a pure
 *     helper function auth() that accepts a PDO instance, username and password,
 *     and returns true only when the credentials match the admin record.
 *   - We use an in-memory SQLite database to avoid requiring a real MySQL
 *     connection, seeding it with a single admin record.
 *   - We test three sub-properties independently with 100 iterations each:
 *       2a: wrong username (correct password) → false
 *       2b: wrong password (correct username) → false
 *       2c: both wrong (random username + random password) → false
 *   - We also verify the positive case: correct credentials → true.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


// ── Pure helper: mirrors the authentication logic in admin/login.php ──────────

/**
 * Attempts to authenticate against the admin table.
 *
 * Mirrors the logic from admin/login.php:
 *   SELECT id, username, password FROM admin WHERE username = ? LIMIT 1
 *   password_verify($password, $admin['password'])
 *
 * @param PDO    $pdo      Database connection (MySQL or SQLite for tests).
 * @param string $username Submitted username.
 * @param string $password Submitted plaintext password.
 * @return bool  true if credentials match the admin record, false otherwise.
 */
function auth(PDO $pdo, string $username, string $password): bool
{
    try {
        $stmt = $pdo->prepare('SELECT id, username, password FROM admin WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            return true;
        }

        return false;
    } catch (\PDOException $e) {
        return false;
    }
}


// ── Test class ────────────────────────────────────────────────────────────────

class Property2InvalidCredentialsTest extends TestCase
{
    /** @var PDO In-memory SQLite database seeded with one admin record. */
    private PDO $pdo;

    /** @var string The real admin username stored in the test DB. */
    private string $adminUsername = 'admin';

    /** @var string The real admin plaintext password (hashed in DB). */
    private string $adminPassword = 'S3cur3P@ssw0rd!';

    // ── Setup / Teardown ──────────────────────────────────────────────────────

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec('
            CREATE TABLE admin (
                id       INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL
            )
        ');

        $hash = password_hash($this->adminPassword, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare('INSERT INTO admin (username, password) VALUES (?, ?)');
        $stmt->execute([$this->adminUsername, $hash]);
    }

    protected function tearDown(): void
    {
        unset($this->pdo);
    }


    // ── Generators ────────────────────────────────────────────────────────────

    /**
     * Returns a random username guaranteed to differ from the real admin username.
     */
    private function randomWrongUsername(): string
    {
        $candidates = [
            'administrator', 'root', 'superuser', 'user', 'guest',
            'test', 'dif_admin', 'webmaster', 'cms_user', 'operador',
            '',                             // empty string
            ' ',                            // whitespace
            'admin ',                       // trailing space
            ' admin',                       // leading space
            'ADMIN',                        // uppercase
            'Admin',                        // mixed case
            "admin'; DROP TABLE admin;--",  // SQL injection attempt
            str_repeat('a', 255),           // very long string
            "\0admin",                      // null byte prefix
        ];

        $candidate = $candidates[array_rand($candidates)];
        if ($candidate === $this->adminUsername) {
            $candidate .= '_wrong_' . bin2hex(random_bytes(4));
        }

        return $candidate;
    }

    /**
     * Returns a random password guaranteed to differ from the real admin password.
     */
    private function randomWrongPassword(): string
    {
        $candidates = [
            'password', '123456', 'admin', 'letmein', 'qwerty',
            '',                                         // empty string
            ' ',                                        // whitespace
            $this->adminPassword . '!',                 // real password + extra char
            strtoupper($this->adminPassword),           // uppercase variant
            substr($this->adminPassword, 0, -1),        // one char shorter
            "' OR '1'='1",                              // SQL injection attempt
            str_repeat('x', 72),                        // bcrypt max length boundary
            str_repeat('x', 73),                        // one byte over bcrypt limit
            "\0",                                       // null byte
            bin2hex(random_bytes(16)),                  // random hex string
        ];

        $candidate = $candidates[array_rand($candidates)];
        if ($candidate === $this->adminPassword) {
            $candidate .= '_wrong';
        }

        return $candidate;
    }


    // =========================================================================
    // Positive baseline — correct credentials authenticate successfully
    // =========================================================================

    /**
     * **Validates: Requirements 1.2**
     *
     * Sanity check: the correct admin credentials must return true.
     * This confirms the test DB is set up correctly before testing the
     * invalid-credentials property.
     */
    public function testCorrectCredentialsReturnTrue(): void
    {
        $result = auth($this->pdo, $this->adminUsername, $this->adminPassword);

        $this->assertTrue(
            $result,
            'Correct admin credentials must return true from auth().'
        );
    }

    // =========================================================================
    // Property 2a — wrong username (correct password) never authenticates (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 1.3**
     *
     * For any username that does NOT match the admin record, auth() must
     * return false even when the correct password is supplied.
     */
    public function testProperty2a_WrongUsernameNeverAuthenticates(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $wrongUsername = $this->randomWrongUsername();
            $result        = auth($this->pdo, $wrongUsername, $this->adminPassword);

            if ($result !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 2a failed: auth() returned true for a wrong username in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 2b — wrong password (correct username) never authenticates (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 1.3**
     *
     * For any password that does NOT match the stored bcrypt hash, auth() must
     * return false even when the correct username is supplied.
     */
    public function testProperty2b_WrongPasswordNeverAuthenticates(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $wrongPassword = $this->randomWrongPassword();
            $result        = auth($this->pdo, $this->adminUsername, $wrongPassword);

            if ($result !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 2b failed: auth() returned true for a wrong password in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 2c — both wrong (random username + random password) never authenticates (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 1.3**
     *
     * For any pair (username, password) where neither matches the admin record,
     * auth() must return false.
     *
     * This is the core property: invalid credentials NEVER start a session.
     */
    public function testProperty2c_BothWrongNeverAuthenticates(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $wrongUsername = $this->randomWrongUsername();
            $wrongPassword = $this->randomWrongPassword();
            $result        = auth($this->pdo, $wrongUsername, $wrongPassword);

            if ($result !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 2c failed: auth() returned true for both wrong username and password in {$failures}/100 iterations."
        );
    }


    // =========================================================================
    // Edge cases — specific inputs that must always be rejected
    // =========================================================================

    /**
     * **Validates: Requirements 1.3**
     *
     * Empty username and empty password must be rejected.
     */
    public function testEmptyCredentialsAreRejected(): void
    {
        $this->assertFalse(
            auth($this->pdo, '', ''),
            'Empty username and password must not authenticate.'
        );
        $this->assertFalse(
            auth($this->pdo, $this->adminUsername, ''),
            'Correct username with empty password must not authenticate.'
        );
        $this->assertFalse(
            auth($this->pdo, '', $this->adminPassword),
            'Empty username with correct password must not authenticate.'
        );
    }

    /**
     * **Validates: Requirements 1.3**
     *
     * SQL injection attempts in the username field must not bypass authentication.
     * PDO prepared statements must neutralize these inputs.
     */
    public function testSqlInjectionInUsernameIsRejected(): void
    {
        $injections = [
            "' OR '1'='1",
            "' OR 1=1--",
            "admin'--",
            "' UNION SELECT id, username, password FROM admin--",
            "'; DROP TABLE admin;--",
            "admin' OR 'x'='x",
        ];

        foreach ($injections as $injection) {
            $this->assertFalse(
                auth($this->pdo, $injection, $this->adminPassword),
                "SQL injection in username must not authenticate: {$injection}"
            );
            $this->assertFalse(
                auth($this->pdo, $injection, 'wrongpassword'),
                "SQL injection in username with wrong password must not authenticate: {$injection}"
            );
        }
    }

    /**
     * **Validates: Requirements 1.3**
     *
     * SQL injection attempts in the password field must not bypass authentication.
     */
    public function testSqlInjectionInPasswordIsRejected(): void
    {
        $injections = [
            "' OR '1'='1",
            "' OR 1=1--",
            "wrongpass' OR 'x'='x",
            "'; SELECT * FROM admin;--",
        ];

        foreach ($injections as $injection) {
            $this->assertFalse(
                auth($this->pdo, $this->adminUsername, $injection),
                "SQL injection in password must not authenticate: {$injection}"
            );
        }
    }

    /**
     * **Validates: Requirements 1.3**
     *
     * A non-existent admin table (simulating a DB error) must cause auth()
     * to return false rather than throw an unhandled exception.
     */
    public function testDatabaseErrorReturnsFalse(): void
    {
        $emptyPdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $result = auth($emptyPdo, $this->adminUsername, $this->adminPassword);

        $this->assertFalse(
            $result,
            'auth() must return false (not throw) when the admin table does not exist.'
        );
    }

    /**
     * **Validates: Requirements 1.3**
     *
     * Usernames with trailing/leading spaces must not authenticate.
     */
    public function testUsernameWithSpacesIsRejected(): void
    {
        $variants = ['admin ', ' admin', ' admin '];

        foreach ($variants as $variant) {
            $this->assertFalse(
                auth($this->pdo, $variant, $this->adminPassword),
                "Username with surrounding spaces must not authenticate: '{$variant}'"
            );
        }
    }
}
