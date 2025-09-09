<?php
/**
 * Insert Test Data for Applications Table
 * Diocese Certificate Application System
 */

require_once 'config/database.php';

// Start session to check for authenticated user
session_start();

// Determine user ID - use authenticated user or fallback to 1
$current_user_id = $_SESSION['user_id'] ?? 1;
$is_authenticated = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

echo "<h1>Diocese Certificate Application System - Test Data Insertion</h1>";
echo "<p><strong>Current User ID:</strong> " . $current_user_id . "</p>";
echo "<p><strong>Authentication Status:</strong> " . ($is_authenticated ? 'Authenticated' : 'Using fallback user ID') . "</p>";
echo "<hr>";

try {
    // Check if database connection exists
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

    // Test data array with various scenarios
    $test_applications = [
        // Completed Applications
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 1, // Abasheshakanguhe
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'completed',
            'submitted_date' => '2024-01-15 09:30:00',
            'approved_date' => '2024-01-18 14:20:00',
            'completed_date' => '2024-01-22 10:15:00',
            'payment_code' => 'PAY' . rand(100000, 999999),
            'payment_status' => 'confirmed',
            'payment_date' => '2024-01-17 16:45:00',
            'notes' => 'Certificate issued and ready for pickup. All documents verified successfully.',
            'notification_methods' => '["email", "sms"]'
        ],
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 7, // Marriage
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'completed',
            'submitted_date' => '2024-01-10 08:00:00',
            'approved_date' => '2024-01-15 13:30:00',
            'completed_date' => '2024-01-20 11:00:00',
            'payment_code' => 'PAY' . rand(100000, 999999),
            'payment_status' => 'confirmed',
            'payment_date' => '2024-01-14 10:20:00',
            'notes' => 'Marriage certificate completed. Couple notified for pickup.',
            'notification_methods' => '["email", "sms", "phone"]'
        ],
        
        // Processing Applications
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 2, // Ebenezer
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'processing',
            'submitted_date' => '2024-01-20 11:15:00',
            'approved_date' => null,
            'completed_date' => null,
            'payment_code' => null,
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'Application under review by parish committee. Additional documentation requested.',
            'notification_methods' => '["email"]'
        ],
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 4, // Seminary Visit
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'processing',
            'submitted_date' => '2024-01-22 09:00:00',
            'approved_date' => null,
            'completed_date' => null,
            'payment_code' => null,
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'Seminary visit request under review. Background check in progress.',
            'notification_methods' => '["email", "phone"]'
        ],
        
        // Approved Applications (Awaiting Payment)
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 3, // Father's Union
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'approved',
            'submitted_date' => '2024-01-18 14:30:00',
            'approved_date' => '2024-01-22 09:45:00',
            'completed_date' => null,
            'payment_code' => 'PAY' . rand(100000, 999999),
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'Application approved. Awaiting payment confirmation to proceed with certificate issuance.',
            'notification_methods' => '["email", "sms"]'
        ],
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 5, // GFS
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'approved',
            'submitted_date' => '2024-01-19 15:30:00',
            'approved_date' => '2024-01-24 10:15:00',
            'completed_date' => null,
            'payment_code' => 'PAY' . rand(100000, 999999),
            'payment_status' => 'paid',
            'payment_date' => '2024-01-23 14:20:00',
            'notes' => 'Application approved and payment received. Certificate preparation in progress.',
            'notification_methods' => '["email", "sms"]'
        ],
        
        // Pending Applications
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 6, // Christian Certificate
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'submitted_date' => '2024-01-25 16:20:00',
            'approved_date' => null,
            'completed_date' => null,
            'payment_code' => null,
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'New application received. Awaiting initial document verification.',
            'notification_methods' => '["email"]'
        ],
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 8, // Mother's Union
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'submitted_date' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'approved_date' => null,
            'completed_date' => null,
            'payment_code' => null,
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'Recent Mother\'s Union application. Initial documentation review scheduled.',
            'notification_methods' => '["email", "sms"]'
        ],
        
        // Recent Applications (Last few days)
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 9, // Youth Union
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'submitted_date' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'approved_date' => null,
            'completed_date' => null,
            'payment_code' => null,
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'Youth Union application submitted yesterday.',
            'notification_methods' => '["email", "sms"]'
        ],
        [
            'user_id' => $current_user_id,
            'certificate_type_id' => 1, // Abasheshakanguhe
            'application_number' => 'APP' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
            'status' => 'pending',
            'submitted_date' => date('Y-m-d H:i:s', strtotime('-3 hours')),
            'approved_date' => null,
            'completed_date' => null,
            'payment_code' => null,
            'payment_status' => 'pending',
            'payment_date' => null,
            'notes' => 'Fresh application submitted today. Awaiting document verification process.',
            'notification_methods' => '["email"]'
        ]
    ];

    // Prepare INSERT statement
    $sql = "INSERT INTO applications (
        user_id, certificate_type_id, application_number, status, submitted_date,
        approved_date, completed_date, payment_code, payment_status, payment_date,
        notes, notification_methods
    ) VALUES (
        :user_id, :certificate_type_id, :application_number, :status, :submitted_date,
        :approved_date, :completed_date, :payment_code, :payment_status, :payment_date,
        :notes, :notification_methods
    )";

    $stmt = $db->prepare($sql);
    
    $inserted_count = 0;
    $errors = [];

    echo "<h2>Inserting Test Applications...</h2>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 10px; border-radius: 5px;'>";

    foreach ($test_applications as $index => $app) {
        try {
            $stmt->execute([
                ':user_id' => $app['user_id'],
                ':certificate_type_id' => $app['certificate_type_id'],
                ':application_number' => $app['application_number'],
                ':status' => $app['status'],
                ':submitted_date' => $app['submitted_date'],
                ':approved_date' => $app['approved_date'],
                ':completed_date' => $app['completed_date'],
                ':payment_code' => $app['payment_code'],
                ':payment_status' => $app['payment_status'],
                ':payment_date' => $app['payment_date'],
                ':notes' => $app['notes'],
                ':notification_methods' => $app['notification_methods']
            ]);
            
            $inserted_count++;
            echo "✅ Inserted: " . $app['application_number'] . " (" . $app['status'] . " - Certificate Type " . $app['certificate_type_id'] . ")<br>";
            
        } catch (PDOException $e) {
            $errors[] = "❌ Error inserting " . $app['application_number'] . ": " . $e->getMessage();
            echo "❌ Error inserting " . $app['application_number'] . ": " . $e->getMessage() . "<br>";
        }
    }

    echo "</div>";
    
    echo "<h2>Summary</h2>";
    echo "<p><strong>✅ Successfully inserted:</strong> " . $inserted_count . " applications</p>";
    echo "<p><strong>❌ Errors:</strong> " . count($errors) . "</p>";
    
    if (count($errors) > 0) {
        echo "<h3>Error Details:</h3>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>" . $error . "</li>";
        }
        echo "</ul>";
    }

    // Show summary by status
    echo "<h3>Applications by Status:</h3>";
    $status_query = "SELECT status, COUNT(*) as count FROM applications WHERE user_id = :user_id GROUP BY status";
    $status_stmt = $db->prepare($status_query);
    $status_stmt->execute([':user_id' => $current_user_id]);
    $status_results = $status_stmt->fetchAll();
    
    echo "<ul>";
    foreach ($status_results as $status) {
        echo "<li><strong>" . ucfirst($status['status']) . ":</strong> " . $status['count'] . " applications</li>";
    }
    echo "</ul>";

} catch (PDOException $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<h2>❌ General Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Test data insertion completed. You can now test the certificate application system with realistic sample data.</em></p>";
echo "<p><a href='api/applications.php'>View Applications API</a> | <a href='debug_api_response.html'>Test API Endpoints</a></p>";
?>
