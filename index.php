<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Pull the three packages for the "What we plan" preview, ordered as set by admin.
$packages = $pdo->query(
    'SELECT name, description FROM packages ORDER BY display_order ASC LIMIT 3'
)->fetchAll();

// Pull a couple of live stats to make the hero numbers data-driven rather than hard-coded.
$eventCount = (int) $pdo->query("SELECT COUNT(*) AS c FROM enquiries WHERE status = 'confirmed'")->fetch()['c'];
$avgRatingRow = $pdo->query("SELECT AVG(rating) AS avg_rating, COUNT(*) AS c FROM testimonials WHERE status = 'approved'")->fetch();
$avgRating = $avgRatingRow['c'] > 0 ? round((float)$avgRatingRow['avg_rating'], 1) : 4.9;

$pageTitle       = 'Adesh & Co. — Event Management, Hurstville & Sydney';
$pageDescription = 'Adesh & Co. is a Hurstville-based event management studio planning weddings, corporate functions and private celebrations across Sydney.';
$activeNav       = 'home';
require __DIR__ . '/includes/header.php';
?>
    <!-- HERO -->
    <section class="hero">
      <div class="wrap hero-inner">
        <div class="hero-copy">
          <span class="eyebrow">Hurstville, NSW &middot; Servicing Sydney-wide</span>
          <h1>Every Gathering,<br><span class="accent">planned to the last detail.</span></h1>
          <p class="hero-lede">
            Adesh &amp; Co. is a Hurstville-based event management studio planning weddings,
            corporate functions and private celebrations across Sydney. We handle every detail —
            venue, styling, vendors and timeline — so you can actually enjoy the day you're hosting.
          </p>
          <div class="hero-actions">
            <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary">Book a Free Consultation</a>
            <a href="<?= BASE_URL ?>/services.php" class="btn btn-ghost">See Packages</a>
          </div>
          <div class="hero-stats">
            <div class="hero-stat">
              <span class="num"><?= $eventCount > 0 ? e((string)$eventCount) . '+' : '180+' ?></span>
              <span class="label">Events planned</span>
            </div>
            <div class="hero-stat">
              <span class="num">6yr</span>
              <span class="label">In business</span>
            </div>
            <div class="hero-stat">
              <span class="num"><?= e((string)$avgRating) ?>/5</span>
              <span class="label">Average client rating</span>
            </div>
          </div>
        </div>
        <div class="invite-stack" aria-hidden="true">
          <div class="invite-card c1">
            <span class="tag">Wedding</span>
            <h3>Priya &amp; Anthony</h3>
            <p>120 guests &middot; Oatley Pavilion</p>
          </div>
          <div class="invite-card c2">
            <span class="tag">Corporate</span>
            <h3>Meridian Launch</h3>
            <p>85 guests &middot; Sydney CBD</p>
          </div>
          <div class="invite-card c3">
            <span class="tag">Milestone</span>
            <h3>Grace's 50th</h3>
            <p>60 guests &middot; Hurstville</p>
          </div>
        </div>
      </div>
    </section>

    <div class="divider-dots" role="presentation"></div>

    <!-- PILLARS -->
    <section class="section">
      <div class="wrap">
        <div class="section-head reveal">
          <span class="eyebrow">How we work</span>
          <h2>Three ways we take the load off</h2>
          <p>Whatever stage your event is at, we slot in where you need us most.</p>
        </div>
        <div class="grid-3 reveal-group">
          <div class="card reveal">
            <div class="icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
            </div>
            <h3>Full Planning</h3>
            <p>From first concept to final send-off — venue sourcing, vendor booking, styling and on-the-day coordination, start to finish.</p>
          </div>
          <div class="card reveal">
            <div class="icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            </div>
            <h3>Day-of Coordination</h3>
            <p>Already planned it yourself? We step in on the day so you're not the one answering vendor calls during the ceremony.</p>
          </div>
          <div class="card reveal">
            <div class="icon" aria-hidden="true">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-7-4.5-9.5-9A5.5 5.5 0 0112 5.5 5.5 5.5 0 0121.5 12c-2.5 4.5-9.5 9-9.5 9z"/></svg>
            </div>
            <h3>Styling &amp; Design</h3>
            <p>Florals, tablescapes, signage and lighting design pulled into one cohesive look for your venue.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ABOUT PREVIEW -->
    <section class="section section--panel">
      <div class="wrap split">
        <div class="reveal">
          <span class="eyebrow">Founded 2019</span>
          <h2>Run by a planner who answers his own phone</h2>
          <p>
            Adesh &amp; Co. was started by Adesh Pokhrel out of Hurstville, after years of
            planning events for friends and family on the side. It's grown into a small studio,
            but the approach hasn't changed — direct communication, no hand-offs to a call centre.
          </p>
          <p>Every enquiry that comes through this site is read and answered personally.</p>
          <a href="<?= BASE_URL ?>/about.php" class="btn btn-ghost">Our Story</a>
        </div>
        <div class="media-block reveal" style="border-radius: var(--radius-md); overflow:hidden;">
          <img src="<?= BASE_URL ?>/images/669-700x560.jpg" alt="Elegantly styled reception table with florals and place settings at an Adesh & Co. event" loading="lazy">
        </div>
      </div>
    </section>

    <div class="divider-dots" role="presentation"></div>

    <!-- SERVICES PREVIEW (data-driven from the packages table) -->
    <section class="section">
      <div class="wrap">
        <div class="section-head reveal">
          <span class="eyebrow">Services</span>
          <h2>What we plan</h2>
          <p>Full packages and pricing on the services page — every event is quoted individually after a free consult.</p>
        </div>
        <div class="grid-3 reveal-group">
          <?php foreach ($packages as $pkg): ?>
          <div class="card reveal">
            <h3><?= e($pkg['name']) ?></h3>
            <p><?= e(mb_strimwidth($pkg['description'], 0, 140, '…')) ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta-band">
      <div class="wrap">
        <h2 class="reveal">Let's talk about your event</h2>
        <p class="reveal">Book a free 30-minute consultation with Adesh — no obligation, just a conversation about what you're planning.</p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary reveal">Book a Free Consultation</a>
      </div>
    </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
