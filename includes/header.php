<?php
// includes/header.php - Global Dark Theme Header
require_once __DIR__ . '/functions.php';

$siteTitle = get_setting('site_title', 'Anjum Iqbal');
$siteTagline = get_setting('site_tagline', 'Skill Development & Empowerment');
$announcementEnabled = get_setting('announcement_enabled', '1');
$scamNotice = get_setting('scam_alert_banner', '⚠️ Official Security Notice: Anjum Iqbal never charges any registration fees or asks for OTPs/passwords.');
$primaryYoutube = get_setting('primary_youtube', 'https://youtube.com/@anjumiqbal');
$navPages = get_nav_pages();

$pageTitle = isset($pageTitle) ? $pageTitle . ' | ' . $siteTitle : $siteTitle . ' | Learn Today, Lead Tomorrow';
$pageDesc = isset($pageDesc) ? $pageDesc : 'Anjum Iqbal - Free digital educator empowering people with high-value digital skills, online opportunities, and scam safety.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= clean($pageDesc) ?>">
    <title><?= clean($pageTitle) ?></title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Modern Dark Theme Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= file_exists(__DIR__ . '/../assets/css/style.css') ? filemtime(__DIR__ . '/../assets/css/style.css') : '2.0' ?>">
    
    <link rel="icon" type="image/jpeg" href="assets/images/logo.jpg">
</head>
<body>

<!-- Main Sticky Header -->
<header class="site-header">
    <div class="container navbar">
        <a href="index.php" class="brand-wrapper">
            <img src="assets/images/logo.jpg" alt="<?= clean($siteTitle) ?> Logo" class="brand-logo" onerror="this.src='logo.jpg'">
            <div class="brand-info">
                <strong><?= clean($siteTitle) ?> <i class="verified-icon" title="Verified Educator">✓</i></strong>
                <small><?= clean($siteTagline) ?></small>
            </div>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="nav-links" id="navLinks">
            <a href="index.php#home">Home</a>
            <a href="index.php#tracks">Free Tracks</a>
            <a href="index.php#about">About</a>
            <a href="page.php?slug=scam-alert" class="alert-link">🛡️ Scam Alert</a>
            
            <?php foreach ($navPages as $np): ?>
                <?php if ($np['slug'] !== 'scam-alert'): ?>
                    <a href="page.php?slug=<?= urlencode($np['slug']) ?>"><?= clean($np['title']) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>

            <a href="index.php#contact">Contact</a>
        </nav>

        <!-- Right Header Action Button -->
        <div class="nav-actions">
            <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="btn btn-youtube btn-sm" title="Watch Free Lessons on YouTube" aria-label="Watch Free on YouTube">
                <?= get_social_icon_svg('youtube') ?>
                <span>Watch Free</span>
            </a>
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Toggle Navigation Menu">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
</header>
