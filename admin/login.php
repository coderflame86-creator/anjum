<?php
// admin/login.php - Secure Admin Login Interface
require_once __DIR__ . '/../includes/functions.php';

// If no admin exists yet, redirect to the initial setup wizard
if (!admin_exists()) {
    header("Location: setup.php");
    exit;
}

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

// Rate limiting: max 5 failed attempts per 10 minutes
$lockoutTime = 600; // 10 minutes
$maxAttempts = 5;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Reset counter if lockout period has expired
if (time() - $_SESSION['last_attempt_time'] > $lockoutTime) {
    $_SESSION['login_attempts'] = 0;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    // Check brute-force lockout
    if ($_SESSION['login_attempts'] >= $maxAttempts) {
        $remaining = $lockoutTime - (time() - $_SESSION['last_attempt_time']);
        $minutes = ceil($remaining / 60);
        $error = "Too many failed login attempts. For security, please wait {$minutes} minute(s) before trying again.";
    } else {
        $csrf = $_POST['csrf_token'] ?? '';
        if (!verify_csrf_token($csrf)) {
            $error = 'Security session expired. Please refresh and try again.';
        } else {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Please enter both username and password.';
            } else {
                // Find admin in database
                $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    // Reset failed attempts on success
                    $_SESSION['login_attempts'] = 0;

                    // Prevent session fixation
                    session_regenerate_id(true);

                    // Successful login
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];

                    // Update last login timestamp
                    $update = $db->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                    $update->execute([$admin['id']]);

                    set_flash('success', 'Welcome back, ' . htmlspecialchars($admin['username']) . '!');
                    header("Location: index.php");
                    exit;
                } else {
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_attempt_time'] = time();
                    $remainingAttempts = $maxAttempts - $_SESSION['login_attempts'];

                    if ($remainingAttempts > 0) {
                        $error = "Invalid username or password. ({$remainingAttempts} attempts remaining)";
                    } else {
                        $error = "Too many failed attempts. Login locked for 10 minutes.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal Login | Anjum Iqbal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" type="image/jpeg" href="../assets/images/logo.jpg">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-logo">
            <img src="../assets/images/logo.jpg" alt="Anjum Iqbal Logo" onerror="this.src='../logo.jpg'">
            <h2>Admin Control Panel</h2>
            <p>Authorized Personnel Only</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="adm-alert danger" style="margin-bottom: 20px;">
                <span><?= clean($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="adm-form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" class="adm-input" placeholder="Enter username" required autofocus autocomplete="username">
            </div>

            <div class="adm-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="adm-input" placeholder="••••••••" required autocomplete="current-password">
            </div>

            <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 12px;">
                <span>Sign In Securely</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.82rem;">
            <a href="../index.php" style="color: var(--adm-text-muted);">&larr; Return to Public Website</a>
        </div>
    </div>
</body>
</html>
