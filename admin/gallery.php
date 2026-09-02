<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$errors = [];
$notice = '';
$UPLOAD_DIR = __DIR__ . '/../images/';
$ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp'];
$MAX_BYTES = 4 * 1024 * 1024; // 4MB

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $notice = 'Session expired — please try again.';
    } elseif (isset($_POST['delete_image'])) {
        $id = (int)$_POST['image_id'];
        $stmt = $pdo->prepare('SELECT image_url FROM gallery WHERE image_id = ?');
        $stmt->execute([$id]);
        if ($row = $stmt->fetch()) {
            $path = __DIR__ . '/../' . $row['image_url'];
            if (is_file($path)) @unlink($path);
        }
        $pdo->prepare('DELETE FROM gallery WHERE image_id = ?')->execute([$id]);
        $notice = 'Image deleted.';
    } elseif (isset($_POST['save_image'])) {
        $caption  = trim($_POST['caption'] ?? '');
        $category = $_POST['category'] ?? '';
        $altText  = trim($_POST['alt_text'] ?? '');

        if ($caption === '' || mb_strlen($caption) > 150) $errors['caption'] = 'Enter a caption (max 150 characters).';
        if (!in_array($category, ['wedding', 'corporate', 'celebration'], true)) $errors['category'] = 'Select a category.';
        if ($altText === '') $errors['alt_text'] = 'Enter descriptive alt text for accessibility.';

        $storedPath = null;
        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $errors['image'] = 'Upload failed — please try again.';
            } elseif ($file['size'] > $MAX_BYTES) {
                $errors['image'] = 'Image is too large (max 4MB).';
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $ALLOWED_EXT, true)) {
                    $errors['image'] = 'Only JPG, PNG or WEBP images are allowed.';
                } else {
                    // Confirm it's really an image, not just a renamed file.
                    if (@getimagesize($file['tmp_name']) === false) {
                        $errors['image'] = 'That file does not look like a valid image.';
                    } else {
                        $filename = 'gallery_' . bin2hex(random_bytes(8)) . '.' . $ext;
                        if (move_uploaded_file($file['tmp_name'], $UPLOAD_DIR . $filename)) {
                            $storedPath = 'images/' . $filename;
                        } else {
                            $errors['image'] = 'Could not save the uploaded file.';
                        }
                    }
                }
            }
        } else {
            $errors['image'] = 'Choose an image to upload.';
        }

        if (empty($errors) && $storedPath) {
            $stmt = $pdo->prepare(
                'INSERT INTO gallery (image_url, caption, category, alt_text, uploaded_by) VALUES (?,?,?,?,?)'
            );
            $stmt->execute([$storedPath, $caption, $category, $altText, $_SESSION['user_id']]);
            $notice = 'Image added to gallery.';
        }
    }
}

$images = $pdo->query('SELECT * FROM gallery ORDER BY created_at DESC')->fetchAll();

$pageTitle = 'Manage Gallery — Admin';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap"><span class="eyebrow">Admin</span><h1>Manage gallery</h1></div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <?php admin_nav('gallery'); ?>

        <div>
          <?php if ($notice): ?>
            <div class="form-status success is-visible" role="status" style="display:block;"><?= e($notice) ?></div>
          <?php endif; ?>

          <div class="form-card" style="margin-bottom:32px;">
            <h2 style="font-size:1.1rem; margin-bottom:16px;">Add a new photo</h2>
            <form method="post" enctype="multipart/form-data" novalidate>
              <?= csrf_field() ?>
              <div class="field">
                <label for="image">Image file (JPG, PNG or WEBP, max 4MB) <span class="required">*</span></label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                <span class="field-error"><?= e($errors['image'] ?? '') ?></span>
              </div>
              <div class="field-row">
                <div class="field">
                  <label for="caption">Caption <span class="required">*</span></label>
                  <input type="text" id="caption" name="caption" required value="<?= e($_POST['caption'] ?? '') ?>">
                  <span class="field-error"><?= e($errors['caption'] ?? '') ?></span>
                </div>
                <div class="field">
                  <label for="category">Category <span class="required">*</span></label>
                  <select id="category" name="category" required>
                    <option value="">Select one</option>
                    <option value="wedding" <?= ($_POST['category'] ?? '') === 'wedding' ? 'selected' : '' ?>>Wedding</option>
                    <option value="corporate" <?= ($_POST['category'] ?? '') === 'corporate' ? 'selected' : '' ?>>Corporate</option>
                    <option value="celebration" <?= ($_POST['category'] ?? '') === 'celebration' ? 'selected' : '' ?>>Celebration</option>
                  </select>
                  <span class="field-error"><?= e($errors['category'] ?? '') ?></span>
                </div>
              </div>
              <div class="field">
                <label for="alt_text">Alt text (for screen readers) <span class="required">*</span></label>
                <input type="text" id="alt_text" name="alt_text" required value="<?= e($_POST['alt_text'] ?? '') ?>" placeholder="Describe what's in the photo">
                <span class="field-error"><?= e($errors['alt_text'] ?? '') ?></span>
              </div>
              <button type="submit" name="save_image" value="1" class="btn btn-primary">Upload photo</button>
            </form>
          </div>

          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Preview</th><th>Caption</th><th>Category</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($images as $img): ?>
                <tr>
                  <td><img src="<?= BASE_URL ?>/<?= e($img['image_url']) ?>" alt="" style="width:64px; height:48px; object-fit:cover; border-radius:6px;"></td>
                  <td><?= e($img['caption']) ?></td>
                  <td><?= e(ucfirst($img['category'])) ?></td>
                  <td>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this image?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="image_id" value="<?= (int)$img['image_id'] ?>">
                      <button type="submit" name="delete_image" value="1" class="btn btn-danger btn-small">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
