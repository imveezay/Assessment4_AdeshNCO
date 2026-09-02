<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$images = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();

$pageTitle       = 'Gallery — Adesh & Co.';
$pageDescription = 'Photos from weddings, corporate functions and celebrations planned by Adesh & Co.';
$activeNav       = 'gallery';
require __DIR__ . '/includes/header.php';
?>
    <section class="page-header">
      <div class="wrap">
        <span class="eyebrow">Gallery</span>
        <h1>Past events, in photos</h1>
        <p>A selection of weddings, corporate functions and celebrations we've planned. Click any photo to see it full size — use the filters to browse by event type.</p>
      </div>
    </section>

    <section class="section">
      <div class="wrap">
        <div class="gallery-filter" role="group" aria-label="Filter gallery by event type">
          <button class="filter-btn is-active" data-filter="all" aria-pressed="true">All</button>
          <button class="filter-btn" data-filter="wedding" aria-pressed="false">Weddings</button>
          <button class="filter-btn" data-filter="corporate" aria-pressed="false">Corporate</button>
          <button class="filter-btn" data-filter="celebration" aria-pressed="false">Celebrations</button>
        </div>

        <div class="gallery-grid">
          <?php if (empty($images)): ?>
            <p>No gallery images yet — check back soon.</p>
          <?php endif; ?>
          <?php foreach ($images as $img): ?>
          <button class="gallery-item" data-category="<?= e($img['category']) ?>" type="button" aria-label="View larger image: <?= e($img['caption']) ?>">
            <img src="<?= BASE_URL ?>/<?= e($img['image_url']) ?>" data-full="<?= BASE_URL ?>/<?= e($img['image_url']) ?>" alt="<?= e($img['alt_text']) ?>" loading="lazy">
            <span class="cap"><?= e($img['caption']) ?></span>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
      <button class="lightbox-close" aria-label="Close image viewer">&#10005;</button>
      <button class="lightbox-prev" aria-label="Previous image">&#10094;</button>
      <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="">
        <p class="lightbox-caption" id="lightbox-caption"></p>
      </div>
      <button class="lightbox-next" aria-label="Next image">&#10095;</button>
    </div>
<?php
$pageScripts = ['/js/gallery.js'];
require __DIR__ . '/includes/footer.php';
?>
