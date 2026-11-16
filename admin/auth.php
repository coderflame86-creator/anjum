<?php
// admin/auth.php - Authentication verification helper

require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$adminUsername = $_SESSION['admin_username'] ?? 'Admin';
$adminId = $_SESSION['admin_id'] ?? 1;
