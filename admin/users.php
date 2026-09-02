<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$notice = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $notice = 'Session expired — please try again.';
    } elseif (isset($_POST['update_user'])) {
        $id     = (int)$_POST['user_id'];
        $role   = $_POST['role'] ?? '';
        $status = $_POST['status'] ?? '';

        if ($id === (int)$_SESSION['user_id'] && $role !== 'admin') {
            $notice = "You can't remove your own admin role.";
        } elseif (in_array($role, ['admin', 'planner', 'client'], true) && in_array($status, ['active', 'disabled'], true)) {
            $stmt = $pdo->prepare('UPDATE users SET role = ?, status = ? WHERE user_id = ?');
            $stmt->execute([$role, $status, $id]);
            $notice = 'User updated.';
        }
    } elseif (isset($_POST['add_staff'])) {
        $name  = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role  = $_POST['role'] ?? 'planner';
        $temp  = bin2hex(random_bytes(6)); // temporary password, shown once below

        if ($msg = validate_name($name)) $errors['full_name'] = $msg;
        if ($msg = validate_email_address($email)) $errors['email'] = $msg;
        if (!in_array($role, ['admin', 'planner'], true)) $errors['role'] = 'Invalid role.';

        if (empty($errors)) {
            $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $errors['email'] = 'An account with that email already exists.';
            } else {
                $hash = password_hash($temp, PASSWORD_BCRYPT);
                $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (?,?,?,?)')
                    ->execute([$name, $email, $hash, $role]);
                $notice = "Staff account created for $email. Temporary password: $temp (share this securely and ask them to change it after logging in).";
            }
        }
    }
}

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Manage Users — Admin';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap"><span class="eyebrow">Admin</span><h1>Manage users</h1></div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <?php admin_nav('users'); ?>

        <div>
          <?php if ($notice): ?>
            <div class="form-status success is-visible" role="status" style="display:block;"><?= e($notice) ?></div>
          <?php endif; ?>

          <div class="form-card" style="margin-bottom:32px;">
            <h2 style="font-size:1.1rem; margin-bottom:16px;">Add a planner or admin account</h2>
            <p style="color:var(--muted); font-size:0.9rem; margin-top:-8px; margin-bottom:18px;">
              Clients self-register from the public site. Use this only for staff accounts —
              a random temporary password is generated and shown once.
            </p>
            <form method="post" novalidate>
              <?= csrf_field() ?>
              <div class="field-row">
                <div class="field">
                  <label for="full_name">Full name <span class="required">*</span></label>
                  <input type="text" id="full_name" name="full_name" required>
                  <span class="field-error"><?= e($errors['full_name'] ?? '') ?></span>
                </div>
                <div class="field">
                  <label for="email">Email <span class="required">*</span></label>
                  <input type="email" id="email" name="email" required>
                  <span class="field-error"><?= e($errors['email'] ?? '') ?></span>
                </div>
              </div>
              <div class="field">
                <label for="role">Role <span class="required">*</span></label>
                <select id="role" name="role" required>
                  <option value="planner">Planner</option>
                  <option value="admin">Admin</option>
                </select>
                <span class="field-error"><?= e($errors['role'] ?? '') ?></span>
              </div>
              <button type="submit" name="add_staff" value="1" class="btn btn-primary">Create account</button>
            </form>
          </div>

          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Name</th><th>Email</th><th>Role &amp; status</th><th>Joined</th></tr></thead>
              <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                  <td><?= e($u['full_name']) ?></td>
                  <td><?= e($u['email']) ?></td>
                  <td>
                    <form method="post" class="inline-form action-row">
                      <?= csrf_field() ?>
                      <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                      <select name="role" aria-label="Role">
                        <?php foreach (['admin','planner','client'] as $r): ?>
                          <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <select name="status" aria-label="Account status">
                        <option value="active" <?= $u['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="disabled" <?= $u['status'] === 'disabled' ? 'selected' : '' ?>>Disabled</option>
                      </select>
                      <button type="submit" name="update_user" value="1" class="btn btn-ghost btn-small">Save</button>
                    </form>
                  </td>
                  <td><?= e(date('j M Y', strtotime($u['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
