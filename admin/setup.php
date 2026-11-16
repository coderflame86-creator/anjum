<?php
// admin/setup.php - Initial Administrator Account Creation Wizard
require_once __DIR__ . '/../includes/functions.php';

// If an administrator already exists, setup is permanently locked
if (admin_exists()) {
    header("Location: login.php");
    exit;
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please fill in all required fields.';
        } elseif (strlen($username) < 4) {
            $error = 'Username must be at least 4 characters long.';
        } elseif (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $username)) {
            $error = 'Username can only contain letters, numbers, hyphens, and underscores.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long for security.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Password and confirmation do not match.';
        } else {
            // Hash password securely with bcrypt cost 12
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            $stmt = $db->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $email]);
            $adminId = $db->lastInsertId();

            // Secure login session
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $adminId;
            $_SESSION['admin_username'] = $username;

            set_flash('success', 'Administrator account created successfully! Your portal is now secured.');
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Initial Security Setup | Anjum Iqbal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" type="image/jpeg" href="../assets/images/logo.jpg">
</head>
<body class="login-body">
    <div class="login-card" style="max-width: 480px;">
        <div class="login-logo">
            <img src="../assets/images/logo.jpg" alt="Logo" onerror="this.src='../logo.jpg'">
            <h2>Security Setup Wizard</h2>
            <p style="color: var(--adm-emerald-light);">Create Your Master Admin Account</p>
        </div>

        <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-sm); padding: 14px 18px; margin-bottom: 22px; font-size: 0.85rem; color: #cbd5e1; line-height: 1.6;">
            🔒 <strong>First-Time Security Configuration:</strong><br>
            Set your own private administrator credentials. Once created, this setup wizard is permanently locked.
        </div>

        <?php if (!empty($error)): ?>
            <div class="adm-alert danger" style="margin-bottom: 20px;">
                <span><?= clean($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="setup.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="adm-form-group">
                <label for="username">Choose Admin Username *</label>
                <input type="text" id="username" name="username" class="adm-input" placeholder="e.g. administrator" value="<?= clean($_POST['username'] ?? '') ?>" required autofocus>
                <small style="color: var(--adm-text-muted); font-size: 0.75rem;">Minimum 4 characters (letters, numbers, underscores)</small>
            </div>

            <div class="adm-form-group">
                <label for="email">Admin Email (For security & inquiries)</label>
                <input type="email" id="email" name="email" class="adm-input" placeholder="e.g. you@example.com" value="<?= clean($_POST['email'] ?? '') ?>">
            </div>

            <div class="adm-form-group">
                <label for="password">Create Strong Password *</label>
                <input type="password" id="password" name="password" class="adm-input" placeholder="Minimum 8 characters" required>
                <small style="color: var(--adm-text-muted); font-size: 0.75rem;">Minimum 8 characters. Keep this confidential.</small>
            </div>

            <div class="adm-form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" class="adm-input" placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 13px;">
                <span>Complete Setup & Secure Portal</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.82rem;">
            <a href="../index.php" style="color: var(--adm-text-muted);">&larr; Return to Public Website</a>
        </div>
    </div>
</body>
</html>
