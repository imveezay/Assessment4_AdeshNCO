<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors = [];
$old = ['full_name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'Your session expired — please try again.';
    } else {
        $old['full_name'] = trim($_POST['full_name'] ?? '');
        $old['email']     = trim($_POST['email'] ?? '');
        $old['phone']     = trim($_POST['phone'] ?? '');
        $password         = $_POST['password'] ?? '';
        $confirmPassword  = $_POST['confirm_password'] ?? '';

        if ($msg = validate_name($old['full_name']))           $errors['full_name'] = $msg;
        if ($msg = validate_email_address($old['email']))      $errors['email'] = $msg;
        if ($msg = validate_phone($old['phone']))               $errors['phone'] = $msg;
        if ($msg = validate_password($password))                $errors['password'] = $msg;
        if ($confirmPassword !== $password)                     $errors['confirm_password'] = 'Passwords do not match.';

        // Uniqueness check (only if the email itself passed the format check).
        if (empty($errors['email'])) {
            $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
            $check->execute([$old['email']]);
            if ($check->fetch()) {
                $errors['email'] = 'An account with that email already exists.';
            }
        }

        if (empty($errors)) {
            // New public registrations are always created as "client" accounts.
            // Admin and planner accounts are provisioned separately (see sql/seed_users.php
            // and the admin "Manage users" screen) so the role can't be self-elevated.
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, email, password_hash, role, phone) VALUES (?, ?, ?, "client", ?)'
            );
            $stmt->execute([$old['full_name'], $old['email'], $hash, $old['phone'] ?: null]);

            $newUser = [
                'user_id'   => $pdo->lastInsertId(),
                'full_name' => $old['full_name'],
                'email'     => $old['email'],
                'role'      => 'client',
            ];
            log_in_user($newUser);
            header('Location: ' . BASE_URL . '/client/index.php');
            exit;
        }
    }
}

$pageTitle       = 'Create an account — Adesh & Co.';
$pageDescription = 'Register a client account with Adesh & Co. to track your enquiries and leave testimonials.';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap">
        <span class="eyebrow">Client account</span>
        <h1>Create your account</h1>
        <p>Register to track your enquiries and leave a testimonial once your event is done.</p>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap auth-shell">
        <div class="form-card">
          <?php if (!empty($errors['form'])): ?>
            <div class="form-status error is-visible" role="alert"><?= e($errors['form']) ?></div>
          <?php endif; ?>

          <form method="post" novalidate data-validate-form>
            <?= csrf_field() ?>
            <div class="field">
              <label for="full_name">Full name <span class="required">*</span></label>
              <input type="text" id="full_name" name="full_name" required maxlength="100" autocomplete="name" data-validate="name" aria-describedby="full_name-error" value="<?= e($old['full_name']) ?>">
              <span class="field-error" id="full_name-error"><?= e($errors['full_name'] ?? '') ?></span>
            </div>
            <div class="field">
              <label for="email">Email <span class="required">*</span></label>
              <input type="email" id="email" name="email" required maxlength="150" autocomplete="email" data-validate="email" aria-describedby="email-error" value="<?= e($old['email']) ?>">
              <span class="field-error" id="email-error"><?= e($errors['email'] ?? '') ?></span>
            </div>
            <div class="field">
              <label for="phone">Phone (optional)</label>
              <input type="tel" id="phone" name="phone" maxlength="30" autocomplete="tel" aria-describedby="phone-error" value="<?= e($old['phone']) ?>">
              <span class="field-error" id="phone-error"><?= e($errors['phone'] ?? '') ?></span>
            </div>
            <div class="field">
              <label for="password">Password <span class="required">*</span></label>
              <input type="password" id="password" name="password" required autocomplete="new-password" data-validate="password" aria-describedby="password-error">
              <span class="field-error" id="password-error"><?= e($errors['password'] ?? '') ?></span>
            </div>
            <div class="field">
              <label for="confirm_password">Confirm password <span class="required">*</span></label>
              <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" data-validate="confirmPassword" aria-describedby="confirm_password-error">
              <span class="field-error" id="confirm_password-error"><?= e($errors['confirm_password'] ?? '') ?></span>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Create account</button>
          </form>
          <p class="auth-switch">Already have an account? <a href="<?= BASE_URL ?>/login.php">Log in</a></p>
        </div>
      </div>
    </section>
<?php
$pageScripts = ['/js/validate.js'];
require __DIR__ . '/includes/footer.php';
?>