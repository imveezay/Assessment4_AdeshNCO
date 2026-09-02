<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$success = '';

// Handle a logged-in client submitting a new testimonial (goes to "pending" for admin approval).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    if (!is_logged_in() || current_role() !== 'client') {
        $errors['form'] = 'Please log in as a client to leave a testimonial.';
    } elseif (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'Your session expired — please try again.';
    } else {
        $eventType = trim($_POST['event_type'] ?? '');
        $rating    = $_POST['rating'] ?? '';
        $quote     = trim($_POST['quote'] ?? '');

        if ($eventType === '' || mb_strlen($eventType) > 100) $errors['event_type'] = 'Enter the type of event (max 100 characters).';
        if ($msg = validate_rating($rating)) $errors['rating'] = $msg;
        if ($msg = validate_message($quote)) $errors['quote'] = $msg;

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'INSERT INTO testimonials (user_id, client_name, event_type, rating, quote, status)
                 VALUES (?, ?, ?, ?, ?, "pending")'
            );
            $stmt->execute([$_SESSION['user_id'], current_name(), $eventType, (int)$rating, $quote]);
            $success = 'Thanks! Your testimonial has been submitted and will appear once approved.';
        }
    }
}

$testimonials = $pdo->query(
    "SELECT * FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC LIMIT 6"
)->fetchAll();

$stats = $pdo->query(
    "SELECT COUNT(*) AS total, AVG(rating) AS avg_rating FROM testimonials WHERE status = 'approved'"
)->fetch();
$eventsDelivered = (int) $pdo->query("SELECT COUNT(*) AS c FROM enquiries WHERE status = 'confirmed'")->fetch()['c'];

$pageTitle       = 'Testimonials — Adesh & Co.';
$pageDescription = 'What clients say about planning their wedding, corporate event or celebration with Adesh & Co.';
$activeNav       = 'testimonials';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header">
      <div class="wrap">
        <span class="eyebrow">Testimonials</span>
        <h1>What clients say afterwards</h1>
        <p>We ask every client for honest feedback once the dust settles. Here's a sample of what's come back.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="result-stats reveal-group">
          <div class="reveal">
            <span class="num"><?= $eventsDelivered > 0 ? e((string)$eventsDelivered) . '+' : '180+' ?></span>
            <span class="label">Events delivered</span>
          </div>
          <div class="reveal">
            <span class="num">98%</span>
            <span class="label">Would recommend us</span>
          </div>
          <div class="reveal">
            <span class="num"><?= $stats['total'] > 0 ? number_format((float)$stats['avg_rating'], 1) : '4.9' ?>/5</span>
            <span class="label">Average client rating</span>
          </div>
          <div class="reveal">
            <span class="num">6yr</span>
            <span class="label">Planning events in Sydney</span>
          </div>
        </div>
      </div>
    </section>

    <div class="divider-dots" role="presentation"></div>

    <section class="section section--panel">
      <div class="wrap">
        <div class="section-head reveal">
          <span class="eyebrow">In their words</span>
          <h2>Client stories</h2>
        </div>
        <div class="testimonial-grid reveal-group">
          <?php foreach ($testimonials as $t): ?>
          <div class="testimonial-card reveal">
            <p class="stars" aria-label="<?= (int)$t['rating'] ?> out of 5 stars">
              <?= str_repeat('&#9733;', (int)$t['rating']) . str_repeat('&#9734;', 5 - (int)$t['rating']) ?>
            </p>
            <p class="testimonial-quote">&ldquo;<?= e($t['quote']) ?>&rdquo;</p>
            <div class="testimonial-person">
              <div class="testimonial-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($t['client_name'], 0, 1))) ?></div>
              <div>
                <div class="testimonial-name"><?= e($t['client_name']) ?></div>
                <div class="testimonial-stat"><?= e($t['event_type']) ?></div>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="wrap" style="max-width: 640px;">
        <div class="section-head reveal">
          <span class="eyebrow">Add your own</span>
          <h2>Been a client? Leave a testimonial</h2>
        </div>

        <?php if (!is_logged_in() || current_role() !== 'client'): ?>
          <p>Please <a href="<?= BASE_URL ?>/login.php">log in with your client account</a> to leave a testimonial. Don't have one yet? <a href="<?= BASE_URL ?>/register.php">Register here</a>.</p>
        <?php else: ?>
          <div class="form-card reveal">
            <?php if ($success): ?>
              <div class="form-status success is-visible" role="status"><?= e($success) ?></div>
            <?php elseif (!empty($errors['form'])): ?>
              <div class="form-status error is-visible" role="alert"><?= e($errors['form']) ?></div>
            <?php endif; ?>

            <form method="post" novalidate data-validate-form>
              <?= csrf_field() ?>
              <div class="field">
                <label for="event_type">Event type <span class="required">*</span></label>
                <input type="text" id="event_type" name="event_type" placeholder="e.g. Wedding · Full Planning" required value="<?= e($_POST['event_type'] ?? '') ?>">
                <span class="field-error"><?= e($errors['event_type'] ?? '') ?></span>
              </div>
              <div class="field">
                <label for="rating">Rating <span class="required">*</span></label>
                <select id="rating" name="rating" required>
                  <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= (($_POST['rating'] ?? '5') == $i) ? 'selected' : '' ?>><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option>
                  <?php endfor; ?>
                </select>
                <span class="field-error"><?= e($errors['rating'] ?? '') ?></span>
              </div>
              <div class="field">
                <label for="quote">Your experience <span class="required">*</span></label>
                <textarea id="quote" name="quote" required data-validate="quote" aria-describedby="quote-error"><?= e($_POST['quote'] ?? '') ?></textarea>
                <span class="field-error" id="quote-error"><?= e($errors['quote'] ?? '') ?></span>
              </div>
              <button type="submit" name="submit_testimonial" value="1" class="btn btn-primary">Submit for review</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="cta-band">
      <div class="wrap">
        <h2 class="reveal">Add your own story</h2>
        <p class="reveal">Book a free consultation and let's talk through what you're planning.</p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary reveal">Book a Free Consultation</a>
      </div>
    </section>
<?php
$pageScripts = ['/js/validate.js'];
require __DIR__ . '/includes/footer.php';
?>
