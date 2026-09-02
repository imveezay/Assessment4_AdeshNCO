<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $notice = 'Session expired — please try again.';
    } elseif (isset($_POST['update_enquiry'])) {
        $id       = (int)($_POST['enquiry_id'] ?? 0);
        $status   = $_POST['status'] ?? '';
        $assignee = $_POST['assigned_to'] !== '' ? (int)$_POST['assigned_to'] : null;
        $allowed  = ['new', 'in_progress', 'confirmed', 'closed'];

        if (in_array($status, $allowed, true)) {
            $stmt = $pdo->prepare('UPDATE enquiries SET status = ?, assigned_to = ? WHERE enquiry_id = ?');
            $stmt->execute([$status, $assignee, $id]);
            $notice = 'Enquiry updated.';
        }
    } elseif (isset($_POST['delete_enquiry'])) {
        $id = (int)($_POST['enquiry_id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM enquiries WHERE enquiry_id = ?');
        $stmt->execute([$id]);
        $notice = 'Enquiry deleted.';
    }
}

$enquiries = $pdo->query('SELECT * FROM enquiries ORDER BY created_at DESC')->fetchAll();
$planners  = $pdo->query("SELECT user_id, full_name FROM users WHERE role = 'planner' ORDER BY full_name")->fetchAll();

$pageTitle = 'Manage Enquiries — Admin';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap"><span class="eyebrow">Admin</span><h1>Manage enquiries</h1></div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <?php admin_nav('enquiries'); ?>

        <div>
          <?php if ($notice): ?>
            <div class="form-status success is-visible" role="status" style="display:block;"><?= e($notice) ?></div>
          <?php endif; ?>

          <?php if (empty($enquiries)): ?>
            <p>No enquiries have been submitted yet.</p>
          <?php else: ?>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Client</th><th>Event</th><th>Date</th><th>Message</th><th>Status &amp; assignment</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <?php foreach ($enquiries as $en): ?>
                <tr>
                  <td><?= e($en['full_name']) ?><br><small style="color:var(--muted);"><?= e($en['email']) ?></small></td>
                  <td><?= e(ucfirst($en['event_type'])) ?></td>
                  <td><?= e(date('j M Y', strtotime($en['event_date']))) ?></td>
                  <td class="wrap-cell"><?= e(mb_strimwidth($en['message'], 0, 80, '…')) ?></td>
                  <td>
                    <form method="post" class="inline-form action-row">
                      <?= csrf_field() ?>
                      <input type="hidden" name="enquiry_id" value="<?= (int)$en['enquiry_id'] ?>">
                      <select name="status" aria-label="Status">
                        <?php foreach (['new','in_progress','confirmed','closed'] as $s): ?>
                          <option value="<?= $s ?>" <?= $en['status'] === $s ? 'selected' : '' ?>><?= e(status_badge($s)[0]) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <select name="assigned_to" aria-label="Assign planner">
                        <option value="">Unassigned</option>
                        <?php foreach ($planners as $p): ?>
                          <option value="<?= (int)$p['user_id'] ?>" <?= (int)$en['assigned_to'] === (int)$p['user_id'] ? 'selected' : '' ?>><?= e($p['full_name']) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" name="update_enquiry" value="1" class="btn btn-ghost btn-small">Save</button>
                    </form>
                  </td>
                  <td>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this enquiry permanently?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="enquiry_id" value="<?= (int)$en['enquiry_id'] ?>">
                      <button type="submit" name="delete_enquiry" value="1" class="btn btn-danger btn-small">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
