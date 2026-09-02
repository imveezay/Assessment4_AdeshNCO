<?php
/**
 * Shared admin sidebar. Call admin_nav('key') from within a dash-layout
 * wrap, where key is one of: overview, enquiries, packages, gallery,
 * testimonials, users.
 */
function admin_nav(string $active): void
{
    $items = [
        'overview'     => [BASE_URL . '/admin/index.php', 'Overview'],
        'enquiries'    => [BASE_URL . '/admin/enquiries.php', 'Enquiries'],
        'packages'     => [BASE_URL . '/admin/packages.php', 'Packages'],
        'gallery'      => [BASE_URL . '/admin/gallery.php', 'Gallery'],
        'testimonials' => [BASE_URL . '/admin/testimonials.php', 'Testimonials'],
        'users'        => [BASE_URL . '/admin/users.php', 'Users'],
    ];
    echo '<nav class="dash-nav" aria-label="Admin dashboard"><h2>Admin</h2><ul>';
    foreach ($items as $key => [$url, $label]) {
        $current = $key === $active ? ' aria-current="page"' : '';
        echo '<li><a href="' . htmlspecialchars($url) . '"' . $current . '>' . htmlspecialchars($label) . '</a></li>';
    }
    echo '<li><a href="' . BASE_URL . '/logout.php">Log out</a></li>';
    echo '</ul></nav>';
}
