  </main>

  <footer class="site-footer">
    <div class="wrap footer-inner">
      <div class="footer-brand">
        <span class="brand">Adesh <span>&amp; Co.</span></span>
        <p>Hurstville, NSW 2220<br>Servicing Sydney-wide</p>
      </div>
      <div class="footer-links">
        <h2>Explore</h2>
        <ul>
          <li><a href="<?= BASE_URL ?>/about.php">About</a></li>
          <li><a href="<?= BASE_URL ?>/services.php">Services</a></li>
          <li><a href="<?= BASE_URL ?>/gallery.php">Gallery</a></li>
          <li><a href="<?= BASE_URL ?>/testimonials.php">Testimonials</a></li>
          <li><a href="<?= BASE_URL ?>/contact.php">Contact</a></li>
          <li><a href="<?= BASE_URL ?>/privacy.php">Privacy Notice</a></li>
        </ul>
      </div>
      <div class="footer-hours">
        <h2>Enquiries</h2>
        <p>Mon&ndash;Fri: 9:00 &ndash; 18:00<br>Sat: 10:00 &ndash; 14:00</p>
      </div>
      <div class="footer-social">
        <h2>Follow</h2>
        <ul>
          <li><a href="https://instagram.com" target="_blank" rel="noopener">Instagram</a></li>
          <li><a href="https://facebook.com" target="_blank" rel="noopener">Facebook</a></li>
          <li><a href="mailto:hello@adeshandco.com.au">hello@adeshandco.com.au</a></li>
        </ul>
      </div>
    </div>
    <p class="footer-bottom">&copy; <span id="year"></span> Adesh &amp; Co. Events. All rights reserved.</p>
  </footer>

  <script src="<?= BASE_URL ?>/js/main.js?v=<?= @filemtime(__DIR__ . '/../js/main.js') ?: '1' ?>"></script>
  <?php foreach ($pageScripts ?? [] as $src): ?>
  <script src="<?= BASE_URL ?><?= e($src) ?>?v=<?= @filemtime(__DIR__ . '/..' . $src) ?: '1' ?>"></script>
  <?php endforeach; ?>
</body>
</html>
