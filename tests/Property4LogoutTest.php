<?php
/**
 * Feature: dif-cms-php-migration, Property 4: cierre de sesión destruye la sesión
 *
 * Validates: Requirements 1.5
 *
 * Para cualquier sesión autenticada activa, después del logout,
 * is_authenticated() retorna false — 100 iteraciones.
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Extracted from admin/auth_guard.php
 * Checks whether the given session array represents an authenticated admin.
 */
function is_authenticated(array $session): bool
{
    return ($session['admin_logged'] ?? false) === true;
}

/**
 * Extracted from admin/logout.php
 * Clears all session data (equivalent to $_SESSION = [] + session_destroy()).
 */
function perform_logout(array &$session): void
{
    $session = [];
}

class Property4LogoutTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Build a random authenticated session with optional extra keys. */
    private function randomAuthenticatedSession(): array
    {
        $session = ['admin_logged' => true];

        // Randomly add extra session keys to simulate real-world sessions
        $extraKeys = ['csrf_token', 'user_id', 'last_activity', 'role', 'flash_message'];
        $count = random_int(0, count($extraKeys));
        $chosen = array_slice($extraKeys, 0, $count);
        foreach ($chosen as $key) {
            $session[$key] = bin2hex(random_bytes(8));
        }

        return $session;
    }

    // -------------------------------------------------------------------------
    // Baseline / sanity checks
    // -------------------------------------------------------------------------

    /** Authenticated session is recognised as authenticated before logout. */
    public function testAuthenticatedSessionIsRecognised(): void
    {
        $session = ['admin_logged' => true];
        $this->assertTrue(is_authenticated($session));
    }

    /** Empty session is not authenticated. */
    public function testEmptySessionIsNotAuthenticated(): void
    {
        $this->assertFalse(is_authenticated([]));
    }

    /** Session without admin_logged key is not authenticated. */
    public function testSessionWithoutKeyIsNotAuthenticated(): void
    {
        $this->assertFalse(is_authenticated(['user_id' => 42]));
    }

    /** Session with admin_logged = false is not authenticated. */
    public function testSessionWithFalseIsNotAuthenticated(): void
    {
        $this->assertFalse(is_authenticated(['admin_logged' => false]));
    }

    /** Session with admin_logged = 1 (non-strict) is not authenticated. */
    public function testSessionWithIntOneIsNotAuthenticated(): void
    {
        $this->assertFalse(is_authenticated(['admin_logged' => 1]));
    }

    /** perform_logout() clears the session array completely. */
    public function testLogoutClearsSessionArray(): void
    {
        $session = ['admin_logged' => true, 'csrf_token' => 'abc123'];
        perform_logout($session);
        $this->assertSame([], $session);
    }

    // -------------------------------------------------------------------------
    // Property 4 — 100 iterations
    // -------------------------------------------------------------------------

    /**
     * **Validates: Requirements 1.5**
     *
     * For any authenticated session, after perform_logout(),
     * is_authenticated() must return false.
     */
    public function testProperty4_LogoutAlwaysDestroysAuthentication(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $session = $this->randomAuthenticatedSession();

            // Pre-condition: session must be authenticated before logout
            $this->assertTrue(
                is_authenticated($session),
                "Pre-condition failed: session should be authenticated before logout."
            );

            perform_logout($session);

            if (is_authenticated($session) !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 4 failed: is_authenticated() returned true after logout in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.5**
     *
     * After logout, the session array is completely empty —
     * no residual keys remain that could be exploited.
     */
    public function testProperty4b_LogoutLeavesNoResidualSessionData(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $session = $this->randomAuthenticatedSession();
            perform_logout($session);

            if (count($session) !== 0) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 4b failed: session was not empty after logout in {$failures}/100 iterations."
        );
    }

    /**
     * **Validates: Requirements 1.5**
     *
     * After logout, auth_guard logic (is_authenticated) redirects to login —
     * simulated by verifying is_authenticated() returns false for the cleared session.
     */
    public function testProperty4c_AuthGuardRejectsLoggedOutSession(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $session = $this->randomAuthenticatedSession();
            perform_logout($session);

            // Simulate auth_guard check: ($session['admin_logged'] ?? false) !== true
            $wouldRedirect = !is_authenticated($session);

            if (!$wouldRedirect) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 4c failed: auth_guard would NOT redirect to login after logout in {$failures}/100 iterations."
        );
    }
}
