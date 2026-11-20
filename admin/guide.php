<?php
// admin/guide.php - Screencast Video Guide and Admin Operating Manual
require_once __DIR__ . '/admin_header.php';

$pageTitle = 'Video Manual & Guide';
$headerTitle = 'Admin Video Manual & Screencasting Guide';

// Handle updating external video embed URL
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['update_video_url'])) {
    $csrf = $_POST['csrf_token'] ?? '';
    if (verify_csrf_token($csrf)) {
        $videoUrl = trim($_POST['guide_video_url'] ?? '');
        update_setting('admin_guide_video_url', $videoUrl);
        set_flash('success', 'Custom screencast video URL updated successfully.');
        header('Location: guide.php');
        exit;
    }
}

$customVideoUrl = get_setting('admin_guide_video_url', '');
?>

<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 4px;">Screencast Video Guide & Operating Manual</h2>
        <p style="color: var(--adm-text-secondary); font-size: 0.88rem;">Interactive visual walkthrough and documentation for administering the Anjum Iqbal portal.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button id="toggleEmbedConfigBtn" class="adm-btn adm-btn-secondary adm-btn-sm">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            <span><?= !empty($customVideoUrl) ? 'Change Video Source' : 'Attach Custom Video URL' ?></span>
        </button>
        <a href="#manual-docs" class="adm-btn adm-btn-primary adm-btn-sm">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Read Written Manual</span>
        </a>
    </div>
</div>

<!-- Modal / Form to configure custom external video (Loom, YouTube, MP4) -->
<div id="embedConfigBox" class="adm-card" style="display: none; margin-bottom: 24px; padding: 20px 24px; border-color: rgba(6, 182, 212, 0.4); background: rgba(6, 182, 212, 0.04);">
    <h3 style="color: #fff; font-size: 1.05rem; margin-bottom: 6px;">Attach Your Own Screencast Video (YouTube / Loom / MP4)</h3>
    <p style="color: var(--adm-text-secondary); font-size: 0.82rem; margin-bottom: 16px;">
        Recorded your own video on Loom or YouTube? Paste the link here to play your recording directly inside this panel. Leave empty to use the built-in Interactive Screencast Simulator.
    </p>
    <form method="POST" action="guide.php" style="display: flex; gap: 10px; flex-wrap: wrap;">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="update_video_url" value="1">
        <input type="url" name="guide_video_url" class="adm-form-control" style="flex: 1; min-width: 280px;" placeholder="e.g. https://www.youtube.com/watch?v=... or https://www.loom.com/share/..." value="<?= clean($customVideoUrl) ?>">
        <button type="submit" class="adm-btn adm-btn-primary">Save Video Link</button>
        <?php if (!empty($customVideoUrl)): ?>
            <button type="button" onclick="document.querySelector('[name=guide_video_url]').value=''; this.form.submit();" class="adm-btn adm-btn-danger">Reset to Interactive Simulator</button>
        <?php endif; ?>
    </form>
</div>

<!-- Video Player & Chapters Grid -->
<div style="display: grid; grid-template-columns: 2.2fr 1fr; gap: 24px; margin-bottom: 35px;" id="playerContainer">
    <!-- Main Video Frame -->
    <div class="adm-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; background: #070a10; border-color: rgba(255, 255, 255, 0.1);">
        
        <!-- Player Topbar / Window Header -->
        <div style="background: rgba(17, 24, 39, 0.95); padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--adm-border);">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="display: flex; gap: 6px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></span>
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></span>
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
                </div>
                <span id="playerTitle" style="font-size: 0.85rem; color: #cbd5e1; font-weight: 600;">Screencast: Admin Control Panel Mastery</span>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span id="liveStatusPill" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.72rem; color: var(--adm-emerald-light); background: rgba(16, 185, 129, 0.15); padding: 2px 8px; border-radius: 12px;">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: var(--adm-emerald); box-shadow: 0 0 6px var(--adm-emerald);"></span>
                    <span id="liveStatusText">Ready to Play</span>
                </span>
            </div>
        </div>

        <?php if (!empty($customVideoUrl)): ?>
            <!-- External Video Embed -->
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; background: #000;">
                <?php
                $embedSrc = $customVideoUrl;
                if (strpos($customVideoUrl, 'youtube.com/watch') !== false) {
                    parse_str(parse_url($customVideoUrl, PHP_URL_QUERY), $ytParams);
                    $embedSrc = 'https://www.youtube.com/embed/' . ($ytParams['v'] ?? '');
                } elseif (strpos($customVideoUrl, 'youtu.be/') !== false) {
                    $embedSrc = 'https://www.youtube.com/embed/' . basename(parse_url($customVideoUrl, PHP_URL_PATH));
                } elseif (strpos($customVideoUrl, 'loom.com/share/') !== false) {
                    $embedSrc = 'https://www.loom.com/embed/' . basename(parse_url($customVideoUrl, PHP_URL_PATH));
                }
                ?>
                <iframe src="<?= clean($embedSrc) ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen style="position: absolute; top:0; left: 0; width: 100%; height: 100%;"></iframe>
            </div>
        <?php else: ?>
            <!-- Interactive Screencast Simulation Screen -->
            <div id="screencastStage" style="position: relative; width: 100%; aspect-ratio: 16/9; background: #0b0f19; overflow: hidden; display: flex; flex-direction: column;">
                
                <!-- Simulated Dynamic Screen Content -->
                <div id="simScreen" style="flex: 1; padding: 24px; display: flex; flex-direction: column; justify-content: center; position: relative;">
                    <!-- Content will be injected dynamically via JS based on current second -->
                </div>

                <!-- Animated Mouse Cursor Pointer -->
                <div id="simCursor" style="position: absolute; top: 50%; left: 50%; width: 22px; height: 22px; pointer-events: none; z-index: 30; transition: top 0.6s cubic-bezier(0.2, 0.8, 0.2, 1), left 0.6s cubic-bezier(0.2, 0.8, 0.2, 1); filter: drop-shadow(0 2px 5px rgba(0,0,0,0.8));">
                    <svg viewBox="0 0 24 24" fill="#fff" stroke="#000" stroke-width="1.5"><path d="M3 3l7 18 3-7 7-3L3 3z"/></svg>
                    <div id="clickRipple" style="position: absolute; top: 0; left: 0; width: 20px; height: 20px; border-radius: 50%; border: 2px solid var(--adm-emerald); opacity: 0; transform: scale(0.5); pointer-events: none;"></div>
                </div>

                <!-- Subtitles / Narration Caption Bar -->
                <div id="captionBar" style="background: rgba(10, 14, 23, 0.85); backdrop-filter: blur(8px); padding: 10px 18px; border-top: 1px solid rgba(255,255,255,0.08); font-size: 0.85rem; color: #f1f5f9; display: flex; align-items: center; gap: 10px; min-height: 42px;">
                    <span style="color: var(--adm-cyan); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Narration</span>
                    <span id="captionText" style="flex: 1; line-height: 1.4;">Click Play to start the animated video screencast guide of the Admin Panel.</span>
                </div>
            </div>

            <!-- Video Controls Bar -->
            <div style="background: #111827; padding: 12px 18px; display: flex; flex-direction: column; gap: 10px; border-top: 1px solid var(--adm-border);">
                <!-- Scrubber Progress Bar -->
                <div id="progressBarContainer" style="width: 100%; height: 6px; background: rgba(255,255,255,0.15); border-radius: 3px; cursor: pointer; position: relative; transition: height 0.15s ease;">
                    <div id="progressBar" style="height: 100%; width: 0%; background: var(--adm-emerald); border-radius: 3px; position: relative;">
                        <span id="scrubHandle" style="position: absolute; right: -5px; top: -3px; width: 12px; height: 12px; border-radius: 50%; background: #fff; box-shadow: 0 0 6px var(--adm-emerald);"></span>
                    </div>
                </div>

                <!-- Controls Buttons Strip -->
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <button id="playBtn" class="adm-btn adm-btn-primary adm-btn-sm" style="width: 36px; height: 36px; padding: 0; border-radius: 50%; display: grid; place-items: center;">
                            <svg id="playIcon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 4 20 12 6 20 6 4"/></svg>
                        </button>
                        <button id="restartBtn" class="adm-btn adm-btn-secondary adm-btn-sm" title="Restart from beginning">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 4v6h6M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                        </button>
                        <span id="timeDisplay" style="font-family: monospace; font-size: 0.85rem; color: #94a3b8;">00:00 / 04:30</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 4px; font-size: 0.78rem; color: var(--adm-text-secondary);">
                            <span>Speed:</span>
                            <select id="speedSelect" style="background: rgba(255,255,255,0.08); border: 1px solid var(--adm-border); color: #fff; border-radius: 4px; padding: 2px 6px; font-size: 0.78rem;">
                                <option value="1">1.0x</option>
                                <option value="1.5">1.5x</option>
                                <option value="2">2.0x</option>
                            </select>
                        </div>
                        <button id="soundToggleBtn" class="adm-btn adm-btn-secondary adm-btn-sm" title="Audio voiceover synth">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>
                            <span id="soundLabel">Sound On</span>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Video Chapters & Interactive Playlist -->
    <div class="adm-card" style="padding: 20px; display: flex; flex-direction: column;">
        <h3 style="color: #fff; font-size: 1rem; margin-bottom: 4px; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            <span>Video Chapters</span>
        </h3>
        <p style="color: var(--adm-text-muted); font-size: 0.78rem; margin-bottom: 16px;">Click any chapter to jump directly to that section.</p>

        <div id="chaptersList" style="display: flex; flex-direction: column; gap: 8px; flex: 1; overflow-y: auto; max-height: 400px;">
            <!-- Chapter Items -->
            <button class="chapter-item active" data-time="0">
                <span class="chap-badge">01</span>
                <div class="chap-info">
                    <strong>Setup Wizard & Secure Login</strong>
                    <small>0:00 - Initial password, bcrypt & lockout</small>
                </div>
                <span class="chap-time">0:00</span>
            </button>

            <button class="chapter-item" data-time="45">
                <span class="chap-badge">02</span>
                <div class="chap-info">
                    <strong>Dashboard KPIs & Navigation</strong>
                    <small>0:45 - Metrics, quick actions & layout</small>
                </div>
                <span class="chap-time">0:45</span>
            </button>

            <button class="chapter-item" data-time="90">
                <span class="chap-badge">03</span>
                <div class="chap-info">
                    <strong>Managing Website Pages</strong>
                    <small>1:30 - Creating, editing & publishing</small>
                </div>
                <span class="chap-time">1:30</span>
            </button>

            <button class="chapter-item" data-time="150">
                <span class="chap-badge">04</span>
                <div class="chap-info">
                    <strong>Contact Messages & Quick Reply</strong>
                    <small>2:30 - Inbox, 1-click email replies</small>
                </div>
                <span class="chap-time">2:30</span>
            </button>

            <button class="chapter-item" data-time="195">
                <span class="chap-badge">05</span>
                <div class="chap-info">
                    <strong>Configuring Social Channels</strong>
                    <small>3:15 - YouTube, WhatsApp & verified icons</small>
                </div>
                <span class="chap-time">3:15</span>
            </button>

            <button class="chapter-item" data-time="235">
                <span class="chap-badge">06</span>
                <div class="chap-info">
                    <strong>Site Settings & Security Ticker</strong>
                    <small>3:55 - Hero headlines, ticker & contact email</small>
                </div>
                <span class="chap-time">3:55</span>
            </button>

            <button class="chapter-item" data-time="260">
                <span class="chap-badge">07</span>
                <div class="chap-info">
                    <strong>Administrator Password Security</strong>
                    <small>4:20 - Profile updates & logout</small>
                </div>
                <span class="chap-time">4:20</span>
            </button>
        </div>
    </div>
</div>

<!-- Comprehensive Written Screencasting & Operating Manual -->
<div id="manual-docs" class="adm-card" style="padding: 30px; margin-bottom: 30px;">
    <div style="border-bottom: 1px solid var(--adm-border); padding-bottom: 20px; margin-bottom: 25px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
        <div>
            <span class="adm-badge published" style="margin-bottom: 8px;">Documentation Manual</span>
            <h2 style="color: #fff; font-size: 1.3rem;">Administrator Complete Operating Guide</h2>
            <p style="color: var(--adm-text-secondary); font-size: 0.85rem;">Reference manual detailing every capability, setting, and security rule in the Anjum Iqbal admin portal.</p>
        </div>
        <button onclick="window.print()" class="adm-btn adm-btn-secondary adm-btn-sm">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            <span>Print / Save as PDF</span>
        </button>
    </div>

    <!-- Manual Sections Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
        
        <!-- Section 1 -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.15); color: var(--adm-emerald-light); display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">01</span>
                <h3 style="color: #fff; font-size: 1.05rem;">Dashboard & Key Metrics</h3>
            </div>
            <p style="font-size: 0.85rem; color: var(--adm-text-secondary); line-height: 1.6; margin-bottom: 12px;">
                The dashboard (<a href="index.php" style="color: var(--adm-cyan);">/admin/index.php</a>) is your central flight deck:
            </p>
            <ul style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.7; padding-left: 20px;">
                <li><strong>KPI Counters:</strong> Real-time counts for published pages, total inquiry submissions, and unread messages.</li>
                <li><strong>Quick Actions:</strong> Jump straight to creating a page, viewing the inbox, or tweaking site settings in 1 click.</li>
                <li><strong>Recent Submissions:</strong> View the last 5 messages submitted by website visitors with quick status pills.</li>
            </ul>
        </div>

        <!-- Section 2 -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(6, 182, 212, 0.15); color: var(--adm-cyan); display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">02</span>
                <h3 style="color: #fff; font-size: 1.05rem;">Pages & Content Management</h3>
            </div>
            <p style="font-size: 0.85rem; color: var(--adm-text-secondary); line-height: 1.6; margin-bottom: 12px;">
                Add or modify pages (<a href="pages.php" style="color: var(--adm-cyan);">/admin/pages.php</a>) without writing code:
            </p>
            <ul style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.7; padding-left: 20px;">
                <li><strong>Formatting Helper Toolbar:</strong> Click toolbar buttons to insert headings, bullet points, two-column cards, or danger security alert boxes automatically.</li>
                <li><strong>URL Slugs:</strong> Slugs are automatically generated (e.g., <code>free-tools</code> renders at <code>page.php?slug=free-tools</code>).</li>
                <li><strong>Nav & Footer Visibility:</strong> Toggle checkboxes to specify whether a page appears in the top navigation bar, in the footer links, or both.</li>
                <li><strong>Publishing Status:</strong> Set status to <em>Published</em> or <em>Draft</em> to preview before going live.</li>
            </ul>
        </div>

        <!-- Section 3 -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.15); color: var(--adm-amber); display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">03</span>
                <h3 style="color: #fff; font-size: 1.05rem;">Contact Inquiries & Inbox</h3>
            </div>
            <p style="font-size: 0.85rem; color: var(--adm-text-secondary); line-height: 1.6; margin-bottom: 12px;">
                Manage incoming visitor inquiries (<a href="messages.php" style="color: var(--adm-cyan);">/admin/messages.php</a>):
            </p>
            <ul style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.7; padding-left: 20px;">
                <li><strong>Filters & Search:</strong> Quickly toggle between <em>All</em>, <em>Unread</em>, and <em>Read</em> messages, or search by sender name or keyword.</li>
                <li><strong>Detailed Inspector:</strong> View message sender, email, submission IP address, and date.</li>
                <li><strong>1-Click Reply:</strong> Click <em>Reply via Email</em> to immediately open your mail app with sender email and subject pre-filled.</li>
                <li><strong>Clean Up:</strong> Mark as read/unread or permanently delete processed messages.</li>
            </ul>
        </div>

        <!-- Section 4 -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(244, 63, 94, 0.15); color: #fb7185; display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">04</span>
                <h3 style="color: #fff; font-size: 1.05rem;">Social Media Links</h3>
            </div>
            <p style="font-size: 0.85rem; color: var(--adm-text-secondary); line-height: 1.6; margin-bottom: 12px;">
                Maintain official public handles (<a href="socials.php" style="color: var(--adm-cyan);">/admin/socials.php</a>):
            </p>
            <ul style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.7; padding-left: 20px;">
                <li><strong>Supported Platforms:</strong> YouTube, WhatsApp Channel, TikTok, Facebook, Instagram, LinkedIn, X/Twitter, Telegram, and Custom Websites.</li>
                <li><strong>Sort Order:</strong> Set numbers (1, 2, 3...) to control the exact left-to-right sequence of icons in the header and footer strips.</li>
                <li><strong>Instant Reflection:</strong> Any link update or deactivation immediately applies across the live website.</li>
            </ul>
        </div>

        <!-- Section 5 -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(168, 85, 247, 0.15); color: #c084fc; display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">05</span>
                <h3 style="color: #fff; font-size: 1.05rem;">Global Settings & Ticker</h3>
            </div>
            <p style="font-size: 0.85rem; color: var(--adm-text-secondary); line-height: 1.6; margin-bottom: 12px;">
                Configure global brand elements (<a href="settings.php" style="color: var(--adm-cyan);">/admin/settings.php</a>):
            </p>
            <ul style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.7; padding-left: 20px;">
                <li><strong>Security Alert Ticker:</strong> Enable/disable the top banner notice warning against scammers, and customize its text.</li>
                <li><strong>Hero Headline & Bio:</strong> Change the homepage title, subtitle, verified badge text, and About section bio.</li>
                <li><strong>Contact Support Email:</strong> Configure the official support inbox (currently <code>contact@anjumiqbal.com</code>).</li>
            </ul>
        </div>

        <!-- Section 6 -->
        <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--adm-border); border-radius: var(--radius-md); padding: 22px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(59, 130, 246, 0.15); color: #60a5fa; display: grid; place-items: center; font-weight: 700; font-size: 0.85rem;">06</span>
                <h3 style="color: #fff; font-size: 1.05rem;">Security & Best Practices</h3>
            </div>
            <p style="font-size: 0.85rem; color: var(--adm-text-secondary); line-height: 1.6; margin-bottom: 12px;">
                Essential security measures to protect your portal:
            </p>
            <ul style="font-size: 0.82rem; color: #cbd5e1; line-height: 1.7; padding-left: 20px;">
                <li><strong>Bcrypt Password Hashing:</strong> Passwords are protected using bcrypt cost 12.</li>
                <li><strong>Brute-Force Rate Limiting:</strong> 5 consecutive failed login attempts trigger an automated 10-minute IP/session lockout.</li>
                <li><strong>CSRF Tokens:</strong> Every POST request is protected with cryptographic session tokens.</li>
                <li><strong>Always Logout:</strong> Always click <em>Logout</em> when managing the portal from shared or public computers.</li>
            </ul>
        </div>
    </div>
</div>

<!-- Embedded Interactive Screencast Simulation Script & Styles -->
<style>
.chapter-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid var(--adm-border);
    border-radius: var(--radius-sm);
    color: var(--adm-text-secondary);
    text-align: left;
    cursor: pointer;
    transition: var(--transition);
    width: 100%;
}

.chapter-item:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
    border-color: rgba(255, 255, 255, 0.15);
}

.chapter-item.active {
    background: rgba(16, 185, 129, 0.12);
    border-color: var(--adm-emerald);
    color: #fff;
}

.chapter-item.active .chap-badge {
    background: var(--adm-emerald);
    color: #0a0e17;
    font-weight: 800;
}

.chap-badge {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.08);
    color: #cbd5e1;
    display: grid;
    place-items: center;
    font-size: 0.72rem;
    font-weight: 700;
    flex-shrink: 0;
}

.chap-info {
    flex: 1;
    min-width: 0;
}

.chap-info strong {
    display: block;
    font-size: 0.82rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chap-info small {
    display: block;
    font-size: 0.7rem;
    color: var(--adm-text-muted);
}

.chap-time {
    font-size: 0.72rem;
    font-family: monospace;
    color: var(--adm-emerald-light);
    flex-shrink: 0;
}

@media (max-width: 900px) {
    #playerContainer {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Toggle custom video URL configuration box
    const toggleConfigBtn = document.getElementById('toggleEmbedConfigBtn');
    const embedConfigBox = document.getElementById('embedConfigBox');
    if (toggleConfigBtn && embedConfigBox) {
        toggleConfigBtn.addEventListener('click', () => {
            const isHidden = embedConfigBox.style.display === 'none';
            embedConfigBox.style.display = isHidden ? 'block' : 'none';
            if (isHidden) {
                embedConfigBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    }

    // 2. Interactive Screencast Video Simulator
    const simScreen = document.getElementById('simScreen');
    if (!simScreen) return; // If external video is active, simulator elements are omitted

    const playBtn = document.getElementById('playBtn');
    const playIcon = document.getElementById('playIcon');
    const restartBtn = document.getElementById('restartBtn');
    const progressBar = document.getElementById('progressBar');
    const progressBarContainer = document.getElementById('progressBarContainer');
    const timeDisplay = document.getElementById('timeDisplay');
    const speedSelect = document.getElementById('speedSelect');
    const soundToggleBtn = document.getElementById('soundToggleBtn');
    const soundLabel = document.getElementById('soundLabel');
    const simCursor = document.getElementById('simCursor');
    const clickRipple = document.getElementById('clickRipple');
    const captionText = document.getElementById('captionText');
    const liveStatusText = document.getElementById('liveStatusText');
    const liveStatusPill = document.getElementById('liveStatusPill');
    const chapterButtons = document.querySelectorAll('.chapter-item');

    const TOTAL_DURATION = 270; // 4 minutes 30 seconds
    let currentTime = 0;
    let isPlaying = false;
    let playbackSpeed = 1.0;
    let isSoundOn = true;
    let playInterval = null;

    // Screencast timeline scenes
    const timelineData = [
        {
            start: 0,
            end: 45,
            title: "Chapter 1: First-Time Setup Wizard & Secure Login",
            caption: "When launching the portal for the first time, setup.php automatically guides you to establish your private administrator username, email, and strong password.",
            cursor: { top: "55%", left: "48%" },
            render: () => `
                <div style="background: #111827; border: 1px solid var(--adm-border); border-radius: 12px; padding: 22px; max-width: 440px; margin: 0 auto; box-shadow: 0 15px 35px rgba(0,0,0,0.5);">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                        <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700;">STEP 01</span>
                        <h4 style="color: #fff; margin: 0; font-size: 1rem;">Administrator Security Setup Wizard</h4>
                    </div>
                    <div style="font-size: 0.78rem; color: #94a3b8; margin-bottom: 14px;">Credentials are hashed with <strong>bcrypt (cost 12)</strong>. Once master account is established, setup is permanently locked.</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <input type="text" value="admin_master" readonly style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 6px 10px; border-radius: 6px; font-size: 0.8rem;">
                        <input type="password" value="••••••••••••" readonly style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; padding: 6px 10px; border-radius: 6px; font-size: 0.8rem;">
                        <div style="background: #10b981; color: #0a0e17; text-align: center; padding: 8px; border-radius: 6px; font-weight: 700; font-size: 0.82rem; margin-top: 4px;">✓ Create Administrator Account</div>
                    </div>
                </div>
            `
        },
        {
            start: 45,
            end: 90,
            title: "Chapter 2: Dashboard KPIs & Navigation Overview",
            caption: "The Dashboard overview displays live KPI tiles showing total pages, unread visitor contact inquiries, and active social channel badges.",
            cursor: { top: "35%", left: "55%" },
            render: () => `
                <div style="display: flex; flex-direction: column; gap: 14px; width: 100%; max-width: 580px; margin: 0 auto;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                        <div style="background: #111827; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 10px; padding: 14px; text-align: center;">
                            <span style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Total Pages</span>
                            <div style="font-size: 1.6rem; color: #fff; font-weight: 800; margin: 4px 0;">5</div>
                            <span style="font-size: 0.7rem; color: #10b981;">Published</span>
                        </div>
                        <div style="background: #111827; border: 1px solid rgba(244, 63, 94, 0.4); border-radius: 10px; padding: 14px; text-align: center;">
                            <span style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Inquiries</span>
                            <div style="font-size: 1.6rem; color: #fb7185; font-weight: 800; margin: 4px 0;">3</div>
                            <span style="font-size: 0.7rem; color: #fb7185;">Unread Messages</span>
                        </div>
                        <div style="background: #111827; border: 1px solid rgba(6, 182, 212, 0.4); border-radius: 10px; padding: 14px; text-align: center;">
                            <span style="font-size: 0.72rem; color: #94a3b8; text-transform: uppercase;">Socials</span>
                            <div style="font-size: 1.6rem; color: #06b6d4; font-weight: 800; margin: 4px 0;">6</div>
                            <span style="font-size: 0.7rem; color: #06b6d4;">Active Channels</span>
                        </div>
                    </div>
                    <div style="background: #111827; border: 1px solid var(--adm-border); border-radius: 10px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.85rem; color: #fff; font-weight: 600;">⚡ Quick Shortcuts:</span>
                        <div style="display: flex; gap: 8px;">
                            <span style="background: #10b981; color: #000; font-size: 0.72rem; padding: 4px 8px; border-radius: 4px; font-weight: 700;">+ New Page</span>
                            <span style="background: rgba(255,255,255,0.08); color: #fff; font-size: 0.72rem; padding: 4px 8px; border-radius: 4px;">Inbox</span>
                        </div>
                    </div>
                </div>
            `
        },
        {
            start: 90,
            end: 150,
            title: "Chapter 3: Managing Website Pages & Rich Formatting",
            caption: "Easily edit or publish pages with the rich formatting toolbar. Add danger alert boxes, two-column cards, and configure top navigation or footer placement.",
            cursor: { top: "42%", left: "62%" },
            render: () => `
                <div style="background: #111827; border: 1px solid var(--adm-border); border-radius: 12px; padding: 20px; max-width: 580px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <strong style="color: #fff; font-size: 0.95rem;">Editing: Scam Alert & Safety Guide</strong>
                        <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px;">Published</span>
                    </div>
                    <div style="display: flex; gap: 6px; margin-bottom: 10px; flex-wrap: wrap;">
                        <span style="background: rgba(255,255,255,0.08); font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; color: #cbd5e1;">H3 Heading</span>
                        <span style="background: rgba(255,255,255,0.08); font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; color: #cbd5e1;">• Bullet List</span>
                        <span style="background: rgba(244, 63, 94, 0.2); color: #fb7185; font-size: 0.7rem; padding: 3px 8px; border-radius: 4px; font-weight: 600;">+ Danger Alert Box</span>
                        <span style="background: rgba(6, 182, 212, 0.2); color: #06b6d4; font-size: 0.7rem; padding: 3px 8px; border-radius: 4px;">2-Col Cards</span>
                    </div>
                    <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.08); padding: 12px; border-radius: 6px; font-family: monospace; font-size: 0.75rem; color: #94a3b8; line-height: 1.5;">
                        &lt;div class="alert-box danger"&gt;<br>
                        &nbsp;&nbsp;&lt;h4&gt;⚠️ Essential Verification Rules&lt;/h4&gt;<br>
                        &nbsp;&nbsp;&lt;p&gt;Anjum Iqbal never charges any registration fees.&lt;/p&gt;<br>
                        &lt;/div&gt;
                    </div>
                </div>
            `
        },
        {
            start: 150,
            end: 195,
            title: "Chapter 4: Contact Inquiries Inbox & 1-Click Email Reply",
            caption: "Inquiries submitted via the homepage contact form are stored safely in SQLite with sender name, email, IP address, and 1-click mailto: reply buttons.",
            cursor: { top: "48%", left: "70%" },
            render: () => `
                <div style="background: #111827; border: 1px solid var(--adm-border); border-radius: 12px; padding: 18px; max-width: 580px; margin: 0 auto;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <strong style="color: #fff; font-size: 0.95rem;">Contact Messages Inbox (3)</strong>
                        <span style="background: rgba(255,255,255,0.08); color: #fff; font-size: 0.72rem; padding: 2px 8px; border-radius: 4px;">Filter: All</span>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="color: #fff; font-size: 0.85rem;">Muhammad Usman</strong>
                                <div style="font-size: 0.72rem; color: #94a3b8;">usman@example.com · Reported fake telegram group</div>
                            </div>
                            <span style="background: #10b981; color: #000; font-size: 0.72rem; font-weight: 700; padding: 4px 8px; border-radius: 4px;">✉ Reply via Email</span>
                        </div>
                        <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255,255,255,0.08); border-radius: 8px; padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong style="color: #cbd5e1; font-size: 0.85rem;">Ayesha Khan</strong>
                                <div style="font-size: 0.72rem; color: #64748b;">ayesha@example.com · Inquiry about Level 04 AI curriculum</div>
                            </div>
                            <span style="background: rgba(255,255,255,0.08); color: #cbd5e1; font-size: 0.72rem; padding: 4px 8px; border-radius: 4px;">View Message</span>
                        </div>
                    </div>
                </div>
            `
        },
        {
            start: 195,
            end: 235,
            title: "Chapter 5: Configuring Social Media Links",
            caption: "Control official social channels across the header and footer. Set custom display order numbers and toggle channels on or off instantly.",
            cursor: { top: "52%", left: "45%" },
            render: () => `
                <div style="background: #111827; border: 1px solid var(--adm-border); border-radius: 12px; padding: 18px; max-width: 580px; margin: 0 auto;">
                    <strong style="color: #fff; font-size: 0.95rem; margin-bottom: 12px; display: block;">Official Verified Social Channels</strong>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px;">
                            <span style="color: #ff0033; font-weight: 700; font-size: 0.85rem;">▶ YouTube (Primary)</span>
                            <span style="font-size: 0.75rem; color: #94a3b8;">Order: 1 · Status: <strong style="color:#10b981;">Active</strong></span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px;">
                            <span style="color: #25d366; font-weight: 700; font-size: 0.85rem;">💬 WhatsApp Community</span>
                            <span style="font-size: 0.75rem; color: #94a3b8;">Order: 3 · Status: <strong style="color:#10b981;">Active</strong></span>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.03); padding: 8px 12px; border-radius: 6px;">
                            <span style="color: #00f2fe; font-weight: 700; font-size: 0.85rem;">🎵 TikTok (@anjumiqbal9999)</span>
                            <span style="font-size: 0.75rem; color: #94a3b8;">Order: 4 · Status: <strong style="color:#10b981;">Active</strong></span>
                        </div>
                    </div>
                </div>
            `
        },
        {
            start: 235,
            end: 260,
            title: "Chapter 6: Global Settings & Security Ticker Banner",
            caption: "Customize the homepage hero headline, verified educator badge text, security announcement ticker notice, and the public contact email.",
            cursor: { top: "38%", left: "50%" },
            render: () => `
                <div style="background: #111827; border: 1px solid var(--adm-border); border-radius: 12px; padding: 18px; max-width: 580px; margin: 0 auto;">
                    <strong style="color: #fff; font-size: 0.95rem; margin-bottom: 12px; display: block;">Global Site Configuration</strong>
                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.8rem;">
                        <div>
                            <span style="color: #94a3b8;">Public Support Email:</span>
                            <div style="background: rgba(255,255,255,0.05); color: #fff; padding: 6px 10px; border-radius: 6px; font-weight: 600;">contact@anjumiqbal.com</div>
                        </div>
                        <div>
                            <span style="color: #94a3b8;">Security Ticker Banner Notice:</span>
                            <div style="background: rgba(244,63,94,0.1); color: #fda4af; padding: 6px 10px; border-radius: 6px; font-size: 0.75rem;">⚠️ Official Security Notice: Anjum Iqbal never charges any registration fees...</div>
                        </div>
                    </div>
                </div>
            `
        },
        {
            start: 260,
            end: 270,
            title: "Chapter 7: Security Best Practices & Password Hashing",
            caption: "Update your admin username and change your password anytime under Profile. Always logout when done to safeguard the educational portal.",
            cursor: { top: "60%", left: "55%" },
            render: () => `
                <div style="background: #111827; border: 1px solid rgba(16, 185, 129, 0.4); border-radius: 12px; padding: 22px; max-width: 480px; margin: 0 auto; text-align: center;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); color: #10b981; display: grid; place-items: center; margin: 0 auto 12px;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h4 style="color: #fff; margin: 0 0 6px 0; font-size: 1.1rem;">Portal Fully Protected</h4>
                    <p style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 12px;">Bcrypt password encryption · Anti-CSRF protection · Brute-force lockout active</p>
                    <span style="background: #10b981; color: #0a0e17; font-weight: 700; font-size: 0.8rem; padding: 6px 14px; border-radius: 20px;">Ready for Production</span>
                </div>
            `
        }
    ];

    // Format seconds into MM:SS
    function formatTime(secs) {
        const m = Math.floor(secs / 60);
        const s = Math.floor(secs % 60);
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    // Voice synthesis simulation
    function speakCaption(text) {
        if (!isSoundOn || !('speechSynthesis' in window)) return;
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.rate = playbackSpeed;
        utter.pitch = 1.0;
        window.speechSynthesis.speak(utter);
    }

    let lastRenderedScene = -1;

    // Render frame based on time
    function updateSimulation(time) {
        const sceneIndex = timelineData.findIndex(s => time >= s.start && time < s.end);
        const activeScene = timelineData[sceneIndex >= 0 ? sceneIndex : timelineData.length - 1];

        // Update progress bar
        const pct = (time / TOTAL_DURATION) * 100;
        progressBar.style.width = `${pct}%`;
        timeDisplay.textContent = `${formatTime(time)} / ${formatTime(TOTAL_DURATION)}`;

        if (activeScene && sceneIndex !== lastRenderedScene) {
            lastRenderedScene = sceneIndex;
            simScreen.innerHTML = activeScene.render();
            captionText.textContent = activeScene.caption;
            document.getElementById('playerTitle').textContent = `Screencast: ${activeScene.title}`;

            // Trigger ripple effect and move cursor
            if (activeScene.cursor) {
                simCursor.style.top = activeScene.cursor.top;
                simCursor.style.left = activeScene.cursor.left;
                clickRipple.style.animation = 'rippleAnim 0.6s ease-out';
                setTimeout(() => { clickRipple.style.animation = ''; }, 600);
            }

            // Highlight active chapter in sidebar playlist
            chapterButtons.forEach((btn, idx) => {
                if (idx === sceneIndex) {
                    btn.classList.add('active');
                    btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                } else {
                    btn.classList.remove('active');
                }
            });

            if (isPlaying) {
                speakCaption(activeScene.caption);
            }
        }
    }

    // Playback loop
    function startPlayback() {
        if (isPlaying) return;
        isPlaying = true;
        playIcon.innerHTML = '<rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>';
        liveStatusText.textContent = 'Playing Walkthrough';
        liveStatusPill.style.color = '#10b981';

        const stepMs = 1000 / playbackSpeed;
        playInterval = setInterval(() => {
            currentTime += 1;
            if (currentTime >= TOTAL_DURATION) {
                pausePlayback();
                currentTime = TOTAL_DURATION;
                liveStatusText.textContent = 'Completed';
            }
            updateSimulation(currentTime);
        }, stepMs);

        // Speak current caption immediately
        const sceneIndex = timelineData.findIndex(s => currentTime >= s.start && currentTime < s.end);
        if (sceneIndex >= 0) speakCaption(timelineData[sceneIndex].caption);
    }

    function pausePlayback() {
        isPlaying = false;
        clearInterval(playInterval);
        playIcon.innerHTML = '<polygon points="6 4 20 12 6 20 6 4"/>';
        liveStatusText.textContent = 'Paused';
        if ('speechSynthesis' in window) window.speechSynthesis.cancel();
    }

    playBtn.addEventListener('click', () => {
        if (isPlaying) pausePlayback();
        else {
            if (currentTime >= TOTAL_DURATION) currentTime = 0;
            startPlayback();
        }
    });

    restartBtn.addEventListener('click', () => {
        pausePlayback();
        currentTime = 0;
        lastRenderedScene = -1;
        updateSimulation(0);
        startPlayback();
    });

    // Scrubber click
    progressBarContainer.addEventListener('click', (e) => {
        const rect = progressBarContainer.getBoundingClientRect();
        const clickX = e.clientX - rect.left;
        const pct = Math.max(0, Math.min(1, clickX / rect.width));
        currentTime = Math.floor(pct * TOTAL_DURATION);
        lastRenderedScene = -1;
        updateSimulation(currentTime);
    });

    // Chapter button click
    chapterButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const targetTime = parseInt(btn.dataset.time, 10);
            currentTime = targetTime;
            lastRenderedScene = -1;
            updateSimulation(currentTime);
            if (!isPlaying) startPlayback();
        });
    });

    // Playback Speed
    speedSelect.addEventListener('change', () => {
        playbackSpeed = parseFloat(speedSelect.value);
        if (isPlaying) {
            pausePlayback();
            startPlayback();
        }
    });

    // Sound toggle
    soundToggleBtn.addEventListener('click', () => {
        isSoundOn = !isSoundOn;
        soundLabel.textContent = isSoundOn ? 'Sound On' : 'Muted';
        if (!isSoundOn && 'speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
    });

    // Initial first-frame render
    updateSimulation(0);
});
</script>

<style>
@keyframes rippleAnim {
    0% { transform: scale(0.4); opacity: 1; }
    100% { transform: scale(2.2); opacity: 0; }
}
</style>

<?php include __DIR__ . '/admin_footer.php'; ?>
