<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$totalEnquiries = (int) $pdo->query('SELECT COUNT(*) c FROM enquiries')->fetch()['c'];
$newEnquiries   = (int) $pdo->query("SELECT COUNT(*) c FROM enquiries WHERE status = 'new'")->fetch()['c'];
$totalUsers     = (int) $pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'];
$pendingReviews = (int) $pdo->query("SELECT COUNT(*) c FROM testimonials WHERE status = 'pending'")->fetch()['c'];
$totalPackages  = (int) $pdo->query('SELECT COUNT(*) c FROM packages')->fetch()['c'];
$totalGallery   = (int) $pdo->query('SELECT COUNT(*) c FROM gallery')->fetch()['c'];

$recentEnquiries = $pdo->query(
    'SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

$pageTitle = 'Admin Dashboard — Adesh & Co.';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap">
        <span class="eyebrow">Admin dashboard</span>
        <h1>Overview</h1>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <?php admin_nav('overview'); ?>

        <div>
          <div class="dash-stats">
            <div class="dash-stat"><span class="num"><?= $totalEnquiries ?></span><span class="label">Total enquiries</span></div>
            <div class="dash-stat"><span class="num"><?= $newEnquiries ?></span><span class="label">New (unactioned)</span></div>
            <div class="dash-stat"><span class="num"><?= $totalUsers ?></span><span class="label">Registered users</span></div>
            <div class="dash-stat"><span class="num"><?= $pendingReviews ?></span><span class="label">Testimonials pending</span></div>
            <div class="dash-stat"><span class="num"><?= $totalPackages ?></span><span class="label">Packages</span></div>
            <div class="dash-stat"><span class="num"><?= $totalGallery ?></span><span class="label">Gallery images</span></div>
          </div>

          <h2 style="font-size:1.2rem; margin-bottom:14px;">Recent enquiries</h2>
          <?php if (empty($recentEnquiries)): ?>
            <p>No enquiries yet.</p>
          <?php else: ?>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Client</th><th>Event</th><th>Date</th><th>Status</th><th>Submitted</th></tr></thead>
              <tbody>
                <?php foreach ($recentEnquiries as $en): [$label, $class] = status_badge($en['status']); ?>
                <tr>
                  <td><?= e($en['full_name']) ?></td>
                  <td><?= e(ucfirst($en['event_type'])) ?></td>
                  <td><?= e(date('j M Y', strtotime($en['event_date']))) ?></td>
                  <td><span class="badge <?= $class ?>"><?= e($label) ?></span></td>
                  <td><?= e(date('j M Y', strtotime($en['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <p style="margin-top:14px;"><a class="btn btn-ghost btn-small" href="<?= BASE_URL ?>/admin/enquiries.php">Manage all enquiries →</a></p>
          <?php endif; ?>
        </div>
      </div>
    </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
