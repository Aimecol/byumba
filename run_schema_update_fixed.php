<?php
/**
 * Fixed Database Schema Update
 * This script updates the database schema to support certificate-specific form data
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Schema Update - Fixed</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: orange; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>";

echo "<h1>Diocese of Byumba - Database Schema Update (Fixed)</h1>";

try {
    // Check if database connection exists
    if (!$db) {
        throw new Exception("Database connection failed. Please check your database configuration.");
    }
    
    echo "<div class='info'>Starting database schema update...</div>";
    
    // Step 1: Add notification_methods column to applications table
    echo "<div class='info'>Step 1: Adding notification_methods column to applications table...</div>";
    
    try {
        // Check if column already exists
        $check_column = $db->query("SHOW COLUMNS FROM applications LIKE 'notification_methods'");
        if ($check_column->rowCount() == 0) {
            $db->exec("ALTER TABLE `applications` ADD COLUMN `notification_methods` JSON DEFAULT NULL COMMENT 'Preferred notification methods (email, sms, phone)' AFTER `notes`");
            echo "<div class='success'>✓ Added notification_methods column to applications table</div>";
        } else {
            echo "<div class='warning'>ℹ notification_methods column already exists in applications table</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error adding notification_methods column: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Step 2: Create application_form_data table
    echo "<div class='info'>Step 2: Creating application_form_data table...</div>";
    
    try {
        $create_table_sql = "
        CREATE TABLE IF NOT EXISTS `application_form_data` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `application_id` int(11) NOT NULL,
          `field_name` varchar(100) NOT NULL,
          `field_value` text DEFAULT NULL,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          KEY `idx_application_id` (`application_id`),
          KEY `idx_field_name` (`field_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->exec($create_table_sql);
        echo "<div class='success'>✓ Created application_form_data table</div>";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "<div class='warning'>ℹ application_form_data table already exists</div>";
        } else {
            echo "<div class='error'>✗ Error creating application_form_data table: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    // Step 3: Add foreign key constraint
    echo "<div class='info'>Step 3: Adding foreign key constraint...</div>";
    
    try {
        // Check if foreign key already exists
        $check_fk = $db->query("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'application_form_data' 
            AND CONSTRAINT_NAME = 'fk_application_form_data_application'
        ");
        
        if ($check_fk->rowCount() == 0) {
            $db->exec("
                ALTER TABLE `application_form_data` 
                ADD CONSTRAINT `fk_application_form_data_application` 
                FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE
            ");
            echo "<div class='success'>✓ Added foreign key constraint</div>";
        } else {
            echo "<div class='warning'>ℹ Foreign key constraint already exists</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>✗ Error adding foreign key constraint: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    // Step 4: Create indexes
    echo "<div class='info'>Step 4: Creating indexes for better performance...</div>";
    
    $indexes = [
        'idx_applications_status' => "CREATE INDEX `idx_applications_status` ON `applications` (`status`)",
        'idx_applications_user_status' => "CREATE INDEX `idx_applications_user_status` ON `applications` (`user_id`, `status`)",
        'idx_applications_certificate_type' => "CREATE INDEX `idx_applications_certificate_type` ON `applications` (`certificate_type_id`)"
    ];
    
    foreach ($indexes as $index_name => $sql) {
        try {
            // Check if index already exists
            $check_index = $db->query("SHOW INDEX FROM applications WHERE Key_name = '$index_name'");
            if ($check_index->rowCount() == 0) {
                $db->exec($sql);
                echo "<div class='success'>✓ Created index: $index_name</div>";
            } else {
                echo "<div class='warning'>ℹ Index $index_name already exists</div>";
            }
        } catch (Exception $e) {
            echo "<div class='error'>✗ Error creating index $index_name: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "<div class='success'><strong>Schema update completed successfully!</strong></div>";
    
    // Step 5: Verify the updates
    echo "<div class='info'>Step 5: Verifying the updates...</div>";
    
    // Verify application_form_data table
    $verify_table = $db->query("SHOW TABLES LIKE 'application_form_data'");
    if ($verify_table && $verify_table->rowCount() > 0) {
        echo "<div class='success'>✓ application_form_data table verified</div>";
        
        // Show table structure
        $structure = $db->query("DESCRIBE application_form_data");
        if ($structure) {
            echo "<div class='info'>Table structure:</div>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            while ($row = $structure->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<div class='error'>✗ application_form_data table not found</div>";
    }
    
    // Verify notification_methods column
    $verify_column = $db->query("SHOW COLUMNS FROM applications LIKE 'notification_methods'");
    if ($verify_column && $verify_column->rowCount() > 0) {
        echo "<div class='success'>✓ notification_methods column verified in applications table</div>";
    } else {
        echo "<div class='error'>✗ notification_methods column not found in applications table</div>";
    }
    
    // Show current applications table structure
    echo "<div class='info'>Current applications table structure:</div>";
    $apps_structure = $db->query("DESCRIBE applications");
    if ($apps_structure) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        
        while ($row = $apps_structure->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'><strong>ERROR:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<br><div style='margin-top: 20px;'>
    <a href='admin/' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Admin Panel</a>
    <a href='application.html' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Test Application Form</a>
    <a href='index.php' style='background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Go to Main Site</a>
</div>";

echo "</body></html>";
?>
