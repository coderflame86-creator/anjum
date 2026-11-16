<?php
// index.php - Redesigned Dynamic Homepage for Anjum Iqbal
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Anjum Iqbal | Learn Today, Lead Tomorrow';
$pageDesc = 'Empowering learners with high-value digital skills, online earning education, and scam safety — 100% free.';

$heroBadge = get_setting('hero_badge', "Pakistan's Free Digital Educator");
$heroHeadline = get_setting('hero_headline', 'Learn Today. <span class="gradient-text">Lead Tomorrow.</span>');
$heroDesc = get_setting('hero_description', 'Empowering you to master high-value digital skills, discover genuine digital opportunities, and build long-term independence — 100% free, honest, and step by step.');
$primaryYoutube = get_setting('primary_youtube', 'https://youtube.com/@anjumiqbal');
$primaryWhatsapp = get_setting('primary_whatsapp', 'https://whatsapp.com/channel/0029Va7o27jGJP8KmhGLJQ3x');
$contactEmail = get_setting('contact_email', 'contact.anjum3@gmail.com');
$aboutHeading = get_setting('about_heading', 'Useful knowledge, shared with purpose.');
$aboutBio = get_setting('about_bio', 'A friendly space for practical advice, digital skills, online opportunities, and everyday knowledge. The mission is to empower people with valuable skills, encourage continuous learning, and help individuals explore realistic ways to work from home, build independence, and improve their lives.');

$activeSocials = get_active_socials();

include __DIR__ . '/includes/header.php';
?>

<main id="home">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="eyebrow"><?= clean($heroBadge) ?></span>
                <h1><?= $heroHeadline ?></h1>
                <p><?= clean($heroDesc) ?></p>

                <div class="hero-actions">
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="btn btn-youtube">
                        <?= get_social_icon_svg('youtube') ?>
                        <span>Watch Free Lessons</span>
                    </a>
                    <a href="<?= clean($primaryWhatsapp) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp">
                        <?= get_social_icon_svg('whatsapp') ?>
                        <span>WhatsApp Channel</span>
                    </a>
                    <a href="#tracks" class="btn btn-outline">
                        <span>Explore Tracks</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 13l5 5 5-5M7 6l5 5 5-5"/></svg>
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="stat-item">
                        <h3>100% Free</h3>
                        <p>No fees, courses, or hidden charges</p>
                    </div>
                    <div class="stat-item">
                        <h3>Rs 0 Cost</h3>
                        <p>Open access knowledge for everyone</p>
                    </div>
                    <div class="stat-item">
                        <h3>Verified</h3>
                        <p>Official channels & scam-checked advice</p>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <div class="portrait-wrapper">
                    <img src="assets/images/hero-portrait.jpg" alt="Anjum Iqbal" class="portrait-img" onerror="this.src='hero-portrait.jpg'">
                    <div class="floating-badge">
                        <div class="badge-text">
                            <strong>Anjum Iqbal <i class="verified-icon">✓</i></strong>
                            <span>Learn Today, Lead Tomorrow</span>
                        </div>
                        <span class="badge badge-emerald">Active Mentor</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Prominent Official Social Channels Strip (Original Brand Colors) -->
    <section class="social-strip-section">
        <div class="container social-strip-inner">
            <div class="social-strip-label">
                <span class="live-pulse"></span>
                <span>Connect on Official Verified Channels:</span>
            </div>
            <div class="social-buttons">
                <?php foreach ($activeSocials as $soc): ?>
                    <?php $brand = get_social_brand_info($soc['icon']); ?>
                    <a href="<?= clean($soc['url']) ?>" target="_blank" rel="noopener" class="prominent-social-btn <?= $brand['class'] ?>" title="Follow on <?= clean($soc['platform']) ?>">
                        <span class="social-icon-wrapper">
                            <?= get_social_icon_svg($soc['icon']) ?>
                        </span>
                        <span class="social-btn-label"><?= clean($soc['platform']) ?></span>
                        <svg class="arrow-indicator" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 17l9.2-9.2M17 17V8H8"/></svg>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Learning Tracks / Curriculum Section (Similar to themrhow.com Curriculum Levels) -->
    <section id="tracks" class="section-padding surface">
        <div class="container">
            <div class="section-header">
                <span class="eyebrow">Structured Curriculum</span>
                <h2>Ideas That Help You <span class="gradient-text">Move Forward</span></h2>
                <p>Designed step-by-step from absolute beginner fundamentals to practical, sustainable digital capability.</p>
            </div>

            <div class="tracks-grid">
                <!-- Track 01 -->
                <article class="track-card">
                    <div class="track-top">
                        <span class="track-level">Level 01</span>
                        <span class="track-badge">Foundations</span>
                    </div>
                    <h3>Digital Skills & Mastery</h3>
                    <p>Clear explanations and repeatable steps for high-demand digital abilities, freelancing setups, and modern web tools.</p>
                    <ul class="track-features">
                        <li>High-demand skill selection</li>
                        <li>Portfolio & proof of work</li>
                        <li>Practical hands-on exercises</li>
                    </ul>
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="track-link">
                        <span>Start Level 01</span> &rarr;
                    </a>
                </article>

                <!-- Track 02 -->
                <article class="track-card">
                    <div class="track-top">
                        <span class="track-level">Level 02</span>
                        <span class="track-badge">Opportunities</span>
                    </div>
                    <h3>Online Earning & Remote Work</h3>
                    <p>Realistic insights into genuine digital opportunities, remote work platforms, client communication, and sustainable income.</p>
                    <ul class="track-features">
                        <li>Verified remote platforms</li>
                        <li>International payment setups</li>
                        <li>Avoiding fake earning apps</li>
                    </ul>
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="track-link">
                        <span>Explore Opportunities</span> &rarr;
                    </a>
                </article>

                <!-- Track 03 -->
                <article class="track-card">
                    <div class="track-top">
                        <span class="track-level">Level 03</span>
                        <span class="track-badge" style="color: #fb7185; border-color: rgba(244,63,94,0.3);">Safety</span>
                    </div>
                    <h3>Scam Defense & Threat Modeling</h3>
                    <p>Learn to identify deceptive Ponzi schemes, fake deposit traps, impersonator telegram groups, and identity theft.</p>
                    <ul class="track-features">
                        <li>Anatomy of financial fraud</li>
                        <li>WhatsApp / Telegram scam traps</li>
                        <li>Emergency response checklist</li>
                    </ul>
                    <a href="page.php?slug=scam-alert" class="track-link" style="color: #fb7185;">
                        <span>View Scam Guide</span> &rarr;
                    </a>
                </article>

                <!-- Track 04 -->
                <article class="track-card">
                    <div class="track-top">
                        <span class="track-level">Level 04</span>
                        <span class="track-badge">Automation</span>
                    </div>
                    <h3>AI & Modern Productivity</h3>
                    <p>Leveraging artificial intelligence tools to accelerate content creation, automate workflows, and boost everyday efficiency.</p>
                    <ul class="track-features">
                        <li>AI prompting for workflows</li>
                        <li>Automating repetitive tasks</li>
                        <li>Building digital assets faster</li>
                    </ul>
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="track-link">
                        <span>Explore AI Tools</span> &rarr;
                    </a>
                </article>

                <!-- Track 05 -->
                <article class="track-card">
                    <div class="track-top">
                        <span class="track-level">Level 05</span>
                        <span class="track-badge">Literacy</span>
                    </div>
                    <h3>Financial & Digital Literacy</h3>
                    <p>Understanding digital wallets, currency exchange safeguards, disciplined financial habits, and legal compliance.</p>
                    <ul class="track-features">
                        <li>Secure payment gateways</li>
                        <li>Account security & 2FA</li>
                        <li>Sustainable financial habits</li>
                    </ul>
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="track-link">
                        <span>Learn Digital Finance</span> &rarr;
                    </a>
                </article>

                <!-- Track 06 -->
                <article class="track-card">
                    <div class="track-top">
                        <span class="track-level">Level 06</span>
                        <span class="track-badge">Mindset</span>
                    </div>
                    <h3>Everyday Practical Wisdom</h3>
                    <p>Helpful advice for healthier lifestyle choices, critical thinking, overcoming digital burnout, and self-empowerment.</p>
                    <ul class="track-features">
                        <li>Continuous self-learning</li>
                        <li>Ethical work standards</li>
                        <li>Long-term life progression</li>
                    </ul>
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="track-link">
                        <span>Explore Wisdom</span> &rarr;
                    </a>
                </article>
            </div>
        </div>
    </section>

    <!-- Security & Scam Shield Section (Inspired by Mr How Safety Tools) -->
    <section class="section-padding">
        <div class="container">
            <div class="security-banner-box">
                <div class="security-grid">
                    <div class="security-info">
                        <span class="badge badge-danger">Official Security Shield</span>
                        <h3>Stay Alert. Never Pay To Learn.</h3>
                        <p>Impersonators and malicious actors may clone Anjum Iqbal's profiles to solicit deposits, promote fake investment schemes, or steal passwords.</p>
                        
                        <ul class="security-rules">
                            <li>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                <span><strong>Anjum Iqbal never asks for money:</strong> No registration fees, courses, VIP groups, or deposits.</span>
                            </li>
                            <li>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                <span><strong>No guaranteed profits:</strong> Anyone promising doubled money or fixed returns is 100% a fraud.</span>
                            </li>
                            <li>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                                <span><strong>Never share OTPs:</strong> Keep passwords, 2FA codes, and banking details strictly private.</span>
                            </li>
                        </ul>

                        <a href="page.php?slug=scam-alert" class="btn btn-danger">
                            <span>Open Full Scam Alert Guide</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="security-cards-side">
                        <div class="security-mini-card">
                            <h4>🚫 Fake Registration Fees</h4>
                            <p>Beware of anyone asking for an "account activation fee" or "enrollment deposit". All our resources are completely free.</p>
                        </div>
                        <div class="security-mini-card">
                            <h4>⚠️ WhatsApp Impersonators</h4>
                            <p>Verify phone numbers and never join unofficial trading pool links shared via private messages.</p>
                        </div>
                        <div class="security-mini-card">
                            <h4>🔒 Zero OTP Sharing</h4>
                            <p>No support agent from our team will ever request your verification code, PIN, or wallet phrase.</p>
                        </div>
                        <div class="security-mini-card">
                            <h4>⚖️ Report Fraud Promptly</h4>
                            <p>Immediately report fraudulent profiles to platform administrators and cybercrime authorities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section-padding surface">
        <div class="container about-grid">
            <div class="about-profile-card">
                <img src="assets/images/profile-pic.jpeg" alt="Anjum Iqbal Profile" class="about-profile-img" onerror="this.src='profile-pic.jpeg'">
                <div class="about-profile-info">
                    <h4>Anjum Iqbal</h4>
                    <p>Digital Educator & Skill Mentor</p>
                </div>
            </div>

            <div class="about-content">
                <span class="eyebrow">About The Mission</span>
                <h2><?= clean($aboutHeading) ?></h2>
                <p><?= nl2br(clean($aboutBio)) ?></p>

                <div class="pull-quote">
                    “Learn Today, Lead Tomorrow.”
                </div>

                <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                    <a href="<?= clean($primaryYoutube) ?>" target="_blank" rel="noopener" class="btn btn-youtube">
                        <?= get_social_icon_svg('youtube') ?>
                        <span>Subscribe on YouTube</span>
                    </a>
                    <a href="<?= clean($primaryWhatsapp) ?>" target="_blank" rel="noopener" class="btn btn-whatsapp">
                        <?= get_social_icon_svg('whatsapp') ?>
                        <span>Join Community Channel</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic Contact Section -->
    <section id="contact" class="section-padding">
        <div class="container contact-grid">
            <div class="contact-info-panel">
                <span class="eyebrow">Get In Touch</span>
                <h2>Keep Learning. <br><span class="gradient-text">Keep Building.</span></h2>
                <p>Have inquiries, collaboration proposals, feedback, or need to report an impersonator? Reach out directly via the form or through our verified channels.</p>

                <div class="contact-methods">
                    <div class="contact-method-item">
                        <div class="contact-method-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div class="contact-method-details">
                            <h4>Direct Inquiries</h4>
                            <a href="mailto:<?= clean($contactEmail) ?>"><?= clean($contactEmail) ?></a>
                        </div>
                    </div>

                    <div class="contact-method-item">
                        <div class="contact-method-icon" style="background: rgba(37, 211, 102, 0.12); color: #25d366;">
                            <?= get_social_icon_svg('whatsapp') ?>
                        </div>
                        <div class="contact-method-details">
                            <h4>Official Channel</h4>
                            <a href="<?= clean($primaryWhatsapp) ?>" target="_blank" rel="noopener">WhatsApp Channel Updates</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Contact Form -->
            <div class="contact-form-card">
                <div id="formAlert" style="display: none;" class="form-alert"></div>

                <form id="contactForm" method="POST" action="contact_submit.php">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                    <div class="form-group">
                        <label for="contact_name">Your Full Name *</label>
                        <input type="text" id="contact_name" name="name" class="form-control" placeholder="e.g. Ali Ahmed" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_email">Your Email Address *</label>
                        <input type="email" id="contact_email" name="email" class="form-control" placeholder="e.g. ali@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="contact_subject">Subject</label>
                        <input type="text" id="contact_subject" name="subject" class="form-control" placeholder="e.g. Collaboration / Feedback / Question">
                    </div>

                    <div class="form-group">
                        <label for="contact_message">Your Message *</label>
                        <textarea id="contact_message" name="message" class="form-control" placeholder="Write your message clearly..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <span>Send Message</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
