<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$success = '';
$old = ['name' => '', 'email' => '', 'phone' => '', 'eventType' => '', 'eventDate' => '', 'message' => ''];

if (is_logged_in()) {
    $old['name']  = current_name();
    $old['email'] = $_SESSION['email'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $errors['form'] = 'Your session expired — please try submitting again.';
    } else {
        $old['name']      = trim($_POST['name'] ?? '');
        $old['email']     = trim($_POST['email'] ?? '');
        $old['phone']     = trim($_POST['phone'] ?? '');
        $old['eventType'] = $_POST['eventType'] ?? '';
        $old['eventDate'] = $_POST['eventDate'] ?? '';
        $old['message']   = trim($_POST['message'] ?? '');
        $consent          = isset($_POST['consent']);

        if ($msg = validate_name($old['name']))               $errors['name'] = $msg;
        if ($msg = validate_email_address($old['email']))     $errors['email'] = $msg;
        if ($msg = validate_phone($old['phone']))              $errors['phone'] = $msg;
        if ($msg = validate_event_type($old['eventType']))     $errors['eventType'] = $msg;
        if ($msg = validate_event_date($old['eventDate']))     $errors['eventDate'] = $msg;
        if ($msg = validate_message($old['message']))          $errors['message'] = $msg;
        if (!$consent) $errors['consent'] = 'Please agree to be contacted before submitting.';

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'INSERT INTO enquiries (user_id, full_name, email, phone, event_type, event_date, message)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                is_logged_in() ? $_SESSION['user_id'] : null,
                $old['name'],
                $old['email'],
                $old['phone'] ?: null,
                $old['eventType'],
                $old['eventDate'],
                $old['message'],
            ]);
            $success = 'Thanks, ' . explode(' ', $old['name'])[0] . '! Your enquiry has been received. Adesh will reply within one business day.';
            $old = ['name' => '', 'email' => '', 'phone' => '', 'eventType' => '', 'eventDate' => '', 'message' => ''];
        }
    }
}

$pageTitle       = 'Contact — Adesh & Co.';
$pageDescription = 'Get in touch with Adesh Pokhrel at Adesh & Co. to book a free consultation. Based in Hurstville, servicing Sydney-wide.';
$activeNav       = 'contact';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header">
      <div class="wrap">
        <span class="eyebrow">Contact</span>
        <h1>Book your free consultation</h1>
        <p>Fill in the form and Adesh will get back to you within one business day — or reach out directly using the details alongside it.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap contact-layout">

        <div class="reveal">
          <div class="contact-info-card">
            <h2 style="font-size:1.1rem;">Get in touch</h2>
            <dl>
              <dt>Contact</dt>
              <dd>Adesh Pokhrel, Founder &amp; Lead Planner</dd>
              <dt>Location</dt>
              <dd>Hurstville, NSW 2220 — servicing Sydney-wide</dd>
              <dt>Phone</dt>
              <dd><a href="tel:+61478054129">0478 054 129</a></dd>
              <dt>Email</dt>
              <dd><a href="mailto:hello@adeshandco.com.au">hello@adeshandco.com.au</a></dd>
              <dt>Enquiry Hours</dt>
              <dd>Mon&ndash;Fri 9:00&ndash;18:00 &middot; Sat 10:00&ndash;14:00</dd>
            </dl>
            <div class="social-row">
              <a href="https://instagram.com" target="_blank" rel="noopener" aria-label="Adesh & Co. on Instagram">IG</a>
              <a href="https://facebook.com" target="_blank" rel="noopener" aria-label="Adesh & Co. on Facebook">FB</a>
              <a href="https://pinterest.com" target="_blank" rel="noopener" aria-label="Adesh & Co. on Pinterest">PIN</a>
            </div>
            <div class="map-block">
              <iframe
                title="Map showing Adesh & Co. service area in Hurstville, Sydney"
                src="https://maps.google.com/maps?q=Hurstville%20NSW%202220&t=&z=14&ie=UTF8&iwloc=&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          </div>
        </div>

        <div class="reveal">
          <div class="form-card">
            <div class="form-status <?= $success ? 'success is-visible' : (!empty($errors['form']) ? 'error is-visible' : '') ?>" id="form-status" aria-live="polite">
              <?= e($success ?: ($errors['form'] ?? '')) ?>
            </div>

            <form id="contact-form" method="post" novalidate>
              <?= csrf_field() ?>
              <div class="field-row">
                <div class="field">
                  <label for="name">Full name <span class="required">*</span></label>
                  <input type="text" id="name" name="name" placeholder="Jordan Lee" required autocomplete="name" aria-describedby="name-error" value="<?= e($old['name']) ?>">
                  <span class="field-error" id="name-error"><?= e($errors['name'] ?? '') ?></span>
                </div>
                <div class="field">
                  <label for="email">Email <span class="required">*</span></label>
                  <input type="email" id="email" name="email" placeholder="jordan@example.com" required autocomplete="email" aria-describedby="email-error" value="<?= e($old['email']) ?>">
                  <span class="field-error" id="email-error"><?= e($errors['email'] ?? '') ?></span>
                </div>
              </div>

              <div class="field-row">
                <div class="field">
                  <label for="phone">Phone (optional)</label>
                  <input type="tel" id="phone" name="phone" placeholder="04XX XXX XXX" autocomplete="tel" aria-describedby="phone-error" value="<?= e($old['phone']) ?>">
                  <span class="field-error" id="phone-error"><?= e($errors['phone'] ?? '') ?></span>
                </div>
                <div class="field">
                  <label for="eventType">Event type <span class="required">*</span></label>
                  <select id="eventType" name="eventType" required aria-describedby="eventType-error">
                    <option value="" <?= $old['eventType'] === '' ? 'selected' : '' ?> disabled>Select one</option>
                    <option value="wedding" <?= $old['eventType'] === 'wedding' ? 'selected' : '' ?>>Wedding</option>
                    <option value="corporate" <?= $old['eventType'] === 'corporate' ? 'selected' : '' ?>>Corporate event</option>
                    <option value="celebration" <?= $old['eventType'] === 'celebration' ? 'selected' : '' ?>>Private celebration</option>
                    <option value="other" <?= $old['eventType'] === 'other' ? 'selected' : '' ?>>Other</option>
                  </select>
                  <span class="field-error" id="eventType-error"><?= e($errors['eventType'] ?? '') ?></span>
                </div>
              </div>

              <div class="field">
                <label for="eventDate">Preferred event date <span class="required">*</span></label>
                <input type="date" id="eventDate" name="eventDate" required aria-describedby="eventDate-error" value="<?= e($old['eventDate']) ?>">
                <span class="field-error" id="eventDate-error"><?= e($errors['eventDate'] ?? '') ?></span>
              </div>

              <div class="field">
                <label for="message">Tell us about your event <span class="required">*</span></label>
                <textarea id="message" name="message" placeholder="e.g. Roughly 100 guests, looking for a venue in southern Sydney..." required aria-describedby="message-error"><?= e($old['message']) ?></textarea>
                <span class="field-error" id="message-error"><?= e($errors['message'] ?? '') ?></span>
              </div>

              <div class="checkbox-row">
                <input type="checkbox" id="consent" name="consent" required aria-describedby="consent-error">
                <label for="consent">I agree to be contacted by Adesh &amp; Co. about my enquiry.</label>
              </div>
              <span class="field-error" id="consent-error" style="margin-top:-16px; margin-bottom:20px; display:block;"><?= e($errors['consent'] ?? '') ?></span>

              <button type="submit" class="btn btn-primary">Send Enquiry</button>
            </form>
          </div>
        </div>

      </div>
    </section>
<?php
$pageScripts = ['/js/contact.js'];
require __DIR__ . '/includes/footer.php';
?>
