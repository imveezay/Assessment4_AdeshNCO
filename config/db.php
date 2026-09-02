<?php
/**
 * Database connection (PDO + MySQL).
 * Edit the four constants below to match your local MySQL setup
 * (XAMPP / WAMP / MAMP default values are shown).
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'adesh_and_co');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
        ]
    );
} catch (PDOException $e) {
    // Never leak connection details/credentials to the browser.
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    die('Sorry, the site is temporarily unavailable. Please try again shortly.');
}

/**
 * BASE_URL — the web path this project is served from, worked out
 * automatically so links/assets work whether the project sits at the
 * domain root (http://example.com/) or in a subfolder
 * (http://localhost/adesh-and-co-dynamic/). Every internal link and
 * asset path in the templates is written as BASE_URL . '/xxx' instead
 * of a hard-coded '/xxx' so moving/renaming the folder never breaks it.
 */
if (!defined('BASE_URL')) {
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));
    $docRoot     = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $base = ($docRoot !== '' && str_starts_with($projectRoot, $docRoot))
        ? substr($projectRoot, strlen($docRoot))
        : '';
    define('BASE_URL', rtrim($base, '/'));
}
