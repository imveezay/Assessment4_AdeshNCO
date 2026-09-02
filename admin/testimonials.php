<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $notice = 'Session expired — please try again.';
    } elseif (isset($_POST['set_status'])) {
        $id = (int)$_POST['testimonial_id'];
        $status = $_POST['status'] ?? '';
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $pdo->prepare('UPDATE testimonials SET status = ? WHERE testimonial_id = ?')->execute([$status, $id]);
            $notice = 'Testimonial updated.';
        }
    } elseif (isset($_POST['delete_testimonial'])) {
        $id = (int)$_POST['testimonial_id'];
        $pdo->prepare('DELETE FROM testimonials WHERE testimonial_id = ?')->execute([$id]);
        $notice = 'Testimonial deleted.';
    }
}

$testimonials = $pdo->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Manage Testimonials — Admin';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap"><span class="eyebrow">Admin</span><h1>Manage testimonials</h1></div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <?php admin_nav('testimonials'); ?>

        <div>
          <?php if ($notice): ?>
            <div class="form-status success is-visible" role="status" style="display:block;"><?= e($notice) ?></div>
          <?php endif; ?>

          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Client</th><th>Event</th><th>Rating</th><th>Quote</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($testimonials as $t): [$label, $class] = status_badge($t['status']); ?>
                <tr>
                  <td><?= e($t['client_name']) ?></td>
                  <td><?= e($t['event_type']) ?></td>
                  <td><?= str_repeat('★', (int)$t['rating']) ?></td>
                  <td class="wrap-cell"><?= e(mb_strimwidth($t['quote'], 0, 90, '…')) ?></td>
                  <td><span class="badge <?= $class ?>"><?= e($label) ?></span></td>
                  <td class="action-row">
                    <?php if ($t['status'] !== 'approved'): ?>
                    <form method="post" class="inline-form">
                      <?= csrf_field() ?>
                      <input type="hidden" name="testimonial_id" value="<?= (int)$t['testimonial_id'] ?>">
                      <input type="hidden" name="status" value="approved">
                      <button type="submit" name="set_status" value="1" class="btn btn-ghost btn-small">Approve</button>
                    </form>
                    <?php endif; ?>
                    <?php if ($t['status'] !== 'rejected'): ?>
                    <form method="post" class="inline-form">
                      <?= csrf_field() ?>
                      <input type="hidden" name="testimonial_id" value="<?= (int)$t['testimonial_id'] ?>">
                      <input type="hidden" name="status" value="rejected">
                      <button type="submit" name="set_status" value="1" class="btn btn-ghost btn-small">Reject</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this testimonial permanently?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="testimonial_id" value="<?= (int)$t['testimonial_id'] ?>">
                      <button type="submit" name="delete_testimonial" value="1" class="btn btn-danger btn-small">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
