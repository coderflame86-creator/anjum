<?php
// admin/socials.php - Manage Social Media Links
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Manage Social Links';
$headerTitle = 'Social Media Channels';

$error = '';
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editingSocial = null;

// Handle delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $delStmt = $db->prepare("DELETE FROM social_links WHERE id = ?");
    $delStmt->execute([$delId]);
    set_flash('success', 'Social media link removed.');
    header("Location: socials.php");
    exit;
}

// Handle toggle active
if (isset($_GET['toggle'])) {
    $togId = (int)$_GET['toggle'];
    $togStmt = $db->prepare("UPDATE social_links SET is_active = 1 - is_active WHERE id = ?");
    $togStmt->execute([$togId]);
    set_flash('success', 'Social link status updated.');
    header("Location: socials.php");
    exit;
}

// If editing, load the record
if ($editId > 0) {
    $stmt = $db->prepare("SELECT * FROM social_links WHERE id = ?");
    $stmt->execute([$editId]);
    $editingSocial = $stmt->fetch();
}

// Handle Form Submission (Add or Update)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $platform = trim($_POST['platform'] ?? '');
        $url = trim($_POST['url'] ?? '');
        $icon = trim($_POST['icon'] ?? 'globe');
        $sortOrder = (int)($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $idToUpdate = (int)($_POST['social_id'] ?? 0);

        if (empty($platform) || empty($url)) {
            $error = 'Please provide both platform name and target URL.';
        } else {
            if ($idToUpdate > 0) {
                // Update
                $upd = $db->prepare("UPDATE social_links SET platform = ?, url = ?, icon = ?, sort_order = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $upd->execute([$platform, $url, $icon, $sortOrder, $isActive, $idToUpdate]);
                set_flash('success', 'Social channel "' . htmlspecialchars($platform) . '" updated successfully.');
            } else {
                // Insert
                $ins = $db->prepare("INSERT INTO social_links (platform, url, icon, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $ins->execute([$platform, $url, $icon, $sortOrder, $isActive]);
                set_flash('success', 'Social channel "' . htmlspecialchars($platform) . '" added successfully.');
            }
            header("Location: socials.php");
            exit;
        }
    }
}

$socials = $db->query("SELECT * FROM social_links ORDER BY sort_order ASC, id ASC")->fetchAll();

include __DIR__ . '/admin_header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 30px;">
    <!-- Form to Add or Edit Social Link -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3><?= $editingSocial ? 'Edit Social Channel' : 'Add New Social Channel' ?></h3>
            <?php if ($editingSocial): ?>
                <a href="socials.php" class="adm-btn adm-btn-secondary adm-btn-sm">&times; Cancel Edit</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="adm-alert danger">
                <span><?= clean($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="socials.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <?php if ($editingSocial): ?>
                <input type="hidden" name="social_id" value="<?= $editingSocial['id'] ?>">
            <?php endif; ?>

            <div class="adm-form-group">
                <label for="platform">Platform Label *</label>
                <input type="text" id="platform" name="platform" class="adm-input" placeholder="e.g. YouTube Channel, TikTok" value="<?= clean($editingSocial['platform'] ?? '') ?>" required>
            </div>

            <div class="adm-form-group">
                <label for="icon">Platform Icon</label>
                <select id="icon" name="icon" class="adm-select">
                    <?php
                    $iconOptions = [
                        'youtube' => 'YouTube',
                        'whatsapp' => 'WhatsApp',
                        'tiktok' => 'TikTok',
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'x' => 'X / Twitter',
                        'linkedin' => 'LinkedIn',
                        'telegram' => 'Telegram',
                        'globe' => 'Website / Globe'
                    ];
                    $currentIcon = $editingSocial['icon'] ?? 'youtube';
                    foreach ($iconOptions as $val => $label):
                    ?>
                        <option value="<?= $val ?>" <?= ($currentIcon === $val) ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="adm-form-group">
                <label for="url">Target URL (Link) *</label>
                <input type="url" id="url" name="url" class="adm-input" placeholder="https://..." value="<?= clean($editingSocial['url'] ?? '') ?>" required>
            </div>

            <div class="adm-form-group">
                <label for="sort_order">Display Order (Lower numbers appear first)</label>
                <input type="number" id="sort_order" name="sort_order" class="adm-input" value="<?= (int)($editingSocial['sort_order'] ?? 1) ?>">
            </div>

            <div class="adm-form-group" style="margin-top: 15px;">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" <?= (!isset($editingSocial) || $editingSocial['is_active'] == 1) ? 'checked' : '' ?>>
                    <span><strong>Active</strong> (Display on website header, strip, and footer)</span>
                </label>
            </div>

            <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
                <span><?= $editingSocial ? 'Save Changes' : '+ Add Channel Link' ?></span>
            </button>
        </form>
    </div>

    <!-- Existing Social Links List -->
    <div class="adm-card">
        <div class="adm-card-header">
            <h3>Active Social Channels</h3>
            <span style="font-size: 0.85rem; color: var(--adm-text-muted);"><?= count($socials) ?> Total Channels</span>
        </div>

        <?php if (empty($socials)): ?>
            <p style="color: var(--adm-text-muted); text-align: center; padding: 40px 0;">No social links configured yet.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Icon</th>
                            <th>Platform</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($socials as $s): ?>
                            <tr>
                                <td style="color: var(--adm-text-muted); font-weight: 600;"><?= $s['sort_order'] ?></td>
                                <td>
                                    <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--adm-bg); display: grid; place-items: center; color: var(--adm-emerald-light);">
                                        <?= get_social_icon_svg($s['icon']) ?>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color: #fff; font-size: 0.95rem;"><?= clean($s['platform']) ?></strong>
                                    <div style="font-size: 0.76rem; color: var(--adm-text-muted); max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <a href="<?= clean($s['url']) ?>" target="_blank" rel="noopener" style="color: var(--adm-cyan);"><?= clean($s['url']) ?></a>
                                    </div>
                                </td>
                                <td>
                                    <a href="socials.php?toggle=<?= $s['id'] ?>" title="Click to toggle active status">
                                        <?php if ($s['is_active'] == 1): ?>
                                            <span class="adm-badge published">Active</span>
                                        <?php else: ?>
                                            <span class="adm-badge draft">Hidden</span>
                                        <?php endif; ?>
                                    </a>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="socials.php?edit=<?= $s['id'] ?>" class="adm-btn adm-btn-primary adm-btn-sm">Edit</a>
                                    <a href="socials.php?delete=<?= $s['id'] ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Remove this social link?">&times;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>
