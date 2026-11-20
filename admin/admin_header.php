<?php
// admin/admin_header.php - Shared Admin Dashboard Header & Sidebar
require_once __DIR__ . '/auth.php';

$currentScript = basename($_SERVER['PHP_SELF']);

// Count unread messages for notification badge
$unreadCountStmt = $db->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$unreadMessagesCount = $unreadCountStmt ? (int)$unreadCountStmt->fetchColumn() : 0;

$flash = get_flash();
$siteTitle = get_setting('site_title', 'Anjum Iqbal');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? clean($pageTitle) . ' | Admin Panel' : 'Admin Panel | ' . clean($siteTitle) ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" type="image/jpeg" href="../assets/images/logo.jpg">
</head>
<body>

<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-brand">
                <img src="../assets/images/logo.jpg" alt="Logo" onerror="this.src='../logo.jpg'">
                <div>
                    <strong><?= clean($siteTitle) ?></strong>
                    <small>Admin Panel</small>
                </div>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="sidebar-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="pages.php" class="sidebar-link <?= in_array($currentScript, ['pages.php', 'page_edit.php']) ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span>Manage Pages</span>
                </a>
            </li>
            <li>
                <a href="messages.php" class="sidebar-link <?= ($currentScript === 'messages.php') ? 'active' : '' ?>" style="justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span>Contact Messages</span>
                    </div>
                    <?php if ($unreadMessagesCount > 0): ?>
                        <span class="adm-badge unread" style="padding: 2px 7px;"><?= $unreadMessagesCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="socials.php" class="sidebar-link <?= ($currentScript === 'socials.php') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <span>Social Media Links</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="sidebar-link <?= ($currentScript === 'settings.php') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>Site Settings</span>
                </a>
            </li>
            <li>
                <a href="profile.php" class="sidebar-link <?= ($currentScript === 'profile.php') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Security & Password</span>
                </a>
            </li>
            <li>
                <a href="guide.php" class="sidebar-link <?= ($currentScript === 'guide.php') ? 'active' : '' ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8" fill="currentColor"/></svg>
                    <span>Video Manual</span>
                    <span class="adm-badge" style="background: rgba(16, 185, 129, 0.15); color: var(--adm-emerald-light); font-size: 0.65rem; padding: 2px 6px;">Guide</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div style="display: flex; gap: 8px;">
                <a href="../index.php" target="_blank" class="adm-btn adm-btn-secondary adm-btn-sm" style="flex: 1; justify-content: center;" title="View Live Website">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
                    <span>Live Site</span>
                </a>
                <a href="logout.php" class="adm-btn adm-btn-danger adm-btn-sm" style="justify-content: center;" title="Logout">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-title"><?= isset($headerTitle) ? clean($headerTitle) : 'Overview' ?></div>
            <div class="topbar-actions">
                <span style="font-size: 0.85rem; color: var(--adm-text-secondary);">Logged in as: <strong style="color: #fff;"><?= clean($adminUsername) ?></strong></span>
                <a href="logout.php" class="adm-btn adm-btn-secondary adm-btn-sm">Logout</a>
            </div>
        </header>

        <main class="admin-body">
            <?php if ($flash): ?>
                <div class="adm-alert <?= clean($flash['type']) ?>">
                    <span><?= clean($flash['message']) ?></span>
                </div>
            <?php endif; ?>
