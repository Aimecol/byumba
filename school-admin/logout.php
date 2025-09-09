<?php
/**
 * School Administration Logout
 * Diocese of Byumba - School Management System
 */

session_start();
require_once 'includes/functions.php';

// Logout the user
$schoolAuth->logout();

// Redirect to login page with success message
header('Location: index.php?message=logout');
exit;
?>
