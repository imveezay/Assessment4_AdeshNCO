<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$packages = $pdo->query(
    'SELECT * FROM packages ORDER BY display_order ASC'
)->fetchAll();

$pageTitle       = 'Services — Adesh & Co.';
$pageDescription = 'Full Planning, Day-of Coordination and Styling packages for weddings, corporate events and private celebrations at Adesh & Co.';
$activeNav       = 'services';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header">
      <div class="wrap">
        <span class="eyebrow">Services &amp; Packages</span>
        <h1>Three packages, tailored from there</h1>
        <p>These are starting points — every quote is adjusted after a free consultation based on guest count, venue and vision. No package is ever locked in without a written quote first.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="grid-3 reveal-group">
          <?php foreach ($packages as $pkg): ?>
          <article class="package-card ticket reveal <?= $pkg['is_featured'] ? 'package-card--featured' : '' ?>">
            <div class="package-card__head">
              <span class="tag"><?= e($pkg['tagline']) ?></span>
              <h2 style="font-size:1.4rem; margin: 6px 0 0;"><?= e($pkg['name']) ?></h2>
              <p class="package-card__price">From <?= format_price($pkg['price_from']) ?><span> / event</span></p>
            </div>
            <div class="package-card__body">
              <ul>
                <?php foreach (explode(',', $pkg['description']) as $line): ?>
                  <?php $line = trim($line); if ($line === '') continue; ?>
                  <li><?= e($line) ?></li>
                <?php endforeach; ?>
              </ul>
              <a href="<?= BASE_URL ?>/contact.php" class="btn <?= $pkg['is_featured'] ? 'btn-primary' : 'btn-ghost' ?>" style="margin-top:auto;">Enquire</a>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <div class="divider-dots" role="presentation"></div>

    <section class="section section--panel">
      <div class="wrap">
        <div class="section-head reveal">
          <span class="eyebrow">Event types</span>
          <h2>What we plan most</h2>
        </div>
        <div class="grid-3 reveal-group">
          <div class="card reveal">
            <h3>Weddings</h3>
            <p>Ceremony and reception, single venue or split-site. Guest lists from 40 to 250, run by one lead planner throughout.</p>
          </div>
          <div class="card reveal">
            <h3>Corporate Events</h3>
            <p>Launches, conferences, and end-of-year functions. We work to brand guidelines and can manage multi-vendor AV setups.</p>
          </div>
          <div class="card reveal">
            <h3>Private Celebrations</h3>
            <p>Birthdays, engagements, baby showers and anniversaries — planned with the same rigour as a wedding, scaled to the guest count.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-band">
      <div class="wrap">
        <h2 class="reveal">Not sure which package fits?</h2>
        <p class="reveal">Tell us about your event and Adesh will recommend a starting point — no pressure, no obligation.</p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary reveal">Talk to Adesh</a>
      </div>
    </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
