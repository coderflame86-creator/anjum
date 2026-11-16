# Anjum Iqbal - Dynamic Educational Portal & Admin Dashboard

A modern, high-performance web platform inspired by the structure, curriculum layout, and scam-prevention ethos of **themrhow.com**, custom redesigned with an **ultra-sleek dark palette** (Obsidian `#0a0e17`, Slate `#111827`, accented with Electric Emerald `#10b981`, Cyber Cyan `#06b6d4`, and Warm Amber `#f59e0b`).

The website is fully dynamic with a built-in lightweight SQLite database and a secured **Admin Panel** to manage pages, view & reply to contact submissions, and configure social media links in real-time.

---

## 🚀 Quick Start (Running the Website)

### Option 1: One-Click Startup (Recommended)
Simply double-click:
```
start-server.bat
```
This immediately starts the PHP built-in web server at:
- **Public Website:** [http://localhost:8000/](http://localhost:8000/)
- **Admin Control Panel:** [http://localhost:8000/admin/](http://localhost:8000/admin/)

### Option 2: Via XAMPP
You can also run this directly in your XAMPP Apache setup (`C:\xampp\htdocs\anjum`) or run via PowerShell:
```powershell
& "C:\xampp\php\php.exe" -S localhost:8000
```

---

## 🔐 Admin Portal Security & Initial Setup

To ensure strict security and eliminate open passwords:
1. When opening the admin portal for the first time at **[http://localhost:8000/admin/](http://localhost:8000/admin/)**, you will be guided to the **First-Time Security Setup Wizard** (`/admin/setup.php`).
2. Set your own private administrator username, email, and strong password (minimum 8 characters).
3. Your credentials are securely hashed using `bcrypt` (cost 12). Once your master administrator account is established, the setup wizard is permanently locked.
4. Brute-force rate limiting actively protects against dictionary attacks by enforcing a lockout after 5 consecutive failed attempts.
5. All database directories and sensitive files are blocked from direct public web access.

---

## 🎨 Design & Redesign Highlights (Inspired by themrhow.com)

1. **Distinct Dark Theme Aesthetic:**
   - Deep midnight obsidian backdrop (`#0a0e17`) with glassmorphic cards and subtle atmospheric lighting.
   - Distinct electric accents (Emerald Green & Cyber Cyan) tailored for a professional tech & education platform.
2. **Top Security Alert Ticker:**
   - Warns visitors about impersonators and links directly to the Scam Alert guide. Can be toggled on/off in Admin Settings.
3. **Hero Section:**
   - Bold typography: *"Learn Today. Lead Tomorrow."*
   - Verified Mentor Badge (`✓`).
   - Floating portrait with ambient lighting.
   - Quick counters: **100% Free**, **Rs 0 Cost**, **Verified Advice**.
4. **Official Social Channels Strip:**
   - Dynamic badges linking directly to YouTube channels, WhatsApp Community, TikTok, Facebook, and Instagram.
5. **Curriculum & Learning Tracks (Like Mr How Levels):**
   - **Level 01:** Digital Skills & Freelance Foundations
   - **Level 02:** Online Opportunities & Remote Work
   - **Level 03:** Scam Safety & Threat Modeling
   - **Level 04:** AI & Modern Digital Tools
   - **Level 05:** Financial & Digital Literacy
   - **Level 06:** Everyday Practical Wisdom
6. **Anti-Scam & Verification Shield:**
   - Dedicated prominent security section showing the 4 golden rules of avoiding digital fraud in Pakistan.
7. **Interactive Dynamic Contact Form:**
   - Sends messages directly to the database without requiring external SMTP configuration.
   - Form has CSRF protection, AJAX submission, and instant feedback.

---

## 🛠️ Dynamic Admin Panel Features

### 1. Dashboard Overview (`/admin/index.php`)
- Real-time KPI counters (Total Pages, Unread Contact Messages, Active Social Links).
- Quick action shortcuts.
- Latest 5 contact inquiries with quick status pills.
- All pages overview table.

### 2. Website Pages Management (`/admin/pages.php` & `page_edit.php`)
- **Edit Existing Pages:** Edit Core pages (*Scam Alert*, *Privacy Policy*, *Disclaimer*, *Terms and Conditions*) with full formatting.
- **Create New Pages:** Add custom dynamic pages (e.g. *Free Tools*, *Crypto Rules*, *FAQs*).
- **Navigation & Footer Controls:** Choose whether a page appears in the top navigation bar, in the footer links, or both.
- **Publish Status:** Publish or draft pages with 1 click.
- **Formatting Toolbar:** Built-in helper buttons to insert headings, bullet lists, 2-column cards, and danger alert boxes.

### 3. Contact Us Submissions & Inbox (`/admin/messages.php`)
- **Inbox:** View all inquiries submitted from the website's contact form.
- **Filters & Search:** Filter by *All*, *Unread*, or *Read*, and search by name/email/keyword.
- **Detailed View:** Inspect full message body, sender name, email, IP address, and submission timestamp.
- **Quick Reply:** One-click `mailto:` reply link opens your default email client with sender email and subject pre-filled.
- **Status & Cleanup:** Mark as read/unread or delete processed inquiries.

### 4. Social Media Links Management (`/admin/socials.php`)
- **Supported Platforms:** YouTube, WhatsApp, TikTok, Facebook, Instagram, X/Twitter, LinkedIn, Telegram, Website.
- **Management:** Add new social handles, edit URLs, toggle active status on/off, and change display order.
- Changes instantly reflect in the header, social strip, and footer across the entire site.

### 5. Global Site Settings (`/admin/settings.php`)
- Site title & tagline.
- Hero headline, subtitle, and badge text.
- Security announcement ticker (enable/disable & customize text).
- Primary YouTube & WhatsApp community channel URLs.
- Contact email and about section bio.
- Footer copyright notice.

### 6. Admin Security & Profile (`/admin/profile.php`)
- Update administrator username and notification email.
- Change admin password securely (bcrypt hashed).

---

## 📁 Project Structure

```
anjum/
├── admin/                     # Admin control panel
│   ├── auth.php               # Session security & login protection
│   ├── admin_header.php       # Shared admin sidebar & topbar
│   ├── admin_footer.php       # Shared admin footer
│   ├── index.php              # Dashboard with KPIs & recent data
│   ├── login.php              # Secure admin login
│   ├── logout.php             # Admin logout
│   ├── pages.php              # Pages list & status toggle
│   ├── page_edit.php          # Page creator & rich editor
│   ├── messages.php           # Contact inquiries inbox
│   ├── socials.php            # Social links CRUD
│   ├── settings.php           # Global site options
│   └── profile.php            # Password & username updates
├── assets/
│   ├── css/
│   │   ├── style.css          # Ultra-modern dark website styling
│   │   └── admin.css          # Sleek dark admin dashboard styling
│   ├── js/
│   │   └── main.js            # Mobile drawer, smooth scroll & async form
│   └── images/                # Logos and portraits
├── data/
│   └── database.sqlite        # Auto-created SQLite database
├── includes/
│   ├── db.php                 # SQLite connection & auto-migration
│   ├── functions.php          # Helpers, SVG icons, CSRF & settings getters
│   ├── header.php             # Public website dark header & navigation
│   └── footer.php             # Public website dark footer & social links
├── .htaccess                  # Apache routing & legacy redirect rules
├── contact_submit.php         # Async contact form handler & validator
├── index.php                  # Redesigned dynamic homepage
├── page.php                   # Universal dynamic page renderer
├── start-server.bat           # 1-click Windows server launcher
└── README.md                  # Documentation
```
