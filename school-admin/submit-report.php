<?php
/**
 * Submit Report
 * Diocese of Byumba - School Management System
 */

session_start();
require_once 'includes/functions.php';

$currentUser = $schoolAuth->getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: reports.php');
    exit;
}

$reportId = intval($_POST['report_id'] ?? 0);

if (!$reportId) {
    $_SESSION['error_message'] = 'Invalid report ID.';
    header('Location: reports.php');
    exit;
}

try {
    // Check if report exists and belongs to current school
    $query = "SELECT id, report_number, status, title FROM school_reports 
             WHERE id = :id AND school_id = :school_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $reportId);
    $stmt->bindParam(':school_id', $currentUser['school_id']);
    $stmt->execute();
    
    $report = $stmt->fetch();
    
    if (!$report) {
        $_SESSION['error_message'] = 'Report not found.';
        header('Location: reports.php');
        exit;
    }
    
    // Only allow submission of draft reports
    if ($report['status'] !== 'draft') {
        $_SESSION['error_message'] = 'Only draft reports can be submitted.';
        header('Location: view-report.php?id=' . $reportId);
        exit;
    }
    
    // Update report status to submitted
    $updateQuery = "UPDATE school_reports 
                   SET status = 'submitted', submitted_at = NOW() 
                   WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':id', $reportId);
    $updateStmt->execute();
    
    // Log activity
    $schoolAuth->logActivity(
        $currentUser['school_id'],
        $currentUser['user_id'],
        'submit_report',
        "Submitted report: {$report['report_number']} - {$report['title']}"
    );
    
    $_SESSION['success_message'] = 'Report submitted successfully! It will be reviewed by the diocese administration.';
    
} catch(PDOException $e) {
    error_log("Submit report error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Failed to submit report. Please try again.';
}

header('Location: view-report.php?id=' . $reportId);
exit;
?>
