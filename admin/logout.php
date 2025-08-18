<?php
/**
 * Admin Logout
 * Diocese of Byumba Admin Panel
 */

require_once 'functions.php';

// Log the logout activity
if (isAdminLoggedIn()) {
    logAdminActivity('logout', 'Admin logged out');
}

// Logout the admin
$adminAuth->logout();

// Redirect to login page with success message
header('Location: index.php?logout=success');
exit;
?>
