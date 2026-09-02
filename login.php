<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors = [];
$emailOld = '';
$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';

// Basic login-attempt throttling to slow down brute-force guessing.
if (empty($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'Your session expired — please try again.';
    } elseif ($_SESSION['login_attempts'] >= 8) {
        $errors['form'] = 'Too many attempts — please wait a minute and try again.';
    } else {
        $emailOld = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($emailOld === '' || $password === '') {
            $errors['form'] = 'Enter both your email and password.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$emailOld]);
            $user = $stmt->fetch();

            if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
                $_SESSION['login_attempts'] = 0;
                log_in_user($user);

                if ($redirect && str_starts_with($redirect, '/')) {
                    header('Location: ' . $redirect);
                } else {
                    $dashUrl = match ($user['role']) {
                        'admin'   => BASE_URL . '/admin/index.php',
                        'planner' => BASE_URL . '/planner/index.php',
                        default   => BASE_URL . '/client/index.php',
                    };
                    header('Location: ' . $dashUrl);
                }
                exit;
            }

            $_SESSION['login_attempts']++;
            $errors['form'] = 'Incorrect email or password.';
        }
    }
}

$pageTitle       = 'Log in — Adesh & Co.';
$pageDescription = 'Log in to your Adesh & Co. account.';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap">
        <span class="eyebrow">Account</span>
        <h1>Log in</h1>
        <p>Clients, planners and admins all sign in here — you'll land on the dashboard for your role.</p>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap auth-shell">
        <div class="form-card">
          <?php if (!empty($errors['form'])): ?>
            <div class="form-status error is-visible" role="alert"><?= e($errors['form']) ?></div>
          <?php endif; ?>

          <form method="post" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
            <div class="field">
              <label for="email">Email <span class="required">*</span></label>
              <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($emailOld) ?>">
            </div>
            <div class="field">
              <label for="password">Password <span class="required">*</span></label>
              <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Log in</button>
          </form>
          <p class="auth-switch">New here? <a href="<?= BASE_URL ?>/register.php">Create a client account</a></p>
        </div>
      </div>
    </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
