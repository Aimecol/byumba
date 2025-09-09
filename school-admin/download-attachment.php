<?php
/**
 * Download Report Attachment
 * Diocese of Byumba - School Management System
 */

session_start();
require_once 'includes/functions.php';

$currentUser = $schoolAuth->getCurrentUser();
$attachmentId = intval($_GET['id'] ?? 0);

if (!$attachmentId) {
    http_response_code(404);
    die('Attachment not found.');
}

try {
    // Get attachment details with security check
    $query = "SELECT ra.*, sr.school_id 
             FROM report_attachments ra
             JOIN school_reports sr ON ra.report_id = sr.id
             WHERE ra.id = :id AND sr.school_id = :school_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $attachmentId);
    $stmt->bindValue(':school_id', $currentUser['school_id']);
    $stmt->execute();
    
    $attachment = $stmt->fetch();
    
    if (!$attachment) {
        http_response_code(404);
        die('Attachment not found or access denied.');
    }
    
    $filePath = $attachment['file_path'];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        http_response_code(404);
        die('File not found on server.');
    }
    
    // Log download activity
    $schoolAuth->logActivity(
        $currentUser['school_id'],
        $currentUser['id'],
        'download_attachment',
        "Downloaded attachment: {$attachment['original_name']} (ID: {$attachmentId})"
    );
    
    // Set headers for file download
    header('Content-Type: ' . $attachment['mime_type']);
    header('Content-Disposition: attachment; filename="' . $attachment['original_name'] . '"');
    header('Content-Length: ' . $attachment['file_size']);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    
    // Output file
    readfile($filePath);
    exit;
    
} catch (PDOException $e) {
    error_log("Download attachment error: " . $e->getMessage());
    http_response_code(500);
    die('Failed to download file.');
}
?>
