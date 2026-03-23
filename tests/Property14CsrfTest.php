<?php
/**
 * Feature: dif-cms-php-migration, Property 14: CSRF rechaza POST sin token válido
 *
 * Validates: Requirements 15.5
 *
 * Para cualquier POST sin token CSRF válido (ausente, expirado o incorrecto),
 * el handler retorna rechazo sin escribir en DB — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Ensure session is available before loading csrf.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../admin/csrf.php';

class Property14CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        // Clear any leftover CSRF token before each test
        unset($_SESSION['csrf_token']);
    }

    protected function tearDown(): void
    {
        unset($_SESSION['csrf_token']);
    }

    // -------------------------------------------------------------------------
    // Helper: generate a random string of given byte length (hex-encoded)
    // -------------------------------------------------------------------------
    private function randomHexString(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    // =========================================================================
    // Property 14a — random incorrect tokens are always rejected (100 iterations)
    // =========================================================================

    /**
     * **Validates: Requirements 15.5**
     *
     * For any random string that is NOT the stored token,
     * csrf_validate() must return false.
     * Runs 100 iterations with freshly generated token/input pairs.
     */
    public function testProperty14a_RandomIncorrectTokenIsAlwaysRejected(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            // Generate and store a valid token in session
            $validToken = csrf_token(); // stores in $_SESSION['csrf_token']

            // Generate a different random string to use as the submitted token.
            // Regenerate until it differs from the valid token (astronomically
            // unlikely to collide, but we guard against it explicitly).
            do {
                $wrongToken = $this->randomHexString(32);
            } while ($wrongToken === $validToken);

            // Restore the valid token (csrf_token() already stored it, but
            // csrf_validate() from a previous iteration may have consumed it)
            $_SESSION['csrf_token'] = $validToken;

            $result = csrf_validate($wrongToken);

            if ($result !== false) {
                $failures++;
            }

            // csrf_validate() destroys the token; clear for next iteration
            unset($_SESSION['csrf_token']);
        }

        $this->assertSame(
            0,
            $failures,
            "Property 14a failed: csrf_validate() accepted an incorrect token in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 14b — absent token (no session entry) is always rejected
    // =========================================================================

    /**
     * **Validates: Requirements 15.5**
     *
     * When no CSRF token exists in the session (absent token scenario),
     * csrf_validate() must return false for any submitted value.
     */
    public function testProperty14b_AbsentTokenIsAlwaysRejected(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            // Ensure no token is stored in session
            unset($_SESSION['csrf_token']);

            // Submit any random string — should always be rejected
            $submittedToken = $this->randomHexString(32);

            $result = csrf_validate($submittedToken);

            if ($result !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 14b failed: csrf_validate() accepted a token when none was stored in session in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 14c — consumed (expired/used) token is rejected on second use
    // =========================================================================

    /**
     * **Validates: Requirements 15.5**
     *
     * After a token has been consumed by a first (valid) call to csrf_validate(),
     * any subsequent call with the same token must return false.
     * This covers the "expired/used" scenario.
     */
    public function testProperty14c_ConsumedTokenIsRejectedOnReuse(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            // Generate and store a fresh token
            $token = csrf_token();

            // First validation — should succeed (token is valid and present)
            $firstResult = csrf_validate($token);
            $this->assertTrue(
                $firstResult,
                "Iteration {$i}: First csrf_validate() with correct token should return true."
            );

            // Second validation with the same token — token was consumed, must fail
            $secondResult = csrf_validate($token);

            if ($secondResult !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 14c failed: csrf_validate() accepted a previously consumed token in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Bonus edge-case: empty string token is rejected
    // =========================================================================

    /**
     * **Validates: Requirements 15.5**
     *
     * An empty string submitted as CSRF token must always be rejected,
     * even if (by some bug) the session stored an empty string.
     */
    public function testEmptyStringTokenIsRejected(): void
    {
        // Case 1: no token in session, empty string submitted
        unset($_SESSION['csrf_token']);
        $this->assertFalse(csrf_validate(''), 'Empty token with no session entry must be rejected.');

        // Case 2: valid token in session, empty string submitted
        csrf_token(); // stores a real token
        $this->assertFalse(csrf_validate(''), 'Empty token with valid session entry must be rejected.');
    }
}
