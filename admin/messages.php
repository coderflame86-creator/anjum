<?php
// admin/messages.php - Contact Inquiries Management
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Contact Messages';
$headerTitle = 'Contact Form Inquiries';

// Handle single message view modal / details
$viewId = isset($_GET['view']) ? (int)$_GET['view'] : 0;
if ($viewId > 0) {
    // Automatically mark as read when viewed
    $markStmt = $db->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
    $markStmt->execute([$viewId]);
}

// Handle delete action
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $delStmt = $db->prepare("DELETE FROM contact_messages WHERE id = ?");
    $delStmt->execute([$delId]);
    set_flash('success', 'Contact message deleted.');
    header("Location: messages.php");
    exit;
}

// Handle mark as read / unread toggle
if (isset($_GET['toggle_read'])) {
    $togId = (int)$_GET['toggle_read'];
    $togStmt = $db->prepare("UPDATE contact_messages SET is_read = 1 - is_read WHERE id = ?");
    $togStmt->execute([$togId]);
    set_flash('success', 'Message status updated.');
    header("Location: messages.php");
    exit;
}

// Handle mark all as read
if (isset($_GET['mark_all_read'])) {
    $db->exec("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0");
    set_flash('success', 'All messages marked as read.');
    header("Location: messages.php");
    exit;
}

// Filters & Search
$filter = $_GET['filter'] ?? 'all'; // all, unread, read
$search = trim($_GET['search'] ?? '');

$sql = "SELECT * FROM contact_messages WHERE 1=1";
$params = [];

if ($filter === 'unread') {
    $sql .= " AND is_read = 0";
} elseif ($filter === 'read') {
    $sql .= " AND is_read = 1";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $term = "%{$search}%";
    $params = array_merge($params, [$term, $term, $term, $term]);
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Counts for tabs
$countAll = (int)$db->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
$countUnread = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$countRead = (int)$db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 1")->fetchColumn();

// If viewing a single message
$activeMessage = null;
if ($viewId > 0) {
    $stmtView = $db->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmtView->execute([$viewId]);
    $activeMessage = $stmtView->fetch();
}

include __DIR__ . '/admin_header.php';
?>

<!-- Single Message View Box (if active) -->
<?php if ($activeMessage): ?>
    <div class="adm-card" style="border: 1px solid var(--adm-emerald-light); background: var(--adm-surface-elevated);">
        <div class="adm-card-header">
            <div>
                <span class="adm-badge" style="background: rgba(16,185,129,0.2); color: var(--adm-emerald-light); margin-bottom: 6px;">Message Details</span>
                <h3 style="font-size: 1.3rem;"><?= clean($activeMessage['subject'] ?: 'No Subject') ?></h3>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="mailto:<?= clean($activeMessage['email']) ?>?subject=Re:%20<?= urlencode($activeMessage['subject'] ?? 'Inquiry') ?>" class="adm-btn adm-btn-primary adm-btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>Reply via Email</span>
                </a>
                <a href="messages.php" class="adm-btn adm-btn-secondary adm-btn-sm">&times; Close View</a>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 22px; padding-bottom: 18px; border-bottom: 1px solid var(--adm-border); font-size: 0.88rem;">
            <div>
                <span style="color: var(--adm-text-muted); display: block; font-size: 0.78rem;">SENDER NAME</span>
                <strong style="color: #fff; font-size: 1rem;"><?= clean($activeMessage['name']) ?></strong>
            </div>
            <div>
                <span style="color: var(--adm-text-muted); display: block; font-size: 0.78rem;">EMAIL ADDRESS</span>
                <a href="mailto:<?= clean($activeMessage['email']) ?>" style="color: var(--adm-cyan); font-weight: 600;"><?= clean($activeMessage['email']) ?></a>
            </div>
            <div>
                <span style="color: var(--adm-text-muted); display: block; font-size: 0.78rem;">RECEIVED DATE</span>
                <span style="color: #cbd5e1;"><?= date('F j, Y - g:i A', strtotime($activeMessage['created_at'])) ?></span>
            </div>
            <div>
                <span style="color: var(--adm-text-muted); display: block; font-size: 0.78rem;">IP ADDRESS</span>
                <code style="color: var(--adm-text-secondary);"><?= clean($activeMessage['ip_address'] ?? 'Unknown') ?></code>
            </div>
        </div>

        <div>
            <span style="color: var(--adm-text-muted); display: block; font-size: 0.78rem; margin-bottom: 8px;">MESSAGE BODY:</span>
            <div style="background: rgba(11, 15, 25, 0.7); border: 1px solid var(--adm-border); border-radius: var(--radius-sm); padding: 22px; color: #f1f5f9; font-size: 0.95rem; line-height: 1.7; white-space: pre-wrap;"><?= clean($activeMessage['message']) ?></div>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center;">
            <a href="messages.php?toggle_read=<?= $activeMessage['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm">
                Mark as <?= ($activeMessage['is_read'] == 1) ? 'Unread' : 'Read' ?>
            </a>
            <a href="messages.php?delete=<?= $activeMessage['id'] ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Are you sure you want to delete this message?">
                Delete Message
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Inbox Card -->
<div class="adm-card">
    <div class="adm-card-header" style="flex-wrap: wrap; gap: 15px;">
        <div>
            <h3>Contact Messages Inbox</h3>
            <p style="font-size: 0.85rem; color: var(--adm-text-muted); margin-top: 4px;">Inquiries, questions, and feedback submitted through the public website.</p>
        </div>

        <!-- Filter & Search Controls -->
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <form method="GET" action="messages.php" style="display: flex; gap: 8px;">
                <input type="hidden" name="filter" value="<?= clean($filter) ?>">
                <input type="text" name="search" class="adm-input" placeholder="Search sender, email..." value="<?= clean($search) ?>" style="padding: 7px 12px; width: 200px; font-size: 0.82rem;">
                <button type="submit" class="adm-btn adm-btn-secondary adm-btn-sm">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="messages.php?filter=<?= clean($filter) ?>" class="adm-btn adm-btn-secondary adm-btn-sm">&times;</a>
                <?php endif; ?>
            </form>

            <?php if ($countUnread > 0): ?>
                <a href="messages.php?mark_all_read=1" class="adm-btn adm-btn-secondary adm-btn-sm" title="Mark all messages as read">Mark All Read</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 8px; margin-bottom: 20px; border-bottom: 1px solid var(--adm-border); padding-bottom: 12px;">
        <a href="messages.php?filter=all" class="adm-btn adm-btn-sm <?= ($filter === 'all') ? 'adm-btn-primary' : 'adm-btn-secondary' ?>">
            All (<?= $countAll ?>)
        </a>
        <a href="messages.php?filter=unread" class="adm-btn adm-btn-sm <?= ($filter === 'unread') ? 'adm-btn-primary' : 'adm-btn-secondary' ?>">
            Unread (<?= $countUnread ?>)
        </a>
        <a href="messages.php?filter=read" class="adm-btn adm-btn-sm <?= ($filter === 'read') ? 'adm-btn-primary' : 'adm-btn-secondary' ?>">
            Read (<?= $countRead ?>)
        </a>
    </div>

    <?php if (empty($messages)): ?>
        <p style="color: var(--adm-text-muted); font-size: 0.9rem; text-align: center; padding: 40px 0;">
            No messages found matching your criteria.
        </p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Status</th>
                        <th>Sender</th>
                        <th>Email</th>
                        <th>Subject & Preview</th>
                        <th>Date Received</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $m): ?>
                        <tr style="<?= ($m['is_read'] == 0) ? 'background: rgba(16, 185, 129, 0.04);' : '' ?>">
                            <td>
                                <?php if ($m['is_read'] == 0): ?>
                                    <span class="adm-badge unread">Unread</span>
                                <?php else: ?>
                                    <span class="adm-badge read">Read</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #fff;"><?= clean($m['name']) ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?= clean($m['email']) ?>" style="color: var(--adm-cyan);"><?= clean($m['email']) ?></a>
                            </td>
                            <td>
                                <strong style="color: #cbd5e1;"><?= clean($m['subject'] ?: 'Inquiry') ?></strong>
                                <div style="font-size: 0.78rem; color: var(--adm-text-muted); max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?= clean($m['message']) ?>
                                </div>
                            </td>
                            <td style="color: var(--adm-text-muted); font-size: 0.8rem; white-space: nowrap;">
                                <?= date('M j, Y - H:i', strtotime($m['created_at'])) ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="messages.php?view=<?= $m['id'] ?>" class="adm-btn adm-btn-primary adm-btn-sm">View</a>
                                <a href="messages.php?toggle_read=<?= $m['id'] ?>" class="adm-btn adm-btn-secondary adm-btn-sm" title="Toggle status">
                                    <?= ($m['is_read'] == 1) ? 'Mark Unread' : 'Mark Read' ?>
                                </a>
                                <a href="messages.php?delete=<?= $m['id'] ?>" class="adm-btn adm-btn-danger adm-btn-sm" data-confirm="Delete this message?">&times;</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>
