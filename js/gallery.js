// ============================================================
// Adesh & CO. — Gallery page: filtering + lightbox
// ============================================================

document.addEventListener('DOMContentLoaded', function () {
  var items = Array.prototype.slice.call(document.querySelectorAll('.gallery-item'));
  var filterButtons = Array.prototype.slice.call(document.querySelectorAll('.filter-btn'));
  var lightbox = document.getElementById('lightbox');
  if (!lightbox || !items.length) return;

  var lightboxImg = document.getElementById('lightbox-img');
  var lightboxCaption = document.getElementById('lightbox-caption');
  var closeBtn = lightbox.querySelector('.lightbox-close');
  var prevBtn = lightbox.querySelector('.lightbox-prev');
  var nextBtn = lightbox.querySelector('.lightbox-next');

  var visibleItems = items.slice();
  var currentIndex = 0;
  var lastFocused = null;

  function getVisible() {
    return items.filter(function (item) { return item.style.display !== 'none'; });
  }

  function openLightbox(item) {
    visibleItems = getVisible();
    currentIndex = visibleItems.indexOf(item);
    updateLightbox();
    lastFocused = document.activeElement;
    lightbox.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    closeBtn.focus();
    document.addEventListener('keydown', onKeydown);
  }

  function updateLightbox() {
    var item = visibleItems[currentIndex];
    var img = item.querySelector('img');
    lightboxImg.src = img.getAttribute('data-full') || img.src;
    lightboxImg.alt = img.alt;
    lightboxCaption.textContent = item.querySelector('.cap') ? item.querySelector('.cap').textContent : img.alt;
  }

  function closeLightbox() {
    lightbox.classList.remove('is-open');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeydown);
    if (lastFocused) lastFocused.focus();
  }

  function showPrev() {
    currentIndex = (currentIndex - 1 + visibleItems.length) % visibleItems.length;
    updateLightbox();
  }

  function showNext() {
    currentIndex = (currentIndex + 1) % visibleItems.length;
    updateLightbox();
  }

  function onKeydown(e) {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') showPrev();
    if (e.key === 'ArrowRight') showNext();
  }

  items.forEach(function (item) {
    item.addEventListener('click', function () { openLightbox(item); });
    item.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openLightbox(item);
      }
    });
  });

  closeBtn.addEventListener('click', closeLightbox);
  prevBtn.addEventListener('click', showPrev);
  nextBtn.addEventListener('click', showNext);
  lightbox.addEventListener('click', function (e) {
    if (e.target === lightbox) closeLightbox();
  });

  // ---- Category filtering ----
  filterButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      filterButtons.forEach(function (b) { b.classList.remove('is-active'); b.setAttribute('aria-pressed', 'false'); });
      btn.classList.add('is-active');
      btn.setAttribute('aria-pressed', 'true');

      var category = btn.getAttribute('data-filter');
      items.forEach(function (item) {
        var matches = category === 'all' || item.getAttribute('data-category') === category;
        item.style.display = matches ? '' : 'none';
      });
    });
  });
});
