<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Privacy Notice — Adesh & Co.';
$pageDescription = 'How Adesh & Co. collects, uses and protects the personal information you share with us.';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap">
        <span class="eyebrow">Privacy</span>
        <h1>Privacy Notice</h1>
        <p>Last updated <?= date('j F Y') ?>.</p>
      </div>
    </section>

    <section class="section section--tight">
      <div class="wrap" style="max-width: 760px;">
        <h2>What we collect</h2>
        <p>When you register an account, submit an enquiry, or leave a testimonial, we collect the
        information you provide directly: your name, email address, phone number (optional),
        event details, and any message you write. If you register, your password is never stored
        in plain text — it is hashed using a one-way algorithm (bcrypt) before it touches the database.</p>

        <h2>How we use it</h2>
        <ul>
          <li>To respond to your enquiry and plan your event.</li>
          <li>To let you log in and view the status of enquiries linked to your account.</li>
          <li>To display testimonials you choose to submit, once reviewed and approved.</li>
          <li>We do not sell, rent, or share your personal information with third parties for marketing.</li>
        </ul>

        <h2>How we protect it</h2>
        <ul>
          <li>Passwords are hashed (never stored or logged in plain text).</li>
          <li>All database queries use parameterised statements to prevent SQL injection.</li>
          <li>Access to enquiry and client data is restricted by role — only admins and assigned
          planners can view enquiry details; clients can only see their own.</li>
        </ul>

        <h2>Your choices</h2>
        <p>You can request that we delete your account and associated enquiries at any time by
        contacting <a href="mailto:hello@adeshandco.com.au">hello@adeshandco.com.au</a>.</p>

        <p style="color: var(--muted); font-size: 0.9rem; margin-top: 2em;">
          Note: this site was built as an educational exercise for ICT726 Assignment 4. No real
          client data is collected or processed — any information entered into these forms is
          for demonstration purposes within the local development database only.
        </p>
      </div>
    </section>
<?php require __DIR__ . '/includes/footer.php'; ?>
