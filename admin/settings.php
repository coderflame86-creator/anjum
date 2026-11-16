<?php
// admin/settings.php - Global Website Settings
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Site Settings';
$headerTitle = 'Global Website Configuration';

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $settingsToUpdate = [
            'site_title' => trim($_POST['site_title'] ?? 'Anjum Iqbal'),
            'site_tagline' => trim($_POST['site_tagline'] ?? 'Skill Development & Empowerment'),
            'hero_badge' => trim($_POST['hero_badge'] ?? "Pakistan's Free Digital Educator"),
            'hero_headline' => trim($_POST['hero_headline'] ?? 'Learn Today. <span class="gradient-text">Lead Tomorrow.</span>'),
            'hero_description' => trim($_POST['hero_description'] ?? ''),
            'primary_youtube' => trim($_POST['primary_youtube'] ?? ''),
            'primary_whatsapp' => trim($_POST['primary_whatsapp'] ?? ''),
            'contact_email' => trim($_POST['contact_email'] ?? ''),
            'announcement_enabled' => isset($_POST['announcement_enabled']) ? '1' : '0',
            'scam_alert_banner' => trim($_POST['scam_alert_banner'] ?? ''),
            'about_heading' => trim($_POST['about_heading'] ?? ''),
            'about_bio' => trim($_POST['about_bio'] ?? ''),
            'footer_copyright' => trim($_POST['footer_copyright'] ?? '© 2026 Anjum Iqbal. All rights reserved.')
        ];

        foreach ($settingsToUpdate as $key => $val) {
            update_setting($key, $val);
        }

        set_flash('success', 'Global site settings updated successfully!');
        header("Location: settings.php");
        exit;
    }
}

include __DIR__ . '/admin_header.php';
?>

<div class="adm-card">
    <div class="adm-card-header">
        <div>
            <h3>Website General Configuration</h3>
            <p style="font-size: 0.85rem; color: var(--adm-text-muted); margin-top: 4px;">Update branding text, hero section copy, announcement ticker, and contact information.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="adm-alert danger">
            <span><?= clean($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="settings.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <h4 style="color: #fff; margin-bottom: 16px; font-size: 1rem; border-bottom: 1px solid var(--adm-border); padding-bottom: 8px;">Branding & Identity</h4>
        
        <div class="form-row">
            <div class="adm-form-group">
                <label for="site_title">Website Title / Creator Name *</label>
                <input type="text" id="site_title" name="site_title" class="adm-input" value="<?= clean(get_setting('site_title', 'Anjum Iqbal')) ?>" required>
            </div>

            <div class="adm-form-group">
                <label for="site_tagline">Tagline / Mission Motto</label>
                <input type="text" id="site_tagline" name="site_tagline" class="adm-input" value="<?= clean(get_setting('site_tagline', 'Skill Development & Empowerment')) ?>">
            </div>
        </div>

        <h4 style="color: #fff; margin: 30px 0 16px; font-size: 1rem; border-bottom: 1px solid var(--adm-border); padding-bottom: 8px;">Hero Section (Homepage Top)</h4>

        <div class="adm-form-group">
            <label for="hero_badge">Hero Pill Badge Tag</label>
            <input type="text" id="hero_badge" name="hero_badge" class="adm-input" value="<?= clean(get_setting('hero_badge', "Pakistan's Free Digital Educator")) ?>">
        </div>

        <div class="adm-form-group">
            <label for="hero_headline">Hero Headline (HTML allowed, e.g. &lt;span class="gradient-text"&gt;...&lt;/span&gt;)</label>
            <input type="text" id="hero_headline" name="hero_headline" class="adm-input" value="<?= htmlspecialchars(get_setting('hero_headline', 'Learn Today. <span class="gradient-text">Lead Tomorrow.</span>')) ?>" required>
        </div>

        <div class="adm-form-group">
            <label for="hero_description">Hero Description Paragraph</label>
            <textarea id="hero_description" name="hero_description" class="adm-textarea" style="min-height: 90px;"><?= clean(get_setting('hero_description')) ?></textarea>
        </div>

        <h4 style="color: #fff; margin: 30px 0 16px; font-size: 1rem; border-bottom: 1px solid var(--adm-border); padding-bottom: 8px;">Security & Scam Alert Ticker (Top Bar)</h4>

        <div class="adm-form-group">
            <label class="checkbox-label" style="margin-bottom: 12px;">
                <input type="checkbox" name="announcement_enabled" value="1" <?= (get_setting('announcement_enabled', '1') === '1') ? 'checked' : '' ?>>
                <span><strong>Enable Top Security Warning Ticker</strong></span>
            </label>
            <input type="text" id="scam_alert_banner" name="scam_alert_banner" class="adm-input" value="<?= clean(get_setting('scam_alert_banner')) ?>" placeholder="Announcement notice message...">
        </div>

        <h4 style="color: #fff; margin: 30px 0 16px; font-size: 1rem; border-bottom: 1px solid var(--adm-border); padding-bottom: 8px;">Primary Contacts & Links</h4>

        <div class="form-row">
            <div class="adm-form-group">
                <label for="primary_youtube">Primary YouTube Channel URL</label>
                <input type="url" id="primary_youtube" name="primary_youtube" class="adm-input" value="<?= clean(get_setting('primary_youtube')) ?>" required>
            </div>

            <div class="adm-form-group">
                <label for="primary_whatsapp">Primary WhatsApp Community URL</label>
                <input type="url" id="primary_whatsapp" name="primary_whatsapp" class="adm-input" value="<?= clean(get_setting('primary_whatsapp')) ?>" required>
            </div>
        </div>

        <div class="adm-form-group">
            <label for="contact_email">Public Contact & Support Email</label>
            <input type="email" id="contact_email" name="contact_email" class="adm-input" value="<?= clean(get_setting('contact_email')) ?>" required>
        </div>

        <h4 style="color: #fff; margin: 30px 0 16px; font-size: 1rem; border-bottom: 1px solid var(--adm-border); padding-bottom: 8px;">About Section Content</h4>

        <div class="adm-form-group">
            <label for="about_heading">About Heading</label>
            <input type="text" id="about_heading" name="about_heading" class="adm-input" value="<?= clean(get_setting('about_heading')) ?>">
        </div>

        <div class="adm-form-group">
            <label for="about_bio">About Mission Biography</label>
            <textarea id="about_bio" name="about_bio" class="adm-textarea" style="min-height: 120px;"><?= clean(get_setting('about_bio')) ?></textarea>
        </div>

        <h4 style="color: #fff; margin: 30px 0 16px; font-size: 1rem; border-bottom: 1px solid var(--adm-border); padding-bottom: 8px;">Footer</h4>

        <div class="adm-form-group">
            <label for="footer_copyright">Footer Copyright Text</label>
            <input type="text" id="footer_copyright" name="footer_copyright" class="adm-input" value="<?= clean(get_setting('footer_copyright')) ?>">
        </div>

        <button type="submit" class="adm-btn adm-btn-primary" style="padding: 12px 30px; font-size: 0.95rem; margin-top: 15px;">
            <span>Save All Settings</span>
        </button>
    </form>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>
