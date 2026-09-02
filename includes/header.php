<?php
/**
 * Shared page head + header/nav.
 * Expects (set by the including page before requiring this file):
 *   $pageTitle        string  e.g. "Services — Adesh & Co."
 *   $pageDescription  string  meta description for SEO
 *   $activeNav        string  one of: home, about, services, gallery, testimonials, contact
 *   $canonicalPath    string  e.g. "/services.php" (optional, defaults to current script)
 */
$pageTitle       = $pageTitle       ?? 'Adesh & Co. — Event Management, Hurstville & Sydney';
$pageDescription = $pageDescription ?? 'Adesh & Co. is a Hurstville-based event management studio planning weddings, corporate functions and private celebrations across Sydney.';
$activeNav       = $activeNav       ?? '';
$canonicalPath   = $canonicalPath   ?? (BASE_URL . '/' . basename($_SERVER['SCRIPT_NAME']));
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="<?= e($pageDescription) ?>">
  <link rel="canonical" href="<?= e($canonicalPath) ?>">
  <meta name="robots" content="index, follow">
  <!-- Open Graph tags for SEO / link previews -->
  <meta property="og:title" content="<?= e($pageTitle) ?>">
  <meta property="og:description" content="<?= e($pageDescription) ?>">
  <meta property="og:type" content="website">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,600&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/styles.css?v=<?= @filemtime(__DIR__ . '/../css/styles.css') ?: '1' ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/animations.css?v=<?= @filemtime(__DIR__ . '/../css/animations.css') ?: '1' ?>">
</head>
<body>
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="site-header">
    <div class="header-inner">
      <a href="<?= BASE_URL ?>/index.php" class="brand">Adesh <span>&amp; Co.</span></a>
      <button class="nav-toggle" aria-expanded="false" aria-controls="primary-nav" aria-label="Open menu">
        <span></span><span></span><span></span>
      </button>
      <nav id="primary-nav" class="primary-nav" aria-label="Primary">
        <ul>
          <li><a href="<?= BASE_URL ?>/index.php" <?= $activeNav === 'home' ? 'aria-current="page"' : '' ?>>Home</a></li>
          <li><a href="<?= BASE_URL ?>/about.php" <?= $activeNav === 'about' ? 'aria-current="page"' : '' ?>>About</a></li>
          <li><a href="<?= BASE_URL ?>/services.php" <?= $activeNav === 'services' ? 'aria-current="page"' : '' ?>>Services</a></li>
          <li><a href="<?= BASE_URL ?>/gallery.php" <?= $activeNav === 'gallery' ? 'aria-current="page"' : '' ?>>Gallery</a></li>
          <li><a href="<?= BASE_URL ?>/testimonials.php" <?= $activeNav === 'testimonials' ? 'aria-current="page"' : '' ?>>Testimonials</a></li>
          <li><a href="<?= BASE_URL ?>/contact.php" class="nav-cta" <?= $activeNav === 'contact' ? 'aria-current="page"' : '' ?>>Enquire</a></li>
          <?php if (is_logged_in()): ?>
            <?php
              $dashUrl = match (current_role()) {
                  'admin'   => BASE_URL . '/admin/index.php',
                  'planner' => BASE_URL . '/planner/index.php',
                  default   => BASE_URL . '/client/index.php',
              };
            ?>
            <li><a href="<?= e($dashUrl) ?>">My Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>/logout.php">Log out (<?= e(current_name()) ?>)</a></li>
          <?php else: ?>
            <li><a href="<?= BASE_URL ?>/login.php">Log in</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <main id="main">
