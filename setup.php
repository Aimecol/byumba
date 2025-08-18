<?php
/**
 * Database Setup Script for Diocese of Byumba System
 * Run this script once to set up the database and sample data
 */

// Include database configuration
require_once 'config/database.php';

echo "<h1>Diocese of Byumba Database Setup</h1>\n";

try {
    // Read and execute the main database schema
    echo "<h2>Creating Database Schema...</h2>\n";
    $schema_sql = file_get_contents('database/diocese_byumba.sql');
    
    if ($schema_sql === false) {
        throw new Exception("Could not read database schema file");
    }
    
    // Split SQL statements and execute them
    $statements = explode(';', $schema_sql);
    $executed = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                $executed++;
            } catch (PDOException $e) {
                // Skip errors for statements that might already exist
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "<p style='color: orange;'>Warning: " . htmlspecialchars($e->getMessage()) . "</p>\n";
                }
            }
        }
    }
    
    echo "<p style='color: green;'>✓ Database schema created successfully! ($executed statements executed)</p>\n";
    
    // Read and execute sample data
    echo "<h2>Inserting Sample Data...</h2>\n";
    $sample_sql = file_get_contents('database/sample_data.sql');
    
    if ($sample_sql === false) {
        throw new Exception("Could not read sample data file");
    }
    
    // Split SQL statements and execute them
    $statements = explode(';', $sample_sql);
    $executed = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                $executed++;
            } catch (PDOException $e) {
                // Skip duplicate entry errors
                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                    echo "<p style='color: orange;'>Warning: " . htmlspecialchars($e->getMessage()) . "</p>\n";
                }
            }
        }
    }
    
    echo "<p style='color: green;'>✓ Sample data inserted successfully! ($executed statements executed)</p>\n";
    
    // Verify the setup
    echo "<h2>Verifying Setup...</h2>\n";
    
    // Check tables
    $tables_query = "SHOW TABLES";
    $stmt = $db->query($tables_query);
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Tables created:</strong> " . count($tables) . "</p>\n";
    echo "<ul>\n";
    foreach ($tables as $table) {
        echo "<li>$table</li>\n";
    }
    echo "</ul>\n";
    
    // Check sample data counts
    $data_checks = [
        'users' => 'SELECT COUNT(*) FROM users',
        'parishes' => 'SELECT COUNT(*) FROM parishes',
        'certificate_types' => 'SELECT COUNT(*) FROM certificate_types',
        'applications' => 'SELECT COUNT(*) FROM applications',
        'meetings' => 'SELECT COUNT(*) FROM meetings',
        'notifications' => 'SELECT COUNT(*) FROM notifications',
        'jobs' => 'SELECT COUNT(*) FROM jobs',
        'blog_posts' => 'SELECT COUNT(*) FROM blog_posts'
    ];
    
    echo "<p><strong>Sample data counts:</strong></p>\n";
    echo "<ul>\n";
    foreach ($data_checks as $table => $query) {
        try {
            $stmt = $db->query($query);
            $count = $stmt->fetchColumn();
            echo "<li>$table: $count records</li>\n";
        } catch (PDOException $e) {
            echo "<li style='color: red;'>$table: Error - " . htmlspecialchars($e->getMessage()) . "</li>\n";
        }
    }
    echo "</ul>\n";
    
    // Test API endpoints
    echo "<h2>Testing API Endpoints...</h2>\n";
    
    $api_tests = [
        'Language API' => 'api/language',
        'Dashboard API' => 'api/dashboard',
        'Applications API' => 'api/applications',
        'Meetings API' => 'api/meetings',
        'Notifications API' => 'api/notifications',
        'Certificates API' => 'api/certificates'
    ];
    
    echo "<ul>\n";
    foreach ($api_tests as $name => $endpoint) {
        $url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/$endpoint";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data && isset($data['success']) && $data['success']) {
                echo "<li style='color: green;'>✓ $name: Working</li>\n";
            } else {
                echo "<li style='color: orange;'>⚠ $name: Response received but may have issues</li>\n";
            }
        } else {
            echo "<li style='color: red;'>✗ $name: Not accessible</li>\n";
        }
    }
    echo "</ul>\n";
    
    echo "<h2>Setup Complete!</h2>\n";
    echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
    echo "<h3>✓ Database setup completed successfully!</h3>\n";
    echo "<p><strong>What's been set up:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>Complete database schema with multi-language support</li>\n";
    echo "<li>Sample data for testing (users, applications, meetings, notifications, etc.)</li>\n";
    echo "<li>API endpoints for all major features</li>\n";
    echo "<li>Language switching functionality</li>\n";
    echo "</ul>\n";
    echo "<p><strong>Test Credentials:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>Email: john.doe@email.com</li>\n";
    echo "<li>Password: password (hashed in database)</li>\n";
    echo "</ul>\n";
    echo "<p><strong>Next Steps:</strong></p>\n";
    echo "<ol>\n";
    echo "<li>Visit <a href='dashboard.html'>dashboard.html</a> to test the dashboard</li>\n";
    echo "<li>Try switching languages using the language buttons</li>\n";
    echo "<li>Test all dashboard pages (Profile, Applications, Meetings, Notifications)</li>\n";
    echo "<li>Check the API responses in browser developer tools</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
    
    echo "<p><strong>Important:</strong> Delete this setup.php file after successful setup for security reasons.</p>\n";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>\n";
    echo "<h3 style='color: #721c24;'>✗ Setup Failed</h3>\n";
    echo "<p style='color: #721c24;'><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p><strong>Please check:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>XAMPP is running with Apache and MySQL</li>\n";
    echo "<li>Database connection settings in config/database.php</li>\n";
    echo "<li>File permissions for database/ directory</li>\n";
    echo "<li>MySQL user has CREATE and INSERT privileges</li>\n";
    echo "</ul>\n";
    echo "</div>\n";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Diocese of Byumba</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            line-height: 1.6;
        }
        h1, h2, h3 {
            color: #1e753f;
        }
        .success {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #dc3545;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        ul, ol {
            padding-left: 30px;
        }
        li {
            margin: 5px 0;
        }
        a {
            color: #1e753f;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Content is generated by PHP above -->
</body>
</html>
