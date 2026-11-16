<?php
// admin/profile.php - Admin Security & Profile
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Security & Profile';
$headerTitle = 'Admin Profile & Security';

$error = '';
$stmt = $db->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$adminId]);
$admin = $stmt->fetch();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username)) {
            $error = 'Admin username cannot be empty.';
        } else {
            // Check username uniqueness
            $check = $db->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
            $check->execute([$username, $adminId]);
            if ($check->fetch()) {
                $error = 'Username already taken by another account.';
            } else {
                // If password change is requested
                if (!empty($newPassword)) {
                    if (empty($currentPassword)) {
                        $error = 'Please enter your current password to authorize a password change.';
                    } elseif (!password_verify($currentPassword, $admin['password'])) {
                        $error = 'Current password is incorrect.';
                    } elseif (strlen($newPassword) < 6) {
                        $error = 'New password must be at least 6 characters long.';
                    } elseif ($newPassword !== $confirmPassword) {
                        $error = 'New password and confirmation do not match.';
                    } else {
                        // Hash new password and update
                        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
                        $upd = $db->prepare("UPDATE admins SET username = ?, email = ?, password = ? WHERE id = ?");
                        $upd->execute([$username, $email, $newHash, $adminId]);

                        $_SESSION['admin_username'] = $username;
                        set_flash('success', 'Profile and password updated successfully!');
                        header("Location: profile.php");
                        exit;
                    }
                } else {
                    // Update username & email without password change
                    $upd = $db->prepare("UPDATE admins SET username = ?, email = ? WHERE id = ?");
                    $upd->execute([$username, $email, $adminId]);

                    $_SESSION['admin_username'] = $username;
                    set_flash('success', 'Profile updated successfully!');
                    header("Location: profile.php");
                    exit;
                }
            }
        }
    }
}

include __DIR__ . '/admin_header.php';
?>

<div class="adm-card" style="max-width: 650px;">
    <div class="adm-card-header">
        <div>
            <h3>Security & Admin Account</h3>
            <p style="font-size: 0.85rem; color: var(--adm-text-muted); margin-top: 4px;">Update admin credentials and secure your account.</p>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="adm-alert danger">
            <span><?= clean($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="profile.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="adm-form-group">
            <label for="username">Admin Username *</label>
            <input type="text" id="username" name="username" class="adm-input" value="<?= clean($admin['username']) ?>" required>
        </div>

        <div class="adm-form-group">
            <label for="email">Admin Notification Email</label>
            <input type="email" id="email" name="email" class="adm-input" value="<?= clean($admin['email']) ?>" placeholder="admin@example.com">
        </div>

        <div style="border-top: 1px solid var(--adm-border); margin: 30px 0 20px; padding-top: 20px;">
            <h4 style="color: #fff; font-size: 0.95rem; margin-bottom: 6px;">Change Password</h4>
            <p style="font-size: 0.8rem; color: var(--adm-text-muted); margin-bottom: 18px;">Leave blank if you do not wish to change your password.</p>

            <div class="adm-form-group">
                <label for="current_password">Current Password (Required for password change)</label>
                <input type="password" id="current_password" name="current_password" class="adm-input" placeholder="••••••••">
            </div>

            <div class="form-row">
                <div class="adm-form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="adm-input" placeholder="••••••••">
                </div>

                <div class="adm-form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="adm-input" placeholder="••••••••">
                </div>
            </div>
        </div>

        <button type="submit" class="adm-btn adm-btn-primary" style="padding: 12px 28px;">
            <span>Save Profile & Security</span>
        </button>
    </form>
</div>

<?php include __DIR__ . '/admin_footer.php'; ?>
