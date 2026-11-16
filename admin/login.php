<?php
// admin/login.php - Admin Login Interface
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            // Find admin in database
            $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Successful login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                // Update last login
                $update = $db->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $update->execute([$admin['id']]);

                set_flash('success', 'Welcome back, ' . htmlspecialchars($admin['username']) . '!');
                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid username or password.';
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
            <p>Anjum Iqbal Management System</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="adm-alert danger" style="margin-bottom: 20px;">
                <span><?= clean($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- Helper hint for default initial setup -->
        <div style="background: rgba(16, 185, 129, 0.1); border: 1px dashed rgba(16, 185, 129, 0.4); border-radius: 8px; padding: 12px 14px; margin-bottom: 20px; font-size: 0.82rem; color: #a7f3d0;">
            <strong>Default Credentials:</strong><br>
            Username: <code style="color: #fff; background: rgba(0,0,0,0.3); padding: 2px 5px; border-radius: 4px;">admin</code> &nbsp;|&nbsp; 
            Password: <code style="color: #fff; background: rgba(0,0,0,0.3); padding: 2px 5px; border-radius: 4px;">admin123</code>
        </div>

        <form method="POST" action="login.php">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="adm-form-group">
                <label for="username">Admin Username</label>
                <input type="text" id="username" name="username" class="adm-input" placeholder="e.g. admin" required autofocus>
            </div>

            <div class="adm-form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="adm-input" placeholder="••••••••" required>
            </div>

            <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 12px;">
                <span>Sign In to Admin</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>
            </button>
        </form>

        <div style="text-align: center; margin-top: 25px; font-size: 0.82rem;">
            <a href="../index.php" style="color: var(--adm-text-muted);">&larr; Return to Public Website</a>
        </div>
    </div>
</body>
</html>
