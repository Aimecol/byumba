<?php
/**
 * Delete Report
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
    
    // Only allow deletion of draft reports
    if ($report['status'] !== 'draft') {
        $_SESSION['error_message'] = 'Only draft reports can be deleted.';
        header('Location: view-report.php?id=' . $reportId);
        exit;
    }
    
    // Delete the report
    $deleteQuery = "DELETE FROM school_reports WHERE id = :id";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $reportId);
    $deleteStmt->execute();
    
    // Log activity
    $schoolAuth->logActivity(
        $currentUser['school_id'],
        $currentUser['user_id'],
        'delete_report',
        "Deleted draft report: {$report['report_number']} - {$report['title']}"
    );
    
    $_SESSION['success_message'] = 'Report deleted successfully.';
    
} catch(PDOException $e) {
    error_log("Delete report error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Failed to delete report. Please try again.';
}

header('Location: reports.php');
exit;
?>
