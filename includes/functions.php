<?php
// includes/functions.php - Global utility functions

if (session_status() === PHP_SESSION_NONE) {
    $sessionDir = __DIR__ . '/../data/sessions';
    if (!is_dir($sessionDir)) {
        @mkdir($sessionDir, 0777, true);
    }
    if (is_dir($sessionDir) && is_writable($sessionDir)) {
        session_save_path($sessionDir);
    }
    @session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Fetch a site setting value by key
 */
function get_setting($key, $default = '') {
    global $db;
    try {
        $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Update or insert a site setting
 */
function update_setting($key, $value) {
    global $db;
    $stmt = $db->prepare("INSERT OR REPLACE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    return $stmt->execute([$key, $value]);
}

/**
 * Fetch all active social links ordered by sort_order
 */
function get_active_socials() {
    global $db;
    try {
        $stmt = $db->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Fetch navigation pages
 */
function get_nav_pages() {
    global $db;
    try {
        $stmt = $db->query("SELECT title, slug FROM pages WHERE is_published = 1 AND show_in_nav = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Fetch footer pages
 */
function get_footer_pages() {
    global $db;
    try {
        $stmt = $db->query("SELECT title, slug FROM pages WHERE is_published = 1 AND show_in_footer = 1 ORDER BY sort_order ASC, id ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Clean & sanitize text input
 */
function clean($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Flash messages helper
 */
function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // success, danger, warning, info
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

/**
 * Returns brand styling details (original colors, gradients, shadows)
 */
function get_social_brand_info($iconName) {
    $iconName = strtolower(trim($iconName));
    switch ($iconName) {
        case 'youtube':
            return [
                'bg' => '#FF0000',
                'color' => '#FFFFFF',
                'border' => '#FF0000',
                'shadow' => 'rgba(255, 0, 0, 0.4)',
                'label' => 'YouTube',
                'class' => 'brand-youtube'
            ];
        case 'whatsapp':
            return [
                'bg' => '#25D366',
                'color' => '#FFFFFF',
                'border' => '#25D366',
                'shadow' => 'rgba(37, 211, 102, 0.4)',
                'label' => 'WhatsApp',
                'class' => 'brand-whatsapp'
            ];
        case 'tiktok':
            return [
                'bg' => '#000000',
                'color' => '#FFFFFF',
                'border' => '#FE2C55',
                'shadow' => 'rgba(254, 44, 85, 0.4)',
                'label' => 'TikTok',
                'class' => 'brand-tiktok'
            ];
        case 'facebook':
            return [
                'bg' => '#1877F2',
                'color' => '#FFFFFF',
                'border' => '#1877F2',
                'shadow' => 'rgba(24, 119, 242, 0.4)',
                'label' => 'Facebook',
                'class' => 'brand-facebook'
            ];
        case 'instagram':
            return [
                'bg' => 'linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)',
                'color' => '#FFFFFF',
                'border' => '#e1306c',
                'shadow' => 'rgba(225, 48, 108, 0.4)',
                'label' => 'Instagram',
                'class' => 'brand-instagram'
            ];
        case 'x':
        case 'twitter':
            return [
                'bg' => '#000000',
                'color' => '#FFFFFF',
                'border' => '#334155',
                'shadow' => 'rgba(255, 255, 255, 0.2)',
                'label' => 'X (Twitter)',
                'class' => 'brand-x'
            ];
        case 'telegram':
            return [
                'bg' => '#24A1DE',
                'color' => '#FFFFFF',
                'border' => '#24A1DE',
                'shadow' => 'rgba(36, 161, 222, 0.4)',
                'label' => 'Telegram',
                'class' => 'brand-telegram'
            ];
        case 'linkedin':
            return [
                'bg' => '#0A66C2',
                'color' => '#FFFFFF',
                'border' => '#0A66C2',
                'shadow' => 'rgba(10, 102, 194, 0.4)',
                'label' => 'LinkedIn',
                'class' => 'brand-linkedin'
            ];
        default:
            return [
                'bg' => '#10B981',
                'color' => '#FFFFFF',
                'border' => '#10B981',
                'shadow' => 'rgba(16, 185, 129, 0.4)',
                'label' => 'Web',
                'class' => 'brand-default'
            ];
    }
}

/**
 * Returns inline clean SVG icons for major social platforms
 */
function get_social_icon_svg($iconName) {
    $iconName = strtolower(trim($iconName));
    
    switch ($iconName) {
        case 'youtube':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>';
            
        case 'whatsapp':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.456 5.711 1.456h.005c6.554 0 11.89-5.335 11.893-11.893a11.82 11.82 0 00-3.48-8.413z"/></svg>';

        case 'tiktok':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-5.2 1.74 2.89 2.89 0 012.31-4.64c.298-.002.595.042.88.13V9.4a6.33 6.33 0 00-1-.08A6.34 6.34 0 003 15.66a6.34 6.34 0 0010.86 4.43c.27-.27.5-.57.69-.9V10.2a8.28 8.28 0 005.04 1.72V8.47a4.84 4.84 0 01-1.8-.78z"/></svg>';

        case 'facebook':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';

        case 'instagram':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>';

        case 'twitter':
        case 'x':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';

        case 'linkedin':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>';

        case 'telegram':
            return '<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>';

        default:
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20M2 12h20"/></svg>';
    }
}
