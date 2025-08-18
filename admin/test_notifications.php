<?php
/**
 * Notifications System Test
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login
requireAdminLogin();

$tests = [];
$overall_status = true;

// Test 1: Notification Types Table
try {
    $query = "SELECT COUNT(*) as count FROM notification_types WHERE is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $type_count = $stmt->fetch()['count'];
    
    if ($type_count > 0) {
        $tests[] = [
            'name' => 'Notification Types',
            'status' => 'success',
            'message' => "Found $type_count active notification types"
        ];
    } else {
        $tests[] = [
            'name' => 'Notification Types',
            'status' => 'warning',
            'message' => 'No active notification types found'
        ];
    }
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Notification Types',
        'status' => 'error',
        'message' => 'Error checking notification types: ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 2: Notification Type Translations
try {
    $query = "SELECT nt.id, nt.type_key, ntt.category as name 
              FROM notification_types nt
              JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id
              WHERE ntt.language_code = 'en' AND nt.is_active = 1
              ORDER BY ntt.category";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $translations = $stmt->fetchAll();
    
    if (!empty($translations)) {
        $tests[] = [
            'name' => 'Notification Type Translations',
            'status' => 'success',
            'message' => 'Found ' . count($translations) . ' translated notification types: ' . 
                        implode(', ', array_column($translations, 'name'))
        ];
    } else {
        $tests[] = [
            'name' => 'Notification Type Translations',
            'status' => 'error',
            'message' => 'No notification type translations found'
        ];
        $overall_status = false;
    }
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Notification Type Translations',
        'status' => 'error',
        'message' => 'Error checking translations: ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 3: Notifications Table
try {
    $query = "SELECT COUNT(*) as count FROM notifications";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $notification_count = $stmt->fetch()['count'];
    
    $tests[] = [
        'name' => 'Notifications Table',
        'status' => 'success',
        'message' => "Found $notification_count notifications in the database"
    ];
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Notifications Table',
        'status' => 'error',
        'message' => 'Error checking notifications: ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 4: Notifications with Type Names Query
try {
    $query = "SELECT n.*, 
                     u.first_name, u.last_name, u.email,
                     ntt.category as type_name
              FROM notifications n
              JOIN users u ON n.user_id = u.id
              JOIN notification_types nt ON n.notification_type_id = nt.id
              JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id
              WHERE ntt.language_code = 'en'
              ORDER BY n.created_at DESC
              LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $notifications = $stmt->fetchAll();
    
    $tests[] = [
        'name' => 'Notifications Query (Fixed)',
        'status' => 'success',
        'message' => 'Successfully retrieved ' . count($notifications) . ' notifications with type names'
    ];
    
    // Show sample data
    if (!empty($notifications)) {
        $sample = $notifications[0];
        $tests[] = [
            'name' => 'Sample Notification Data',
            'status' => 'success',
            'message' => "Title: '{$sample['title']}', Type: '{$sample['type_name']}', User: {$sample['first_name']} {$sample['last_name']}"
        ];
    }
    
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Notifications Query (Fixed)',
        'status' => 'error',
        'message' => 'Error in fixed query: ' . $e->getMessage()
    ];
    $overall_status = false;
}

// Test 5: User Count for Bulk Notifications
try {
    $query = "SELECT COUNT(*) as count FROM users WHERE is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $user_count = $stmt->fetch()['count'];
    
    $tests[] = [
        'name' => 'Active Users for Notifications',
        'status' => 'success',
        'message' => "Found $user_count active users for bulk notifications"
    ];
} catch(PDOException $e) {
    $tests[] = [
        'name' => 'Active Users for Notifications',
        'status' => 'error',
        'message' => 'Error checking users: ' . $e->getMessage()
    ];
}

$page_title = 'Notifications System Test';
include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">Notifications System Test</h1>
        <p class="text-muted">Testing notification types, translations, and database queries</p>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fas fa-bell me-2"></i>Test Results
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
                    <h6>Quick Actions:</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button onclick="location.reload()" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i>Refresh Tests
                        </button>
                        <a href="notifications.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-bell me-1"></i>Open Notifications
                        </a>
                        <a href="database_status.php" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-database me-1"></i>Database Status
                        </a>
                    </div>
                </div>
                
                <?php if ($overall_status): ?>
                    <div class="mt-3 p-3 bg-success bg-opacity-10 rounded">
                        <h6><i class="fas fa-check-circle me-2 text-success"></i>System Ready!</h6>
                        <p class="mb-0 small">The notifications system is working correctly. You can now:</p>
                        <ul class="mb-0 small">
                            <li>Send bulk notifications to users</li>
                            <li>View and manage existing notifications</li>
                            <li>Filter notifications by type and status</li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded">
                        <h6><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Issues Found</h6>
                        <p class="mb-0 small">Please resolve the failed tests before using the notifications system.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
