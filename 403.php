<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Access denied — Adesh & Co.';
$pageDescription = 'You do not have permission to view this page.';
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="wrap">
    <h1>403 — Access denied</h1>
    <p>Your account (<?= e(current_role() ?? 'guest') ?>) does not have permission to view this page.</p>
    <p><a class="btn btn-primary" href="<?= BASE_URL ?>/index.php">Back to homepage</a></p>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
