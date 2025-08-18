<?php
/**
 * Database Connection Test
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login
requireAdminLogin();

$tests = [];
$overall_status = true;

// Test 1: Database Connection
try {
    $test_query = "SELECT 1 as test";
    $stmt = $db->prepare($test_query);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result && $result['test'] == 1) {
        $tests[] = [
            'name' => 'Database Connection',
            'status' => 'success',
            'message' => 'Database connection is working properly'
        ];
    } else {
        $tests[] = [
            'name' => 'Database Connection',
            'status' => 'error',
            'message' => 'Database connection test failed'
        ];
        $overall_status = false;
    }
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Database Connection',
        'status' => 'error',
        'message' => 'Database connection error: ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 2: Admin Activity Log Table
try {
    $table_query = "SHOW TABLES LIKE 'admin_activity_log'";
    $stmt = $db->prepare($table_query);
    $stmt->execute();
    $table_exists = $stmt->fetch();
    
    if ($table_exists) {
        $tests[] = [
            'name' => 'Admin Activity Log Table',
            'status' => 'success',
            'message' => 'admin_activity_log table exists'
        ];
        
        // Test table structure
        $structure_query = "DESCRIBE admin_activity_log";
        $stmt = $db->prepare($structure_query);
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        $required_columns = ['id', 'admin_id', 'admin_email', 'action', 'details', 'ip_address', 'user_agent', 'created_at'];
        $existing_columns = array_column($columns, 'Field');
        $missing_columns = array_diff($required_columns, $existing_columns);
        
        if (empty($missing_columns)) {
            $tests[] = [
                'name' => 'Table Structure',
                'status' => 'success',
                'message' => 'All required columns are present'
            ];
        } else {
            $tests[] = [
                'name' => 'Table Structure',
                'status' => 'warning',
                'message' => 'Missing columns: ' . implode(', ', $missing_columns)
            ];
        }
    } else {
        $tests[] = [
            'name' => 'Admin Activity Log Table',
            'status' => 'error',
            'message' => 'admin_activity_log table does not exist. Please import database/admin_activity_log.sql'
        ];
        $overall_status = false;
    }
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Admin Activity Log Table',
        'status' => 'error',
        'message' => 'Error checking table: ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 3: Activity Logging Function
try {
    logAdminActivity('test_connection', 'Testing admin activity logging functionality');
    
    $tests[] = [
        'name' => 'Activity Logging Function',
        'status' => 'success',
        'message' => 'logAdminActivity() function is working'
    ];
} catch(Exception $e) {
    $tests[] = [
        'name' => 'Activity Logging Function',
        'status' => 'error',
        'message' => 'Error in logAdminActivity(): ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 4: Recent Activities Function
try {
    $recent_activities = getRecentAdminActivities(5);
    
    $tests[] = [
        'name' => 'Recent Activities Function',
        'status' => 'success',
        'message' => 'getRecentAdminActivities() returned ' . count($recent_activities) . ' activities'
    ];
} catch(Exception $e) {
    $tests[] = [
        'name' => 'Recent Activities Function',
        'status' => 'error',
        'message' => 'Error in getRecentAdminActivities(): ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 5: Connection Recovery
try {
    $recovery_test = handleDatabaseError(new PDOException('MySQL server has gone away', 2006));
    
    $tests[] = [
        'name' => 'Connection Recovery',
        'status' => 'success',
        'message' => 'Connection recovery function is available'
    ];
} catch(Exception $e) {
    $tests[] = [
        'name' => 'Connection Recovery',
        'status' => 'warning',
        'message' => 'Connection recovery function may not work properly: ' . $e->getMessage()
    ];
}

$page_title = 'Database Connection Test';
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">Database Connection Test</h1>
        <p class="text-muted">Testing database connectivity and admin activity logging functionality</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-database me-2"></i>Test Results
                    <?php if ($overall_status): ?>
                        <span class="badge bg-success ms-2">All Tests Passed</span>
                    <?php else: ?>
                        <span class="badge bg-danger ms-2">Some Tests Failed</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php foreach ($tests as $test): ?>
                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                        <div class="me-3">
                            <?php if ($test['status'] === 'success'): ?>
                                <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                            <?php elseif ($test['status'] === 'warning'): ?>
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo htmlspecialchars($test['name']); ?></h6>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($test['message']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="mt-4 p-3 bg-light rounded">
                    <h6>Next Steps:</h6>
                    <ul class="mb-0">
                        <?php if (!$overall_status): ?>
                            <li>Fix any failed tests before proceeding</li>
                            <li>If admin_activity_log table is missing, import <code>database/admin_activity_log.sql</code></li>
                        <?php else: ?>
                            <li>All tests passed! The system is ready to use</li>
                            <li>Visit the <a href="dashboard.php">Dashboard</a> to see recent admin activities</li>
                            <li>Visit the <a href="activities.php">Activities page</a> for detailed activity management</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
