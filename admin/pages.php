<?php
// admin/pages.php - Manage Pages
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Manage Pages';
$headerTitle = 'Website Pages & Content';

// Handle delete action
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    
    // Check if it's a system page
    $check = $db->prepare("SELECT is_system, title FROM pages WHERE id = ?");
    $check->execute([$delId]);
    $p = $check->fetch();

    if ($p) {
        if ($p['is_system'] == 1) {
            set_flash('danger', 'System pages cannot be deleted, but you can edit their contents.');
        } else {
            $delStmt = $db->prepare("DELETE FROM pages WHERE id = ?");
            $delStmt->execute([$delId]);
            set_flash('success', 'Page "' . htmlspecialchars($p['title']) . '" deleted successfully.');
        }
    }
    header("Location: pages.php");
    exit;
}

// Handle toggle publish
if (isset($_GET['toggle_publish'])) {
    $togId = (int)$_GET['toggle_publish'];
    $togStmt = $db->prepare("UPDATE pages SET is_published = 1 - is_published, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $togStmt->execute([$togId]);
    set_flash('success', 'Page publication status updated.');
    header("Location: pages.php");
    exit;
}

$pages = $db->query("SELECT * FROM pages ORDER BY sort_order ASC, id ASC")->fetchAll();

include __DIR__ . '/admin_header.php';
?>

<div class="adm-card">
    <div class="adm-card-header">
        <div>
            <h3>Website Pages</h3>
            <p style="font-size: 0.85rem; color: var(--adm-text-muted); margin-top: 4px;">Manage all static and dynamic pages. Core pages and newly created pages can be fully edited anytime.</p>
        </div>
        <a href="page_edit.php" class="adm-btn adm-btn-primary">+ Add New Page</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Page Title</th>
                    <th>URL Slug</th>
                    <th>Nav Bar</th>
                    <th>Footer</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $p): ?>
                    <tr>
                        <td style="color: var(--adm-text-muted); font-weight: 600;"><?= $p['sort_order'] ?></td>
                        <td>
                            <strong style="color: #fff; font-size: 0.95rem;"><?= clean($p['title']) ?></strong>
                            <?php if ($p['is_system'] == 1): ?>
                                <span class="adm-badge" style="background: rgba(6, 182, 212, 0.15); color: var(--adm-cyan); margin-left: 6px;">Core</span>
                            <?php endif; ?>
                            <?php if (!empty($p['subtitle'])): ?>
                                <div style="font-size: 0.78rem; color: var(--adm-text-muted); max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= clean($p['subtitle']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <code style="color: var(--adm-emerald-light); background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px;">page.php?slug=<?= clean($p['slug']) ?></code>
                        </td>
                        <td>
                            <?= ($p['show_in_nav'] == 1) ? '<span style="color: var(--adm-emerald-light);">✓ Active</span>' : '<span style="color: var(--adm-text-muted);">Hidden</span>' ?>
                        </td>
                        <td>
                            <?= ($p['show_in_footer'] == 1) ? '<span style="color: var(--adm-emerald-light);">✓ Active</span>' : '<span style="color: var(--adm-text-muted);">Hidden</span>' ?>
                        </td>
                        <td>
                            <a href="pages.php?toggle_publish=<?= $p['id'] ?>" title="Click to toggle status">
                                <?php if ($p['is_published'] == 1): ?>
                                    <span class="adm-badge published">Published</span>
                                <?php else: ?>
                                    <span class="adm-badge draft">Draft</span>
                                <?php endif; ?>
                            </a>
                        </td>
                        <td style="color: var(--adm-text-muted); font-size: 0.8rem;"><?= date('M j, Y', strtotime($p['updated_at'])) ?></td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="../page.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm" title="View live page">Preview</a>
                            <a href="page_edit.php?id=<?= $p['id'] ?>" class="adm-btn adm-btn-primary adm-btn-sm">Edit</a>
                            <?php if ($p['is_system'] == 0): ?>
                                <a href="pages.php?delete=<?= $p['id'] ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Are you sure you want to permanently delete this page?">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>
