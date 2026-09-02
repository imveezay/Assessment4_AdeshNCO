<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_role(['admin']);

$errors = [];
$notice = '';
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $notice = 'Session expired — please try again.';
    } elseif (isset($_POST['delete_package'])) {
        $stmt = $pdo->prepare('DELETE FROM packages WHERE package_id = ?');
        $stmt->execute([(int)$_POST['package_id']]);
        $notice = 'Package deleted.';
    } elseif (isset($_POST['save_package'])) {
        $id          = (int)($_POST['package_id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $tagline     = trim($_POST['tagline'] ?? '');
        $priceFrom   = $_POST['price_from'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $isFeatured  = isset($_POST['is_featured']) ? 1 : 0;
        $displayOrder = (int)($_POST['display_order'] ?? 0);

        if ($name === '' || mb_strlen($name) > 100) $errors['name'] = 'Enter a package name (max 100 characters).';
        if (!is_numeric($priceFrom) || (float)$priceFrom < 0) $errors['price_from'] = 'Enter a valid price.';
        if ($description === '') $errors['description'] = 'Enter a description (comma-separated feature bullets).';

        if (empty($errors)) {
            if ($id > 0) {
                $stmt = $pdo->prepare(
                    'UPDATE packages SET name=?, tagline=?, price_from=?, description=?, is_featured=?, display_order=? WHERE package_id=?'
                );
                $stmt->execute([$name, $tagline, $priceFrom, $description, $isFeatured, $displayOrder, $id]);
                $notice = 'Package updated.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO packages (name, tagline, price_from, description, is_featured, display_order, created_by) VALUES (?,?,?,?,?,?,?)'
                );
                $stmt->execute([$name, $tagline, $priceFrom, $description, $isFeatured, $displayOrder, $_SESSION['user_id']]);
                $notice = 'Package created.';
            }
        } else {
            // Keep the failed submission around so the form can be re-shown with errors.
            $editing = [
                'package_id' => $id, 'name' => $name, 'tagline' => $tagline, 'price_from' => $priceFrom,
                'description' => $description, 'is_featured' => $isFeatured, 'display_order' => $displayOrder,
            ];
        }
    }
}

// Load a package into the form for editing.
if ($editing === null && isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM packages WHERE package_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch() ?: null;
}

$packages = $pdo->query('SELECT * FROM packages ORDER BY display_order ASC')->fetchAll();

$pageTitle = 'Manage Packages — Admin';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/_nav.php';
?>
    <section class="page-header page-header--compact">
      <div class="wrap"><span class="eyebrow">Admin</span><h1>Manage packages</h1></div>
    </section>

    <section class="section section--tight">
      <div class="wrap dash-layout">
        <?php admin_nav('packages'); ?>

        <div>
          <?php if ($notice): ?>
            <div class="form-status success is-visible" role="status" style="display:block;"><?= e($notice) ?></div>
          <?php endif; ?>

          <div class="form-card" style="margin-bottom:32px;">
            <h2 style="font-size:1.1rem; margin-bottom:16px;"><?= $editing && !empty($editing['package_id']) ? 'Edit package' : 'Add a new package' ?></h2>
            <form method="post" novalidate>
              <?= csrf_field() ?>
              <input type="hidden" name="package_id" value="<?= (int)($editing['package_id'] ?? 0) ?>">
              <div class="field-row">
                <div class="field">
                  <label for="name">Name <span class="required">*</span></label>
                  <input type="text" id="name" name="name" required value="<?= e($editing['name'] ?? '') ?>">
                  <span class="field-error"><?= e($errors['name'] ?? '') ?></span>
                </div>
                <div class="field">
                  <label for="tagline">Tagline</label>
                  <input type="text" id="tagline" name="tagline" value="<?= e($editing['tagline'] ?? '') ?>" placeholder="e.g. Most comprehensive">
                </div>
              </div>
              <div class="field-row">
                <div class="field">
                  <label for="price_from">Price from ($) <span class="required">*</span></label>
                  <input type="number" step="0.01" min="0" id="price_from" name="price_from" required value="<?= e((string)($editing['price_from'] ?? '')) ?>">
                  <span class="field-error"><?= e($errors['price_from'] ?? '') ?></span>
                </div>
                <div class="field">
                  <label for="display_order">Display order</label>
                  <input type="number" id="display_order" name="display_order" value="<?= e((string)($editing['display_order'] ?? 0)) ?>">
                </div>
              </div>
              <div class="field">
                <label for="description">Description (comma-separated feature bullets) <span class="required">*</span></label>
                <textarea id="description" name="description" required><?= e($editing['description'] ?? '') ?></textarea>
                <span class="field-error"><?= e($errors['description'] ?? '') ?></span>
              </div>
              <div class="checkbox-row">
                <input type="checkbox" id="is_featured" name="is_featured" <?= !empty($editing['is_featured']) ? 'checked' : '' ?>>
                <label for="is_featured">Feature this package (highlighted card on the Services page)</label>
              </div>
              <div class="action-row">
                <button type="submit" name="save_package" value="1" class="btn btn-primary"><?= $editing && !empty($editing['package_id']) ? 'Save changes' : 'Add package' ?></button>
                <?php if ($editing && !empty($editing['package_id'])): ?>
                  <a class="btn btn-ghost" href="<?= BASE_URL ?>/admin/packages.php">Cancel edit</a>
                <?php endif; ?>
              </div>
            </form>
          </div>

          <div class="data-table-wrap">
            <table class="data-table">
              <thead><tr><th>Name</th><th>Price</th><th>Featured</th><th>Order</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($packages as $pkg): ?>
                <tr>
                  <td><?= e($pkg['name']) ?><br><small style="color:var(--muted);"><?= e($pkg['tagline']) ?></small></td>
                  <td><?= format_price($pkg['price_from']) ?></td>
                  <td><?= $pkg['is_featured'] ? 'Yes' : '—' ?></td>
                  <td><?= (int)$pkg['display_order'] ?></td>
                  <td class="action-row">
                    <a class="btn btn-ghost btn-small" href="<?= BASE_URL ?>/admin/packages.php?edit=<?= (int)$pkg['package_id'] ?>">Edit</a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this package?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="package_id" value="<?= (int)$pkg['package_id'] ?>">
                      <button type="submit" name="delete_package" value="1" class="btn btn-danger btn-small">Delete</button>
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
