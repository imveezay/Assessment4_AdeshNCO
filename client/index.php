<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['client']);

$stmt = $pdo->prepare('SELECT * FROM enquiries WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$_SESSION['user_id']]);
$enquiries = $stmt->fetchAll();

$stmt2 = $pdo->prepare('SELECT * FROM testimonials WHERE user_id = ? ORDER BY created_at DESC');
$stmt2->execute([$_SESSION['user_id']]);
$myTestimonials = $stmt2->fetchAll();

$pageTitle = 'My Dashboard — Adesh & Co.';
require __DIR__ . '/../includes/header.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap">
        <span class="eyebrow">Client dashboard</span>
        <h1>Welcome back, <?= e(current_name()) ?></h1>
        <p>Track the enquiries you've sent us and the testimonials you've left.</p>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <nav class="dash-nav" aria-label="Client dashboard">
          <h2>My Account</h2>
          <ul>
            <li><a href="<?= BASE_URL ?>/client/index.php" aria-current="page">My enquiries</a></li>
            <li><a href="<?= BASE_URL ?>/testimonials.php">Leave a testimonial</a></li>
            <li><a href="<?= BASE_URL ?>/contact.php">Submit new enquiry</a></li>
            <li><a href="<?= BASE_URL ?>/logout.php">Log out</a></li>
          </ul>
        </nav>

        <div>
          <h2 style="font-size:1.2rem; margin-bottom:14px;">My enquiries</h2>
          <?php if (empty($enquiries)): ?>
            <p>You haven't submitted an enquiry yet. <a href="<?= BASE_URL ?>/contact.php">Send one now</a>.</p>
          <?php else: ?>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead>
                <tr><th>Event type</th><th>Preferred date</th><th>Message</th><th>Status</th><th>Submitted</th></tr>
              </thead>
              <tbody>
                <?php foreach ($enquiries as $en): [$label, $class] = status_badge($en['status']); ?>
                <tr>
                  <td><?= e(ucfirst($en['event_type'])) ?></td>
                  <td><?= e(date('j M Y', strtotime($en['event_date']))) ?></td>
                  <td class="wrap-cell"><?= e(mb_strimwidth($en['message'], 0, 90, '…')) ?></td>
                  <td><span class="badge <?= $class ?>"><?= e($label) ?></span></td>
                  <td><?= e(date('j M Y', strtotime($en['created_at']))) ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>

          <h2 style="font-size:1.2rem; margin:34px 0 14px;">My testimonials</h2>
          <?php if (empty($myTestimonials)): ?>
            <p>You haven't left a testimonial yet. <a href="<?= BASE_URL ?>/testimonials.php">Leave one here</a>.</p>
          <?php else: ?>
          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Event type</th><th>Rating</th><th>Quote</th><th>Status</th></tr></thead>
              <tbody>
                <?php foreach ($myTestimonials as $t): [$label, $class] = status_badge($t['status']); ?>
                <tr>
                  <td><?= e($t['event_type']) ?></td>
                  <td><?= str_repeat('★', (int)$t['rating']) ?></td>
                  <td class="wrap-cell"><?= e(mb_strimwidth($t['quote'], 0, 90, '…')) ?></td>
                  <td><span class="badge <?= $class ?>"><?= e($label) ?></span></td>
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
