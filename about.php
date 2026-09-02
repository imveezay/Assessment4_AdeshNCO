<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'About Us — Adesh & Co.';
$pageDescription = 'Meet Adesh Pokhrel and the team behind Adesh & Co., an event management studio based in Hurstville, Sydney.';
$activeNav       = 'about';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header">
      <div class="wrap">
        <span class="eyebrow">About Us</span>
        <h1>A studio built on one phone number</h1>
        <p>Adesh &amp; Co. is small by design — so every event still gets Adesh's direct attention, from first enquiry to final send-off.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap split">
        <div class="media-block reveal" style="border-radius: var(--radius-md); overflow:hidden;">
          <img src="<?= BASE_URL ?>/images/1083-400x501.jpg" alt="Adesh Pokhrel reviewing a floor plan and seating chart ahead of an event" loading="lazy">
        </div>
        <div class="reveal">
          <span class="eyebrow">Our story</span>
          <h2>From family favours to a full studio</h2>
          <p>
            Adesh Pokhrel started planning events almost by accident — a cousin's engagement
            party in 2018, then a friend's 40th, then a work Christmas function that somehow
            landed in his inbox. By 2019, Adesh &amp; Co. was registered as a proper business,
            still based out of Hurstville.
          </p>
          <p>
            The studio has grown to a small team of planners and stylists, but the promise from
            year one hasn't moved: you deal directly with the person planning your event, not a
            rotating account manager.
          </p>
          <div class="value-list">
            <div class="reveal">
              <span class="value-num">01 — One point of contact</span>
              <p>You work with the same planner from consultation through to the event itself.</p>
            </div>
            <div class="reveal">
              <span class="value-num">02 — Transparent budgets</span>
              <p>Every quote is itemised. No surprise line items appear the week of your event.</p>
            </div>
            <div class="reveal">
              <span class="value-num">03 — Local vendor network</span>
              <p>Six years of working the same florists, caterers and venues across southern Sydney.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="divider-dots" role="presentation"></div>

    <section class="section section--panel">
      <div class="wrap">
        <div class="section-head reveal">
          <span class="eyebrow">The team</span>
          <h2>Who you'll be working with</h2>
          <p>A small studio of planners and stylists led by Adesh Pokhrel.</p>
        </div>
        <div class="team-grid reveal-group">
          <div class="team-card reveal">
            <div class="team-photo">
              <img src="<?= BASE_URL ?>/images/670-400x500.jpg" alt="Portrait of Adesh Pokhrel, Founder and Lead Planner at Adesh & Co." loading="lazy">
            </div>
            <h3>Adesh Pokhrel</h3>
            <span class="team-role">Founder &amp; Lead Planner</span>
            <p>Plans and personally coordinates weddings and large corporate functions. Based in Hurstville.</p>
          </div>
          <div class="team-card reveal">
            <div class="team-photo">
              <img src="<?= BASE_URL ?>/images/362-500x705.jpg" alt="Portrait of Meera Shah, event stylist at Adesh & Co." loading="lazy">
            </div>
            <h3>Meera Shah</h3>
            <span class="team-role">Event Stylist</span>
            <p>Leads florals, tablescapes and venue styling across every Adesh &amp; Co. event.</p>
          </div>
          <div class="team-card reveal">
            <div class="team-photo">
              <img src="<?= BASE_URL ?>/images/390-400x502.jpg" alt="Portrait of Liam Chen, day-of coordinator at Adesh & Co." loading="lazy">
            </div>
            <h3>Liam Chen</h3>
            <span class="team-role">Day-of Coordinator</span>
            <p>Runs the floor on event day — vendor arrivals, run sheets and timing, so nothing slips.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="cta-band">
      <div class="wrap">
        <h2 class="reveal">Ready to start planning?</h2>
        <p class="reveal">Book a free consultation with Adesh and tell us what you're picturing — we'll take it from there.</p>
        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-primary reveal">Book a Free Consultation</a>
      </div>
    </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
