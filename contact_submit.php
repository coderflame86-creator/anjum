<?php
// contact_submit.php - Handles dynamic contact form submissions

require_once __DIR__ . '/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrf)) {
    echo json_encode(['success' => false, 'message' => 'Security token expired or invalid. Please refresh the page and try again.']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? 'General Inquiry');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields (Name, Email, Message).']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

try {
    $stmt = $db->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message, $ip]);

    echo json_encode([
        'success' => true,
        'message' => 'Thank you, ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '! Your message has been received and will be reviewed shortly.'
    ]);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'A database error occurred. Please contact via direct email.']);
    exit;
}
