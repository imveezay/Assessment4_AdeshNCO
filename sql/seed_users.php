<?php
/**
 * One-time setup script: creates the three demo accounts (admin, planner,
 * client) with properly bcrypt-hashed passwords. Run this ONCE after
 * importing schema.sql, then delete it or move it outside the web root.
 *
 * Run from the command line:   php seed_users.php
 * Or open it in a browser:     http://localhost/adesh-and-co/sql/seed_users.php
 */

require_once __DIR__ . '/../config/db.php';

$accounts = [
    ['Adesh Pokhrel', 'admin@adeshandco.com.au',   'Passw0rd!', 'admin'],
    ['Sarina Kaur',   'planner@adeshandco.com.au', 'Passw0rd!', 'planner'],
    ['Jordan Lee',    'client@example.com',        'Passw0rd!', 'client'],
];

$inserted = [];
foreach ($accounts as [$name, $email, $plainPassword, $role]) {
    $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
    $check->execute([$email]);
    if ($check->fetch()) {
        $inserted[] = "$email — already exists, skipped";
        continue;
    }

    $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare(
        'INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([$name, $email, $hash, $role]);
    $inserted[] = "$email — created ($role)";
}

$isCli = (php_sapi_name() === 'cli');
$nl = $isCli ? "\n" : "<br>";

echo "Seed accounts processed:" . $nl;
foreach ($inserted as $line) {
    echo "- $line" . $nl;
}
echo $nl . "All seed accounts use the password: Passw0rd!" . $nl;
echo "Log in and change these passwords before any real deployment." . $nl;
