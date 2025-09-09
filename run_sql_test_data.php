<?php
/**
 * Execute SQL Test Data File
 * Diocese Certificate Application System
 */

require_once 'config/database.php';

echo "<h1>📊 Diocese Certificate System - SQL Test Data Execution</h1>";

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

    // Read the SQL file
    $sql_file = 'insert_applications_test_data.sql';
    
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: " . $sql_file);
    }

    $sql_content = file_get_contents($sql_file);
    
    if ($sql_content === false) {
        throw new Exception("Could not read SQL file: " . $sql_file);
    }

    echo "<p><strong>SQL File:</strong> " . $sql_file . "</p>";
    echo "<p><strong>File Size:</strong> " . number_format(strlen($sql_content)) . " characters</p>";
    echo "<hr>";

    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );

    echo "<h2>🚀 Executing SQL Statements</h2>";
    echo "<div style='font-family: monospace; background: #f8f9fa; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";

    $executed_count = 0;
    $error_count = 0;
    $inserted_records = 0;

    foreach ($statements as $index => $statement) {
        $statement = trim($statement);
        if (empty($statement)) continue;

        try {
            $stmt = $db->prepare($statement);
            $result = $stmt->execute();
            
            if ($result) {
                $affected_rows = $stmt->rowCount();
                $inserted_records += $affected_rows;
                $executed_count++;
                
                // Show first few words of statement for identification
                $preview = substr(preg_replace('/\s+/', ' ', $statement), 0, 60) . '...';
                echo "✅ Statement " . ($index + 1) . ": " . htmlspecialchars($preview) . " (" . $affected_rows . " rows)<br>";
            }
            
        } catch (PDOException $e) {
            $error_count++;
            $preview = substr(preg_replace('/\s+/', ' ', $statement), 0, 60) . '...';
            echo "❌ Statement " . ($index + 1) . ": " . htmlspecialchars($preview) . " - ERROR: " . $e->getMessage() . "<br>";
        }
    }

    echo "</div>";

    echo "<h2>📊 Execution Summary</h2>";
    echo "<div style='background: " . ($error_count > 0 ? '#fff3cd' : '#d4edda') . "; padding: 15px; border-radius: 5px;'>";
    echo "<p><strong>✅ Statements Executed:</strong> " . $executed_count . "</p>";
    echo "<p><strong>📝 Records Inserted:</strong> " . $inserted_records . "</p>";
    echo "<p><strong>❌ Errors:</strong> " . $error_count . "</p>";
    echo "<p><strong>📄 Total Statements:</strong> " . count($statements) . "</p>";
    echo "</div>";

    // Verify the data was inserted
    echo "<h2>🔍 Data Verification</h2>";
    
    $verify_query = "SELECT COUNT(*) as total FROM applications";
    $verify_stmt = $db->prepare($verify_query);
    $verify_stmt->execute();
    $total_apps = $verify_stmt->fetch()['total'];
    
    $recent_query = "SELECT application_number, status, submitted_date FROM applications ORDER BY id DESC LIMIT 5";
    $recent_stmt = $db->prepare($recent_query);
    $recent_stmt->execute();
    $recent_apps = $recent_stmt->fetchAll();

    echo "<p><strong>Total Applications in Database:</strong> " . $total_apps . "</p>";
    
    if (count($recent_apps) > 0) {
        echo "<p><strong>Most Recent Applications:</strong></p>";
        echo "<ul>";
        foreach ($recent_apps as $app) {
            echo "<li>" . $app['application_number'] . " - " . ucfirst($app['status']) . " (" . $app['submitted_date'] . ")</li>";
        }
        echo "</ul>";
    }

    echo "<h2>🔗 Next Steps</h2>";
    echo "<p><a href='verify_test_data.php'>📊 View Detailed Data Verification</a></p>";
    echo "<p><a href='test_certificate_system.php'>🧪 Run System Test Suite</a></p>";
    echo "<p><a href='api/applications.php'>📋 Test Applications API</a></p>";

} catch (PDOException $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
} catch (Exception $e) {
    echo "<h2>❌ File Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>SQL execution completed. Check the summary above for results.</em></p>";
?>
