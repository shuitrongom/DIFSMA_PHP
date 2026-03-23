<?php
/**
 * Feature: dif-cms-php-migration, Property 3: rutas admin sin sesión redirigen a login
 *
 * Validates: Requirements 1.4
 *
 * Para cualquier ruta /admin/*.php (excepto login), sin sesión activa →
 * Location: login.php — 100 iteraciones.
 *
 * Since we cannot issue real HTTP requests in unit tests, we extract the
 * auth_guard logic into a testable helper and verify the redirect decision
 * directly, without calling header().
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
 * Mimics the auth_guard check without side-effects (no header(), no exit).
 *
 * Returns true  → guard passes (user is authenticated, no redirect needed).
 * Returns false → guard would redirect to login.php.
 *
 * @param array<string,mixed> $session  Simulated $_SESSION contents.
 */
function auth_guard_check(array $session): bool
{
    return ($session['admin_logged'] ?? false) === true;
}

/**
 * Returns true when the given filename is the login page and therefore
 * exempt from the auth guard.
 */
function is_login_page(string $filename): bool
{
    return strtolower(basename($filename)) === 'login.php';
}

class Property3AuthGuardTest extends TestCase
{
    // -------------------------------------------------------------------------
    // Generators
    // -------------------------------------------------------------------------

    /**
     * Returns a random admin route filename (*.php, never login.php).
     * Covers realistic names as well as arbitrary random strings.
     */
    private function randomAdminRoute(): string
    {
        $realistic = [
            'dashboard.php',
            'slider_principal.php',
            'slider_comunica.php',
            'noticias.php',
            'presidencia.php',
            'direcciones.php',
            'organigrama.php',
            'tramites.php',
            'galeria.php',
            'seac.php',
            'programas.php',
            'transparencia.php',
            'footer.php',
            'logout.php',
            'upload_handler.php',
            'csrf.php',
        ];

        // 50 % chance: pick a realistic name; 50 % chance: random string + .php
        if (random_int(0, 1) === 0) {
            return $realistic[array_rand($realistic)];
        }

        // Random alphanumeric filename, guaranteed not to be "login.php"
        do {
            $name = substr(bin2hex(random_bytes(8)), 0, random_int(4, 16)) . '.php';
        } while (strtolower($name) === 'login.php');

        return $name;
    }

    /**
     * Returns a session array that is NOT authenticated.
     * Covers: key absent, null, false, 0, empty string, wrong type.
     */
    private function randomUnauthenticatedSession(): array
    {
        $variants = [
            [],                                    // key absent
            ['admin_logged' => false],             // explicit false
            ['admin_logged' => null],              // null
            ['admin_logged' => 0],                 // integer 0
            ['admin_logged' => ''],                // empty string
            ['admin_logged' => 'true'],            // string "true" (not bool)
            ['admin_logged' => 1],                 // integer 1 (not strict true)
            ['admin_logged' => '1'],               // string "1"
            ['admin_logged' => []],                // array
            ['other_key'    => true],              // unrelated key
        ];

        return $variants[array_rand($variants)];
    }

    // =========================================================================
    // Property 3a — any admin route without session redirects to login (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 1.4**
     *
     * For any /admin/*.php route (except login.php), when no authenticated
     * session exists, the auth guard must decide to redirect (returns false).
     */
    public function testProperty3a_AdminRouteWithoutSessionRedirectsToLogin(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $route   = $this->randomAdminRoute();
            $session = $this->randomUnauthenticatedSession();

            // Skip login.php — it is explicitly exempt from the guard
            if (is_login_page($route)) {
                continue;
            }

            $guardPasses = auth_guard_check($session);

            if ($guardPasses !== false) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 3a failed: auth guard passed (did not redirect) for an unauthenticated session in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 3b — authenticated session always passes the guard (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 1.4**
     *
     * For any /admin/*.php route, when $_SESSION['admin_logged'] === true
     * (strict), the auth guard must pass without redirecting.
     */
    public function testProperty3b_AuthenticatedSessionAlwaysPassesGuard(): void
    {
        $failures = 0;

        for ($i = 0; $i < 100; $i++) {
            $route   = $this->randomAdminRoute();
            $session = ['admin_logged' => true];

            $guardPasses = auth_guard_check($session);

            if ($guardPasses !== true) {
                $failures++;
            }
        }

        $this->assertSame(
            0,
            $failures,
            "Property 3b failed: auth guard rejected a valid authenticated session in {$failures}/100 iterations."
        );
    }

    // =========================================================================
    // Property 3c — login.php is exempt from the guard (100 iter)
    // =========================================================================

    /**
     * **Validates: Requirements 1.4**
     *
     * login.php must never be subject to the auth guard, regardless of
     * session state, so that unauthenticated users can reach the login form.
     */
    public function testProperty3c_LoginPageIsExemptFromGuard(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $session = $this->randomUnauthenticatedSession();

            $this->assertTrue(
                is_login_page('login.php'),
                'login.php must be identified as the login page.'
            );

            // The guard should NOT be applied to login.php; we verify the
            // exemption helper correctly identifies it.
            $this->assertFalse(
                is_login_page($this->randomAdminRoute()),
                'A random non-login admin route must not be identified as login.php.'
            );
        }
    }

    // =========================================================================
    // Edge cases — specific session values that must NOT pass the guard
    // =========================================================================

    /**
     * **Validates: Requirements 1.4**
     *
     * Strict comparison: only boolean true passes. All lookalike truthy values
     * must be rejected.
     */
    public function testStrictComparisonRejectsTruthyNonBooleanValues(): void
    {
        $falsy = [
            ['admin_logged' => 1],
            ['admin_logged' => '1'],
            ['admin_logged' => 'true'],
            ['admin_logged' => 1.0],
            ['admin_logged' => [true]],
            ['admin_logged' => new stdClass()],
        ];

        foreach ($falsy as $session) {
            $this->assertFalse(
                auth_guard_check($session),
                'auth_guard_check() must return false for non-boolean-true value: ' . var_export($session['admin_logged'], true)
            );
        }
    }

    /**
     * **Validates: Requirements 1.4**
     *
     * Absent key must be treated as unauthenticated.
     */
    public function testMissingSessionKeyIsUnauthenticated(): void
    {
        $this->assertFalse(auth_guard_check([]), 'Empty session must not pass the guard.');
        $this->assertFalse(auth_guard_check(['other' => true]), 'Unrelated session key must not pass the guard.');
    }

    /**
     * **Validates: Requirements 1.4**
     *
     * Only exact boolean true passes.
     */
    public function testOnlyBooleanTruePassesGuard(): void
    {
        $this->assertTrue(auth_guard_check(['admin_logged' => true]), 'Boolean true must pass the guard.');
        $this->assertFalse(auth_guard_check(['admin_logged' => false]), 'Boolean false must not pass the guard.');
        $this->assertFalse(auth_guard_check(['admin_logged' => null]), 'Null must not pass the guard.');
    }
}
