<?php
// includes/footer.php - Global Dark Theme Footer
require_once __DIR__ . '/functions.php';

$siteTitle = get_setting('site_title', 'Anjum Iqbal');
$siteTagline = get_setting('site_tagline', 'Skill Development & Empowerment');
$footerCopyright = get_setting('footer_copyright', '© 2026 Anjum Iqbal. All rights reserved.');
$activeSocials = get_active_socials();
$footerPages = get_footer_pages();
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <strong><?= clean($siteTitle) ?> <i class="verified-icon">✓</i></strong>
                <p>Dedicated to delivering 100% free, honest digital education, real online skills, and cybersecurity awareness to empower the community.</p>
                <div style="font-size: 0.8rem; color: #64748b;">
                    Official Portal · <a href="admin/login.php" style="color: #64748b; text-decoration: underline;">Admin Area</a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Navigation</h4>
                <ul class="footer-links">
                    <li><a href="index.php#home">Home</a></li>
                    <li><a href="index.php#tracks">Curriculum Tracks</a></li>
                    <li><a href="index.php#about">About Anjum Iqbal</a></li>
                    <li><a href="page.php?slug=scam-alert" style="color: #fda4af;">Shield & Scam Alert</a></li>
                    <li><a href="index.php#contact">Contact & Support</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Policies & Information</h4>
                <ul class="footer-links">
                    <?php foreach ($footerPages as $fp): ?>
                        <li><a href="page.php?slug=<?= urlencode($fp['slug']) ?>"><?= clean($fp['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div><?= clean($footerCopyright) ?></div>
            <div class="footer-social-group">
                <?php foreach ($activeSocials as $soc): ?>
                    <?php $brand = get_social_brand_info($soc['icon']); ?>
                    <a href="<?= clean($soc['url']) ?>" target="_blank" rel="noopener" class="footer-social-pill <?= $brand['class'] ?>" title="<?= clean($soc['platform']) ?>">
                        <?= get_social_icon_svg($soc['icon']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</footer>

<!-- Interactive Scripts -->
<script src="assets/js/main.js"></script>
</body>
</html>
