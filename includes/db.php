<?php
// includes/db.php - Database connection and automatic initialization

$dbPath = __DIR__ . '/../data/database.sqlite';

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Initialize tables if they do not exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            email TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login DATETIME
        );

        CREATE TABLE IF NOT EXISTS site_settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT
        );

        CREATE TABLE IF NOT EXISTS pages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            slug TEXT UNIQUE NOT NULL,
            subtitle TEXT,
            content TEXT NOT NULL,
            meta_description TEXT,
            is_system INTEGER DEFAULT 0,
            is_published INTEGER DEFAULT 1,
            show_in_nav INTEGER DEFAULT 1,
            show_in_footer INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS social_links (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            platform TEXT NOT NULL,
            url TEXT NOT NULL,
            icon TEXT NOT NULL,
            color TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT,
            message TEXT NOT NULL,
            is_read INTEGER DEFAULT 0,
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Seed default admin if table is empty
    $stmt = $db->query("SELECT COUNT(*) as count FROM admins");
    if ($stmt->fetch()['count'] == 0) {
        $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $insertAdmin = $db->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
        $insertAdmin->execute(['admin', $defaultPassword, 'contact.anjum3@gmail.com']);
    }

    // Seed default site settings if table is empty
    $stmt = $db->query("SELECT COUNT(*) as count FROM site_settings");
    if ($stmt->fetch()['count'] == 0) {
        $defaultSettings = [
            'site_title' => 'Anjum Iqbal',
            'site_tagline' => 'Skill Development & Empowerment',
            'hero_badge' => "Pakistan's Free Digital Educator",
            'hero_headline' => 'Learn Today. <span class="gradient-text">Lead Tomorrow.</span>',
            'hero_description' => 'Empowering you to master high-value digital skills, navigate real-world online earning, and build long-term independence — 100% free, honest, and step by step.',
            'primary_youtube' => 'https://youtube.com/@anjumiqbal',
            'primary_whatsapp' => 'https://whatsapp.com/channel/0029Va7o27jGJP8KmhGLJQ3x',
            'contact_email' => 'contact.anjum3@gmail.com',
            'scam_alert_banner' => '⚠️ Official Security Notice: Anjum Iqbal never charges any registration fees, asks for passwords or OTPs, or offers guaranteed returns. Beware of fake accounts.',
            'announcement_enabled' => '1',
            'about_heading' => 'Useful knowledge, shared with purpose.',
            'about_bio' => 'A friendly space for practical advice, digital skills, online opportunities, and everyday knowledge. The mission is to empower people with valuable skills, encourage continuous learning, and help individuals explore realistic ways to work from home, build independence, and improve their lives.',
            'stat_learners' => '100% Free',
            'stat_fee' => 'Rs 0 Tuition',
            'stat_mission' => 'Honest Skills',
            'footer_copyright' => '© 2026 Anjum Iqbal. All rights reserved.'
        ];

        $insertSetting = $db->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
        foreach ($defaultSettings as $k => $v) {
            $insertSetting->execute([$k, $v]);
        }
    }

    // Seed default social links if table is empty
    $stmt = $db->query("SELECT COUNT(*) as count FROM social_links");
    if ($stmt->fetch()['count'] == 0) {
        $defaultSocials = [
            ['platform' => 'YouTube (Primary)', 'url' => 'https://youtube.com/@anjumiqbal', 'icon' => 'youtube', 'color' => '#ff0033', 'sort_order' => 1],
            ['platform' => 'YouTube (Second)', 'url' => 'https://youtube.com/@anjumiqbalyt', 'icon' => 'youtube', 'color' => '#ff0033', 'sort_order' => 2],
            ['platform' => 'WhatsApp Channel', 'url' => 'https://whatsapp.com/channel/0029Va7o27jGJP8KmhGLJQ3x', 'icon' => 'whatsapp', 'color' => '#25d366', 'sort_order' => 3],
            ['platform' => 'TikTok', 'url' => 'https://www.tiktok.com/@anjumiqbal9999', 'icon' => 'tiktok', 'color' => '#00f2fe', 'sort_order' => 4],
            ['platform' => 'Facebook', 'url' => 'https://www.facebook.com/share/1CSHEwBiZQ/', 'icon' => 'facebook', 'color' => '#1877f2', 'sort_order' => 5],
            ['platform' => 'Instagram', 'url' => 'https://www.instagram.com/anjum.iqbal950', 'icon' => 'instagram', 'color' => '#e1306c', 'sort_order' => 6],
        ];

        $insertSocial = $db->prepare("INSERT INTO social_links (platform, url, icon, color, sort_order) VALUES (?, ?, ?, ?, ?)");
        foreach ($defaultSocials as $s) {
            $insertSocial->execute([$s['platform'], $s['url'], $s['icon'], $s['color'], $s['sort_order']]);
        }
    }

    // Seed default pages if table is empty
    $stmt = $db->query("SELECT COUNT(*) as count FROM pages");
    if ($stmt->fetch()['count'] == 0) {
        $defaultPages = [
            [
                'title' => 'Scam Alert & Safety Guide',
                'slug' => 'scam-alert',
                'subtitle' => 'Protect yourself from fake impersonators and fraudulent schemes',
                'meta_description' => 'Important warning and safety guide from Anjum Iqbal to avoid online scams, phishing, and fake investment offers.',
                'is_system' => 1,
                'show_in_nav' => 1,
                'show_in_footer' => 1,
                'sort_order' => 1,
                'content' => '<h3>Online Safety Notice</h3>
<p class="lead">Impersonators frequently use creator names, channel graphics, or fake screenshots to solicit money, promote illicit investment pools, or steal account credentials.</p>

<div class="alert-box danger">
    <h4>⚠️ Essential Verification Rules</h4>
    <ul>
        <li><strong>Rs 0 Fee:</strong> Anjum Iqbal never charges any fee to learn or access video guides.</li>
        <li><strong>No Guaranteed Profits:</strong> Any scheme promising fixed daily returns, double money, or automated trading bots is a scam.</li>
        <li><strong>Never Share OTPs:</strong> Never provide bank one-time passcodes, passwords, or CNIC copies to strangers on WhatsApp or Telegram.</li>
    </ul>
</div>

<h3>Common Warning Signs</h3>
<div class="card-grid-2">
    <div class="content-card">
        <h4>1. Advance Fee Demands</h4>
        <p>Scammers ask for an "activation fee", "security deposit", or "withdrawal release fee" before releasing supposed earnings. Once sent, the money is lost.</p>
    </div>
    <div class="content-card">
        <h4>2. Urgency and Secrecy</h4>
        <p>They pressure you to make instant transactions or forbid you from telling your family and friends. Legitimate business never relies on panicked pressure.</p>
    </div>
    <div class="content-card">
        <h4>3. Fake Telegram & WhatsApp Groups</h4>
        <p>Impersonator groups copy channel names and post fake payment proofs created in Photoshop. Only follow the official verified channel links listed on this website.</p>
    </div>
    <div class="content-card">
        <h4>4. Impersonation of Support</h4>
        <p>No member of Anjum Iqbal\'s team will ever message you privately offering crypto trading management or private investment portfolios.</p>
    </div>
</div>

<h3>What To Do If Targeted</h3>
<ol class="step-list">
    <li><strong>Immediately Cease Communication:</strong> Block the sender across all numbers and social channels.</li>
    <li><strong>Secure Your Financial Accounts:</strong> If you revealed card numbers or banking passwords, notify your bank immediately to freeze the accounts.</li>
    <li><strong>Preserve Evidence:</strong> Take clear screenshots showing phone numbers, handles, bank IBANs, and chat logs.</li>
    <li><strong>Report the Impersonator:</strong> File a cybercrime complaint with relevant national authorities (such as FIA Cyber Crime Wing in Pakistan) and report the profile on the platform.</li>
</ol>'
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'subtitle' => 'How we respect and handle information across our website and channels',
                'meta_description' => 'Privacy Policy for Anjum Iqbal official website and digital educational channels.',
                'is_system' => 1,
                'show_in_nav' => 0,
                'show_in_footer' => 1,
                'sort_order' => 2,
                'content' => '<p><strong>Last Updated: November 2026</strong></p>
<p>This Privacy Policy outlines how Anjum Iqbal collects, uses, and safeguards information when you visit this website, submit inquiries, or interact with our official social media handles.</p>

<h3>Information We Collect</h3>
<p>When you contact us using our contact form, we collect your name, email address, subject, and the message content. This information is used strictly to reply to your inquiry, provide support, and improve our services.</p>

<h3>Log Data & Analytics</h3>
<p>Standard web server logs may automatically record technical diagnostic data including IP addresses, browser types, referring pages, and access timestamps to ensure uptime, security, and defense against malicious activity.</p>

<h3>Third-Party Platforms</h3>
<p>Our website links to third-party services including YouTube, WhatsApp, TikTok, Facebook, and Instagram. When clicking external links, you are subject to the privacy practices and terms of those third-party services. We recommend reviewing their respective privacy policies.</p>

<h3>Cookies</h3>
<p>We do not use tracking cookies for intrusive advertising. Basic session cookies may be used strictly for admin panel authentication and CSRF security.</p>

<h3>Contact</h3>
<p>For any questions regarding privacy, reach out at <a href="mailto:contact.anjum3@gmail.com">contact.anjum3@gmail.com</a>.</p>'
            ],
            [
                'title' => 'Disclaimer',
                'slug' => 'disclaimer',
                'subtitle' => 'General educational disclaimer and limitation of liability',
                'meta_description' => 'Disclaimer regarding the educational nature of content provided by Anjum Iqbal.',
                'is_system' => 1,
                'show_in_nav' => 0,
                'show_in_footer' => 1,
                'sort_order' => 3,
                'content' => '<p><strong>Last Updated: November 2026</strong></p>
<p>The information and educational materials provided by Anjum Iqbal across this website, video channels, and social media platforms are strictly for general educational and informational purposes. It does not constitute licensed financial, investment, legal, tax, or medical advice.</p>

<h3>No Income or Earning Guarantees</h3>
<p>Tutorials, case studies, and digital skill roadmaps discussed do not guarantee specific income, revenue, client acquisition, or commercial results. Individual outcomes depend entirely on each person\'s dedication, market dynamics, skill execution, and diligence.</p>

<h3>Independent Verification</h3>
<p>Always conduct your own independent research and exercise due diligence before engaging with any digital platform, payment gateway, software service, or financial opportunity.</p>

<h3>Limitation of Liability</h3>
<p>To the fullest extent permitted by law, Anjum Iqbal disclaims all liability for any direct or indirect loss, financial cost, or setback arising from reliance on educational content or external third-party links.</p>'
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'subtitle' => 'Website usage guidelines and intellectual property notice',
                'meta_description' => 'Terms and Conditions for using the official Anjum Iqbal educational portal.',
                'is_system' => 1,
                'show_in_nav' => 0,
                'show_in_footer' => 1,
                'sort_order' => 4,
                'content' => '<p><strong>Last Updated: November 2026</strong></p>
<p>By accessing and utilizing this website, you accept and agree to be bound by these Terms and Conditions. If you do not agree with any part of these terms, please discontinue use of the site.</p>

<h3>Intellectual Property</h3>
<p>All original content, visual design, custom logos, text, and structure remain the property of Anjum Iqbal. You may share links and quotes for personal non-commercial educational use with appropriate attribution. Republication, cloning, or commercial redistribution without prior consent is prohibited.</p>

<h3>Acceptable Use</h3>
<p>You agree not to use this website in any manner that impairs site security, transmits harmful software, spams contact channels, or falsely implies official endorsement.</p>

<h3>Modifications</h3>
<p>We reserve the right to revise or update these terms at any time. Your continued use of the website following any updates indicates acceptance of the amended terms.</p>'
            ]
        ];

        $insertPage = $db->prepare("INSERT INTO pages (title, slug, subtitle, meta_description, is_system, show_in_nav, show_in_footer, sort_order, content) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($defaultPages as $p) {
            $insertPage->execute([$p['title'], $p['slug'], $p['subtitle'], $p['meta_description'], $p['is_system'], $p['show_in_nav'], $p['show_in_footer'], $p['sort_order'], $p['content']]);
        }
    }

} catch (PDOException $e) {
    die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
}
