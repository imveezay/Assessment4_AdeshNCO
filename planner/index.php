<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['planner', 'admin']);

$notice = '';

// Update the status of an enquiry assigned to this planner.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $notice = 'Session expired — please try again.';
    } else {
        $enquiryId = (int)($_POST['enquiry_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        $allowed = ['new', 'in_progress', 'confirmed', 'closed'];

        if (in_array($newStatus, $allowed, true)) {
            // Planners may only touch enquiries assigned to them; admins may touch any.
            if (current_role() === 'admin') {
                $stmt = $pdo->prepare('UPDATE enquiries SET status = ? WHERE enquiry_id = ?');
                $stmt->execute([$newStatus, $enquiryId]);
            } else {
                $stmt = $pdo->prepare('UPDATE enquiries SET status = ? WHERE enquiry_id = ? AND assigned_to = ?');
                $stmt->execute([$newStatus, $enquiryId, $_SESSION['user_id']]);
            }
            $notice = 'Enquiry status updated.';
        } else {
            $notice = 'Invalid status.';
        }
    }
}

$stmt = $pdo->prepare(
    'SELECT * FROM enquiries WHERE assigned_to = ? ORDER BY event_date ASC'
);
$stmt->execute([$_SESSION['user_id']]);
$myEnquiries = $stmt->fetchAll();

$pageTitle = 'Planner Dashboard — Adesh & Co.';
require __DIR__ . '/../includes/header.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap">
        <span class="eyebrow">Planner dashboard</span>
        <h1>Your assigned events</h1>
        <p>Update the status of each enquiry as it moves through planning.</p>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <nav class="dash-nav" aria-label="Planner dashboard">
          <h2>Planner</h2>
          <ul>
            <li><a href="<?= BASE_URL ?>/planner/index.php" aria-current="page">Assigned enquiries</a></li>
            <li><a href="<?= BASE_URL ?>/logout.php">Log out</a></li>
          </ul>
        </nav>

        <div>
          <?php if ($notice): ?>
            <div class="form-status success is-visible" role="status" style="display:block;"><?= e($notice) ?></div>
          <?php endif; ?>

          <?php if (empty($myEnquiries)): ?>
            <p>No enquiries are assigned to you yet.</p>
          <?php else: ?>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Client</th><th>Event</th><th>Date</th><th>Message</th><th>Status</th></tr>
              </thead>
              <tbody>
                <?php foreach ($myEnquiries as $en): ?>
                <tr>
                  <td><?= e($en['full_name']) ?><br><small style="color:var(--muted);"><?= e($en['email']) ?></small></td>
                  <td><?= e(ucfirst($en['event_type'])) ?></td>
                  <td><?= e(date('j M Y', strtotime($en['event_date']))) ?></td>
                  <td class="wrap-cell"><?= e($en['message']) ?></td>
                  <td>
                    <form method="post" class="inline-form">
                      <?= csrf_field() ?>
                      <input type="hidden" name="enquiry_id" value="<?= (int)$en['enquiry_id'] ?>">
                      <select name="status" onchange="this.form.submit()" aria-label="Update status for <?= e($en['full_name']) ?>">
                        <?php foreach (['new','in_progress','confirmed','closed'] as $s): ?>
                          <option value="<?= $s ?>" <?= $en['status'] === $s ? 'selected' : '' ?>><?= e(status_badge($s)[0]) ?></option>
                        <?php endforeach; ?>
                      </select>
                      <input type="hidden" name="update_status" value="1">
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
