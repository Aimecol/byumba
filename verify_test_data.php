<?php
/**
 * Verify Test Data Installation
 * Diocese Certificate Application System
 */

require_once 'config/database.php';
session_start();

$current_user_id = $_SESSION['user_id'] ?? 1;

echo "<h1>🔍 Diocese Certificate System - Data Verification</h1>";
echo "<p><strong>Checking for User ID:</strong> " . $current_user_id . "</p>";
echo "<hr>";

try {
    // Check database connection
    if (!isset($db)) {
        $db = new PDO(
            'mysql:host=localhost;dbname=diocese_byumba;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    // 1. Check total applications
    echo "<h2>📊 Applications Summary</h2>";
    $total_query = "SELECT COUNT(*) as total FROM applications";
    $total_stmt = $db->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->fetch();
    echo "<p><strong>Total Applications in Database:</strong> " . $total_result['total'] . "</p>";

    // 2. Check applications by user
    $user_query = "SELECT COUNT(*) as count FROM applications WHERE user_id = :user_id";
    $user_stmt = $db->prepare($user_query);
    $user_stmt->execute([':user_id' => $current_user_id]);
    $user_result = $user_stmt->fetch();
    echo "<p><strong>Applications for Current User:</strong> " . $user_result['count'] . "</p>";

    // 3. Check by status
    echo "<h3>📋 Applications by Status</h3>";
    $status_query = "SELECT status, COUNT(*) as count FROM applications GROUP BY status ORDER BY count DESC";
    $status_stmt = $db->prepare($status_query);
    $status_stmt->execute();
    $status_results = $status_stmt->fetchAll();

    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr><th>Status</th><th>Count</th><th>Percentage</th></tr>";
    foreach ($status_results as $status) {
        $percentage = round(($status['count'] / $total_result['total']) * 100, 1);
        echo "<tr>";
        echo "<td><strong>" . ucfirst($status['status']) . "</strong></td>";
        echo "<td>" . $status['count'] . "</td>";
        echo "<td>" . $percentage . "%</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 4. Check by certificate type
    echo "<h3>🏆 Applications by Certificate Type</h3>";
    $type_query = "SELECT ct.name, COUNT(a.id) as count 
                   FROM applications a 
                   JOIN certificate_types ct ON a.certificate_type_id = ct.id 
                   GROUP BY ct.id, ct.name 
                   ORDER BY count DESC";
    $type_stmt = $db->prepare($type_query);
    $type_stmt->execute();
    $type_results = $type_stmt->fetchAll();

    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr><th>Certificate Type</th><th>Applications</th></tr>";
    foreach ($type_results as $type) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($type['name']) . "</td>";
        echo "<td>" . $type['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 5. Recent applications
    echo "<h3>🕒 Recent Applications (Last 10)</h3>";
    $recent_query = "SELECT a.application_number, ct.name as certificate_type, a.status, a.submitted_date, a.notes
                     FROM applications a 
                     JOIN certificate_types ct ON a.certificate_type_id = ct.id 
                     WHERE a.user_id = :user_id
                     ORDER BY a.submitted_date DESC 
                     LIMIT 10";
    $recent_stmt = $db->prepare($recent_query);
    $recent_stmt->execute([':user_id' => $current_user_id]);
    $recent_results = $recent_stmt->fetchAll();

    if (count($recent_results) > 0) {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>App #</th><th>Certificate Type</th><th>Status</th><th>Submitted</th><th>Notes</th></tr>";
        foreach ($recent_results as $app) {
            echo "<tr>";
            echo "<td><strong>" . $app['application_number'] . "</strong></td>";
            echo "<td>" . htmlspecialchars($app['certificate_type']) . "</td>";
            echo "<td><span style='color: " . getStatusColor($app['status']) . ";'>" . ucfirst($app['status']) . "</span></td>";
            echo "<td>" . date('M j, Y H:i', strtotime($app['submitted_date'])) . "</td>";
            echo "<td>" . htmlspecialchars(substr($app['notes'], 0, 50)) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p><em>No applications found for current user. Run the test data insertion script first.</em></p>";
    }

    // 6. Payment status overview
    echo "<h3>💳 Payment Status Overview</h3>";
    $payment_query = "SELECT payment_status, COUNT(*) as count FROM applications GROUP BY payment_status";
    $payment_stmt = $db->prepare($payment_query);
    $payment_stmt->execute();
    $payment_results = $payment_stmt->fetchAll();

    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr><th>Payment Status</th><th>Count</th></tr>";
    foreach ($payment_results as $payment) {
        echo "<tr>";
        echo "<td>" . ucfirst($payment['payment_status']) . "</td>";
        echo "<td>" . $payment['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 7. API Test Links
    echo "<h2>🔗 Quick API Tests</h2>";
    echo "<p><a href='api/applications.php' target='_blank'>📋 View Applications API</a></p>";
    echo "<p><a href='api/certificate_types.php' target='_blank'>🏆 View Certificate Types API</a></p>";
    echo "<p><a href='test_certificate_system.php' target='_blank'>🧪 Run Full Test Suite</a></p>";
    echo "<p><a href='debug_api_response.html' target='_blank'>🔍 Debug API Responses</a></p>";

    // 8. System Status
    echo "<h2>✅ System Status</h2>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<p><strong>✅ Database Connection:</strong> Working</p>";
    echo "<p><strong>✅ Applications Table:</strong> " . $total_result['total'] . " records</p>";
    echo "<p><strong>✅ Certificate Types:</strong> Available</p>";
    echo "<p><strong>✅ Test Data:</strong> " . ($user_result['count'] > 0 ? 'Installed' : 'Not found - run insert script') . "</p>";
    echo "</div>";

} catch (PDOException $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
} catch (Exception $e) {
    echo "<h2>❌ General Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

function getStatusColor($status) {
    switch ($status) {
        case 'pending': return '#856404';
        case 'processing': return '#0c5460';
        case 'approved': return '#155724';
        case 'completed': return '#155724';
        case 'rejected': return '#721c24';
        default: return '#000000';
    }
}

echo "<hr>";
echo "<p><em>Data verification completed. Use the links above to test the system functionality.</em></p>";
?>
