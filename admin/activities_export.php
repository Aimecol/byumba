<?php
/**
 * Admin Activities Export
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('view_dashboard');

// Check if export is requested
if (!isset($_GET['export']) || $_GET['export'] !== 'csv') {
    header('Location: activities.php');
    exit;
}

// Get the same filters as the main activities page
$admin_filter = $_GET['admin'] ?? '';
$action_filter = $_GET['action_type'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Build query conditions
$where_conditions = [];
$params = [];

if (!empty($admin_filter)) {
    $where_conditions[] = 'aal.admin_email = :admin_email';
    $params[':admin_email'] = $admin_filter;
}

if (!empty($action_filter)) {
    $where_conditions[] = 'aal.action = :action';
    $params[':action'] = $action_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = 'DATE(aal.created_at) >= :date_from';
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = 'DATE(aal.created_at) <= :date_to';
    $params[':date_to'] = $date_to;
}

if (!empty($search)) {
    $where_conditions[] = '(aal.action LIKE :search OR aal.details LIKE :search OR aal.admin_email LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get activities for export
$query = "SELECT aal.*, u.first_name, u.last_name 
          FROM admin_activity_log aal
          LEFT JOIN users u ON aal.admin_id = u.id
          $where_clause
          ORDER BY aal.created_at DESC";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$activities = $stmt->fetchAll();

// Set headers for CSV download
$filename = 'admin_activities_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Create file pointer
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, [
    'Date & Time',
    'Admin Name',
    'Admin Email',
    'Action',
    'Details',
    'IP Address',
    'User Agent'
]);

// Add data rows
foreach ($activities as $activity) {
    $admin_name = ($activity['first_name'] && $activity['last_name']) 
        ? $activity['first_name'] . ' ' . $activity['last_name']
        : 'N/A';
    
    fputcsv($output, [
        $activity['created_at'],
        $admin_name,
        $activity['admin_email'],
        ucwords(str_replace('_', ' ', $activity['action'])),
        $activity['details'] ?: 'No details provided',
        $activity['ip_address'],
        $activity['user_agent']
    ]);
}

// Close file pointer
fclose($output);

// Log the export activity
logAdminActivity('export_activities', 'Exported ' . count($activities) . ' activity records');

exit;
?>
