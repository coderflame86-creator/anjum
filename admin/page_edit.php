<?php
// admin/page_edit.php - Add or Edit a Page
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEditing = ($id > 0);

$page = [
    'title' => '',
    'slug' => '',
    'subtitle' => '',
    'content' => '',
    'meta_description' => '',
    'is_published' => 1,
    'show_in_nav' => 0,
    'show_in_footer' => 1,
    'sort_order' => 10,
    'is_system' => 0
];

if ($isEditing) {
    $stmt = $db->prepare("SELECT * FROM pages WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if ($existing) {
        $page = $existing;
    } else {
        set_flash('danger', 'Page not found.');
        header("Location: pages.php");
        exit;
    }
}

$pageTitle = $isEditing ? 'Edit Page: ' . $page['title'] : 'Create New Page';
$headerTitle = $isEditing ? 'Edit Page' : 'Create New Page';
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $metaDesc = trim($_POST['meta_description'] ?? '');
        $isPublished = isset($_POST['is_published']) ? 1 : 0;
        $showInNav = isset($_POST['show_in_nav']) ? 1 : 0;
        $showInFooter = isset($_POST['show_in_footer']) ? 1 : 0;
        $sortOrder = (int)($_POST['sort_order'] ?? 0);

        // Sanitize slug
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9\-_]/', '-', $slug));
        $slug = trim(preg_replace('/-+/', '-', $slug), '-');

        if (empty($title)) {
            $error = 'Page title cannot be empty.';
        } elseif (empty($slug)) {
            $error = 'Page URL slug cannot be empty.';
        } else {
            // Check slug uniqueness (excluding current page)
            $slugCheck = $db->prepare("SELECT id FROM pages WHERE slug = ? AND id != ?");
            $slugCheck->execute([$slug, $id]);
            if ($slugCheck->fetch()) {
                $error = 'The URL slug "' . htmlspecialchars($slug) . '" is already in use by another page. Please choose a unique slug.';
            } else {
                if ($isEditing) {
                    $updateStmt = $db->prepare("
                        UPDATE pages 
                        SET title = ?, slug = ?, subtitle = ?, content = ?, meta_description = ?, 
                            is_published = ?, show_in_nav = ?, show_in_footer = ?, sort_order = ?, updated_at = CURRENT_TIMESTAMP 
                        WHERE id = ?
                    ");
                    $updateStmt->execute([
                        $title, $slug, $subtitle, $content, $metaDesc,
                        $isPublished, $showInNav, $showInFooter, $sortOrder, $id
                    ]);
                    set_flash('success', 'Page "' . htmlspecialchars($title) . '" updated successfully!');
                } else {
                    $insertStmt = $db->prepare("
                        INSERT INTO pages (title, slug, subtitle, content, meta_description, is_published, show_in_nav, show_in_footer, sort_order)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $insertStmt->execute([
                        $title, $slug, $subtitle, $content, $metaDesc,
                        $isPublished, $showInNav, $showInFooter, $sortOrder
                    ]);
                    $id = $db->lastInsertId();
                    set_flash('success', 'Page "' . htmlspecialchars($title) . '" created successfully!');
                }
                header("Location: pages.php");
                exit;
            }
        }
    }
}

include __DIR__ . '/admin_header.php';
?>

<div class="adm-card">
    <div class="adm-card-header">
        <div>
            <h3><?= $isEditing ? 'Editing Page: ' . clean($page['title']) : 'Add New Web Page' ?></h3>
            <p style="font-size: 0.85rem; color: var(--adm-text-muted); margin-top: 4px;">Fill in page details and publish status.</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="pages.php" class="adm-btn adm-btn-secondary">&larr; Back to Pages</a>
            <?php if ($isEditing): ?>
                <a href="../page.php?slug=<?= urlencode($page['slug']) ?>" target="_blank" class="adm-btn adm-btn-secondary" title="View live page">Preview</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="adm-alert danger">
            <span><?= clean($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="page_edit.php<?= $isEditing ? '?id=' . $id : '' ?>">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-row">
            <div class="adm-form-group">
                <label for="title">Page Title *</label>
                <input type="text" id="title" name="title" class="adm-input" value="<?= clean($page['title']) ?>" placeholder="e.g. Free Learning Resources" required onkeyup="generateSlug(this.value)">
            </div>

            <div class="adm-form-group">
                <label for="slug">URL Slug * (Unique URL identifier)</label>
                <input type="text" id="slug" name="slug" class="adm-input" value="<?= clean($page['slug']) ?>" placeholder="e.g. free-resources" required>
                <small style="color: var(--adm-text-muted); font-size: 0.78rem;">Public URL: <code>page.php?slug=<span id="slugPreview"><?= clean($page['slug']) ?: 'your-slug' ?></span></code></small>
            </div>
        </div>

        <div class="adm-form-group">
            <label for="subtitle">Subtitle / Short Header Description</label>
            <input type="text" id="subtitle" name="subtitle" class="adm-input" value="<?= clean($page['subtitle']) ?>" placeholder="e.g. A comprehensive collection of verified digital tools and guides">
        </div>

        <div class="adm-form-group">
            <label for="meta_description">Meta Description (SEO & Social Sharing)</label>
            <input type="text" id="meta_description" name="meta_description" class="adm-input" value="<?= clean($page['meta_description']) ?>" placeholder="Brief 1-2 sentence description for search engines">
        </div>

        <!-- Quick HTML Toolbar Helper -->
        <div class="adm-form-group">
            <label for="content">Page Content (HTML & Text) *</label>
            
            <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px; background: rgba(255,255,255,0.03); padding: 8px; border-radius: var(--radius-sm); border: 1px solid var(--adm-border);">
                <button type="button" class="adm-btn adm-btn-secondary adm-btn-sm" onclick="insertTag('<h3>', '</h3>')">Heading 3</button>
                <button type="button" class="adm-btn adm-btn-secondary adm-btn-sm" onclick="insertTag('<p>', '</p>')">Paragraph</button>
                <button type="button" class="adm-btn adm-btn-secondary adm-btn-sm" onclick="insertTag('<strong>', '</strong>')">Bold</button>
                <button type="button" class="adm-btn adm-btn-secondary adm-btn-sm" onclick="insertTag('<ul>\n  <li>', '</li>\n</ul>')">Bullet List</button>
                <button type="button" class="adm-btn adm-btn-secondary adm-btn-sm" onclick="insertTag('<div class=\'alert-box danger\'>\n  <h4>Alert Title</h4>\n  <p>', '</p>\n</div>')">Security Alert Box</button>
                <button type="button" class="adm-btn adm-btn-secondary adm-btn-sm" onclick="insertTag('<div class=\'card-grid-2\'>\n  <div class=\'content-card\'>\n    <h4>Card 1</h4>\n    <p>Content</p>\n  </div>\n  <div class=\'content-card\'>\n    <h4>Card 2</h4>\n    <p>Content</p>\n  </div>\n</div>', '')">2-Column Card Grid</button>
            </div>

            <textarea id="content" name="content" class="adm-textarea" style="min-height: 380px; font-family: monospace; font-size: 0.88rem; line-height: 1.5;" required><?= htmlspecialchars($page['content']) ?></textarea>
        </div>

        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-sm); padding: 20px; margin-bottom: 25px;">
            <h4 style="font-size: 0.95rem; color: #fff; margin-bottom: 15px;">Visibility & Positioning</h4>
            
            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_published" value="1" <?= ($page['is_published'] == 1) ? 'checked' : '' ?>>
                    <span><strong>Published</strong> (Visible to visitors)</span>
                </label>

                <label class="checkbox-label">
                    <input type="checkbox" name="show_in_nav" value="1" <?= ($page['show_in_nav'] == 1) ? 'checked' : '' ?>>
                    <span>Show in Top Navigation Menu</span>
                </label>

                <label class="checkbox-label">
                    <input type="checkbox" name="show_in_footer" value="1" <?= ($page['show_in_footer'] == 1) ? 'checked' : '' ?>>
                    <span>Show in Footer Links</span>
                </label>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="sort_order" style="margin-bottom: 0; font-size: 0.88rem;">Sort Order:</label>
                    <input type="number" id="sort_order" name="sort_order" class="adm-input" value="<?= (int)$page['sort_order'] ?>" style="width: 80px; padding: 6px 10px;">
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 14px;">
            <button type="submit" class="adm-btn adm-btn-primary" style="padding: 12px 28px;">
                <span><?= $isEditing ? 'Save Changes' : 'Create Page' ?></span>
            </button>
            <a href="pages.php" class="adm-btn adm-btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function generateSlug(text) {
    <?php if (!$isEditing): ?>
    const slugInput = document.getElementById('slug');
    const preview = document.getElementById('slugPreview');
    const slug = text.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    slugInput.value = slug;
    preview.innerText = slug || 'your-slug';
    <?php endif; ?>
}

document.getElementById('slug').addEventListener('input', function() {
    document.getElementById('slugPreview').innerText = this.value || 'your-slug';
});

function insertTag(startTag, endTag) {
    const textarea = document.getElementById('content');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    const replacement = startTag + selectedText + endTag;
    
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
    textarea.focus();
    textarea.setSelectionRange(start + startTag.length, start + startTag.length + selectedText.length);
}
</script>

<?php include __DIR__ . '/admin_footer.php'; ?>
