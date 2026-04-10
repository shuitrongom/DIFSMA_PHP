<?php
/**
 * CSRF token generation and validation.
 * Include at the top of every admin form page.
 * Uses an array of tokens to support multiple open tabs.
 */

if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    }
}

/**
 * Generates a cryptographically secure CSRF token, stores it in the session,
 * and returns it. Keeps up to 5 tokens to support multiple tabs.
 */
function csrf_token(): string
{
    $token = bin2hex(random_bytes(32));

    if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
        $_SESSION['csrf_tokens'] = [];
    }

    // Keep max 10 tokens (oldest removed first)
    $_SESSION['csrf_tokens'][] = $token;
    if (count($_SESSION['csrf_tokens']) > 10) {
        array_shift($_SESSION['csrf_tokens']);
    }

    return $token;
}

/**
 * Validates the provided token against stored tokens using
 * a timing-safe comparison. Removes the used token after validation.
 */
function csrf_validate(string $token, bool $consume = true): bool
{
    if (empty($token)) {
        return false;
    }

    if (!isset($_SESSION['csrf_tokens']) || !is_array($_SESSION['csrf_tokens'])) {
        // Backward compat: check old single-token format
        $stored = $_SESSION['csrf_token'] ?? '';
        if ($consume) unset($_SESSION['csrf_token']);
        return $stored !== '' && hash_equals($stored, $token);
    }

    foreach ($_SESSION['csrf_tokens'] as $i => $stored) {
        if (hash_equals($stored, $token)) {
            if ($consume) {
                unset($_SESSION['csrf_tokens'][$i]);
                $_SESSION['csrf_tokens'] = array_values($_SESSION['csrf_tokens']);
            }
            return true;
        }
    }

    return false;
}
