<?php
// admin/index.php - Admin Dashboard
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Dashboard';
$headerTitle = 'Dashboard Overview';

// Collect Statistics
$totalPages = (int)$db->query("SELECT COUNT(*) FROM pages")->fetchColumn();
$publishedPages = (int)$db->query("SELECT COUNT(*) FROM pages WHERE is_published = 1")->fetchColumn();

$totalMessages = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$unreadMessages = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();

$totalSocials = (int)$db->query("SELECT COUNT(*) FROM social_links WHERE is_active = 1")->fetchColumn();

// Fetch recent 5 messages
$recentMessages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Fetch all pages
$allPages = $db->query("SELECT * FROM pages ORDER BY sort_order ASC, id ASC")->fetchAll();

include __DIR__ . '/admin_header.php';
?>

<!-- KPI Statistics Cards -->
<div class="stats-grid">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Total Pages</h4>
            <div class="kpi-number"><?= $totalPages ?></div>
            <small style="color: var(--adm-emerald-light); font-size: 0.78rem;"><?= $publishedPages ?> Published</small>
        </div>
        <div class="kpi-icon emerald">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Contact Inquiries</h4>
            <div class="kpi-number"><?= $totalMessages ?></div>
            <small style="color: <?= ($unreadMessages > 0) ? '#fb7185' : 'var(--adm-text-muted)' ?>; font-size: 0.78rem;">
                <?= $unreadMessages ?> Unread
            </small>
        </div>
        <div class="kpi-icon <?= ($unreadMessages > 0) ? 'crimson' : 'cyan' ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Active Social Links</h4>
            <div class="kpi-number"><?= $totalSocials ?></div>
            <small style="color: var(--adm-cyan); font-size: 0.78rem;">Public Channel Badges</small>
        </div>
        <div class="kpi-icon amber">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
        </div>
    </div>
</div>

<!-- Quick Actions Strip -->
<div class="adm-card" style="padding: 20px 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="font-size: 1.1rem; color: #fff;">Quick Actions</h3>
            <p style="font-size: 0.85rem; color: var(--adm-text-muted);">Common administrative tasks for updating the website</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="page_edit.php" class="adm-btn adm-btn-primary adm-btn-sm">+ Create New Page</a>
            <a href="messages.php" class="adm-btn adm-btn-secondary adm-btn-sm">View Inbox (<?= $unreadMessages ?>)</a>
            <a href="socials.php" class="adm-btn adm-btn-secondary adm-btn-sm">Edit Socials</a>
            <a href="settings.php" class="adm-btn adm-btn-secondary adm-btn-sm">Update Site Settings</a>
        </div>
    </div>
</div>

<!-- Recent Contact Messages -->
<div class="adm-card">
    <div class="adm-card-header">
        <h3>Recent Contact Inquiries</h3>
        <a href="messages.php" class="adm-btn adm-btn-secondary adm-btn-sm">View All Inquiries &rarr;</a>
    </div>

    <?php if (empty($recentMessages)): ?>
        <p style="color: var(--adm-text-muted); font-size: 0.9rem; text-align: center; padding: 30px 0;">
            No contact submissions received yet. Submissions from the website's contact form will appear here.
        </p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMessages as $msg): ?>
                        <tr>
                            <td>
                                <?php if ($msg['is_read'] == 0): ?>
                                    <span class="adm-badge unread">Unread</span>
                                <?php else: ?>
                                    <span class="adm-badge read">Read</span>
                                <?php endif; ?>
                            </td>
                            <td><strong style="color: #fff;"><?= clean($msg['name']) ?></strong></td>
                            <td><a href="mailto:<?= clean($msg['email']) ?>" style="color: var(--adm-cyan);"><?= clean($msg['email']) ?></a></td>
                            <td><?= clean($msg['subject'] ?: 'General Inquiry') ?></td>
                            <td style="color: var(--adm-text-muted);"><?= date('M j, Y - H:i', strtotime($msg['created_at'])) ?></td>
                            <td>
                                <a href="messages.php?view=<?= $msg['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm">Read Message</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Website Pages Overview -->
<div class="adm-card">
    <div class="adm-card-header">
        <h3>Website Pages & Content</h3>
        <a href="page_edit.php" class="adm-btn adm-btn-primary adm-btn-sm">+ Add New Page</a>
    </div>

    <div style="overflow-x: auto;">
        <table class="adm-table">
            <thead>
                <tr>
                    <th>Page Title</th>
                    <th>URL Slug</th>
                    <th>Navigation</th>
                    <th>Footer</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allPages as $pg): ?>
                    <tr>
                        <td>
                            <strong style="color: #fff;"><?= clean($pg['title']) ?></strong>
                            <?php if ($pg['is_system'] == 1): ?>
                                <span style="font-size: 0.7rem; color: var(--adm-cyan); margin-left: 6px;">[Core Page]</span>
                            <?php endif; ?>
                        </td>
                        <td><code style="background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px; color: var(--adm-emerald-light);">/page.php?slug=<?= clean($pg['slug']) ?></code></td>
                        <td><?= ($pg['show_in_nav'] == 1) ? '✓ Shown' : '—' ?></td>
                        <td><?= ($pg['show_in_footer'] == 1) ? '✓ Shown' : '—' ?></td>
                        <td>
                            <?php if ($pg['is_published'] == 1): ?>
                                <span class="adm-badge published">Published</span>
                            <?php else: ?>
                                <span class="adm-badge draft">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: var(--adm-text-muted);"><?= date('M j, Y', strtotime($pg['updated_at'])) ?></td>
                        <td style="text-align: right;">
                            <a href="../page.php?slug=<?= urlencode($pg['slug']) ?>" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm" title="Preview Public Page">Preview</a>
                            <a href="page_edit.php?id=<?= $pg['id'] ?>" class="adm-btn adm-btn-primary adm-btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>
