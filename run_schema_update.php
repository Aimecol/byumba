<?php
/**
 * Run Database Schema Update
 * This script updates the database schema to support certificate-specific form data
 */

require_once 'config/database.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Schema Update</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>";

echo "<h1>Diocese of Byumba - Database Schema Update</h1>";

try {
    // Check if database connection exists
    if (!$db) {
        throw new Exception("Database connection failed. Please check your database configuration.");
    }
    
    echo "<div class='info'>Starting database schema update...</div>";
    
    // Read and execute the SQL file
    $sql_file = 'database/update_application_schema.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split SQL statements by semicolon and execute each one
    $statements = array_filter(array_map('trim', explode(';', $sql_content)));
    
    $executed_count = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        try {
            $db->exec($statement);
            $executed_count++;
            
            // Extract table/operation info for better feedback
            if (stripos($statement, 'CREATE TABLE') !== false) {
                preg_match('/CREATE TABLE.*?`([^`]+)`/i', $statement, $matches);
                $table_name = $matches[1] ?? 'unknown';
                echo "<div class='success'>✓ Created table: $table_name</div>";
            } elseif (stripos($statement, 'ALTER TABLE') !== false) {
                preg_match('/ALTER TABLE.*?`([^`]+)`/i', $statement, $matches);
                $table_name = $matches[1] ?? 'unknown';
                echo "<div class='success'>✓ Altered table: $table_name</div>";
            } elseif (stripos($statement, 'CREATE INDEX') !== false) {
                preg_match('/CREATE INDEX.*?`([^`]+)`/i', $statement, $matches);
                $index_name = $matches[1] ?? 'unknown';
                echo "<div class='success'>✓ Created index: $index_name</div>";
            } elseif (stripos($statement, 'INSERT INTO') !== false) {
                preg_match('/INSERT INTO.*?`([^`]+)`/i', $statement, $matches);
                $table_name = $matches[1] ?? 'unknown';
                echo "<div class='success'>✓ Inserted sample data into: $table_name</div>";
            } else {
                echo "<div class='success'>✓ Executed SQL statement</div>";
            }
            
        } catch (Exception $e) {
            // Check if error is about table/column already existing
            if (stripos($e->getMessage(), 'already exists') !== false || 
                stripos($e->getMessage(), 'Duplicate column') !== false) {
                echo "<div class='info'>ℹ Already exists: " . htmlspecialchars($e->getMessage()) . "</div>";
            } else {
                echo "<div class='error'>✗ Error executing statement: " . htmlspecialchars($e->getMessage()) . "</div>";
                echo "<div class='info'>Statement: " . htmlspecialchars(substr($statement, 0, 100)) . "...</div>";
            }
        }
    }
    
    echo "<div class='success'><strong>Schema update completed!</strong></div>";
    echo "<div class='info'>Total statements executed: $executed_count</div>";
    
    // Verify the new table exists
    $verify_query = "SHOW TABLES LIKE 'application_form_data'";
    $result = $db->query($verify_query);
    
    if ($result && $result->rowCount() > 0) {
        echo "<div class='success'>✓ application_form_data table verified</div>";
        
        // Check table structure
        $structure_query = "DESCRIBE application_form_data";
        $structure_result = $db->query($structure_query);
        
        if ($structure_result) {
            echo "<div class='info'>Table structure:</div>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            while ($row = $structure_result->fetch(PDO::FETCH_ASSOC)) {
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
        echo "<div class='error'>✗ application_form_data table not found after update</div>";
    }
    
    // Check if notification_methods column was added to applications table
    $check_column_query = "SHOW COLUMNS FROM applications LIKE 'notification_methods'";
    $column_result = $db->query($check_column_query);
    
    if ($column_result && $column_result->rowCount() > 0) {
        echo "<div class='success'>✓ notification_methods column added to applications table</div>";
    } else {
        echo "<div class='error'>✗ notification_methods column not found in applications table</div>";
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
