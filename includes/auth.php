<?php
/**
 * Authentication, session and role-based access control helpers.
 * Include this at the very top of every page (before any HTML output).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

require_once __DIR__ . '/../config/db.php';

/** Is anyone logged in? */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/** Currently logged in user's role, or null. */
function current_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

/** Convenience accessor for the logged-in user's display name. */
function current_name(): string
{
    return $_SESSION['full_name'] ?? 'Guest';
}

/**
 * Log a verified user into the session. Regenerates the session ID to
 * prevent session fixation attacks.
 */
function log_in_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['role']      = $user['role'];
}

function log_out_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Require the visitor to be logged in. If not, redirect to login and
 * remember where they were headed via a "redirect" query parameter.
 */
function require_login(): void
{
    if (!is_logged_in()) {
        $target = urlencode($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: ' . BASE_URL . '/login.php?redirect=' . $target);
        exit;
    }
}

/**
 * Require the visitor to be logged in AND hold one of the given roles.
 * Example: require_role(['admin']); or require_role(['admin', 'planner']);
 */
function require_role(array $allowedRoles): void
{
    require_login();
    if (!in_array(current_role(), $allowedRoles, true)) {
        http_response_code(403);
        require_once __DIR__ . '/../403.php';
        exit;
    }
}

/** Generate (and cache in session) a CSRF token for form protection. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Verify a submitted CSRF token using a timing-safe comparison. */
function csrf_verify(?string $submitted): bool
{
    return is_string($submitted)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $submitted);
}

/** Output a hidden CSRF input field for a <form>. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}
