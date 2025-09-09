<?php
/**
 * Debug Jobs API - Isolate the exact issue
 */

// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Jobs API Debug</h1>";
echo "<hr>";

try {
    // Test 1: Database Connection
    echo "<h2>Step 1: Database Connection Test</h2>";
    require_once 'config/database.php';
    
    if ($db) {
        echo "<p style='color: green;'>✅ Database connected</p>";
    } else {
        throw new Exception("Database connection failed");
    }
    
    // Test 2: Check parishes table structure
    echo "<h2>Step 2: Parishes Table Structure</h2>";
    $stmt = $db->prepare("DESCRIBE parishes");
    $stmt->execute();
    $columns = $stmt->fetchAll();
    
    $has_name = false;
    $has_name_en = false;
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
    foreach ($columns as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td></tr>";
        if ($col['Field'] === 'name') $has_name = true;
        if ($col['Field'] === 'name_en') $has_name_en = true;
    }
    echo "</table>";
    
    echo "<p><strong>Has 'name' column:</strong> " . ($has_name ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Has 'name_en' column:</strong> " . ($has_name_en ? 'Yes' : 'No') . "</p>";
    
    // Test 3: Test the exact query from jobs.php
    echo "<h2>Step 3: Test Jobs Query</h2>";
    
    $current_language = $current_language ?? 'en';
    
    // Build the exact same query as in jobs.php
    $where_conditions = ['j.is_active = 1', 'jct.language_code = :language'];
    $params = [':language' => $current_language];
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    
    $query = "SELECT j.*, jc.category_key, jc.icon as category_icon,
                     jct.name as category_name, jct.description as category_description,
                     COALESCE(p.name_en, p.name) as parish_name,
                     COALESCE(jt.title, j.title) as translated_title,
                     COALESCE(jt.description, j.description) as translated_description,
                     COALESCE(jt.requirements, j.requirements) as translated_requirements
              FROM jobs j
              JOIN job_categories jc ON j.job_category_id = jc.id
              JOIN job_category_translations jct ON jc.id = jct.job_category_id
              LEFT JOIN job_translations jt ON j.id = jt.job_id AND jt.language_code = :job_language
              LEFT JOIN parishes p ON j.parish_id = p.id
              $where_clause
              ORDER BY j.created_at DESC
              LIMIT 5";
    
    echo "<p><strong>Query:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; font-size: 12px;'>" . htmlspecialchars($query) . "</pre>";
    
    echo "<p><strong>Parameters:</strong></p>";
    echo "<pre>";
    print_r($params);
    echo "job_language: $current_language\n";
    echo "</pre>";
    
    // Execute the query
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':job_language', $current_language);
    
    echo "<p>Executing query...</p>";
    $stmt->execute();
    $jobs = $stmt->fetchAll();
    
    echo "<p style='color: green;'>✅ Query executed successfully!</p>";
    echo "<p><strong>Jobs found:</strong> " . count($jobs) . "</p>";
    
    if (count($jobs) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>ID</th><th>Job Number</th><th>Title</th><th>Parish Name</th><th>Category</th></tr>";
        foreach ($jobs as $job) {
            echo "<tr>";
            echo "<td>" . $job['id'] . "</td>";
            echo "<td>" . $job['job_number'] . "</td>";
            echo "<td>" . htmlspecialchars($job['translated_title']) . "</td>";
            echo "<td>" . htmlspecialchars($job['parish_name'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($job['category_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test 4: Test the actual API call
    echo "<h2>Step 4: Test API Endpoint</h2>";
    
    // Simulate the API call
    $_GET_backup = $_GET ?? [];
    $_SERVER_backup = $_SERVER ?? [];
    
    $_GET['endpoint'] = 'jobs';
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $method = 'GET';
    
    ob_start();
    try {
        include 'api/jobs.php';
        $api_output = ob_get_contents();
        ob_end_clean();
        
        echo "<p style='color: green;'>✅ API executed without errors</p>";
        echo "<p><strong>API Output:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow-y: auto; font-size: 12px;'>";
        echo htmlspecialchars($api_output);
        echo "</pre>";
        
        // Try to parse JSON
        $json_data = json_decode($api_output, true);
        if ($json_data) {
            echo "<p><strong>JSON Parse:</strong> ✅ Success</p>";
            if (isset($json_data['success']) && $json_data['success']) {
                echo "<p><strong>API Status:</strong> ✅ Success</p>";
                echo "<p><strong>Jobs Count:</strong> " . count($json_data['data']['jobs'] ?? []) . "</p>";
            } else {
                echo "<p><strong>API Status:</strong> ❌ Error - " . ($json_data['message'] ?? 'Unknown') . "</p>";
            }
        } else {
            echo "<p><strong>JSON Parse:</strong> ❌ Failed</p>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<p style='color: red;'>❌ API threw exception: " . $e->getMessage() . "</p>";
        echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
        echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    }
    
    // Restore original values
    $_GET = $_GET_backup;
    $_SERVER = $_SERVER_backup;
    
    // Test 5: Direct browser test
    echo "<h2>Step 5: Browser Test Links</h2>";
    echo "<p><a href='api/index.php?endpoint=jobs' target='_blank'>🔗 Test API Endpoint Directly</a></p>";
    echo "<p><a href='jobs.html' target='_blank'>🔗 Test Jobs Page</a></p>";
    
    echo "<h2>✅ Debug Complete</h2>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<p>If you see this message, the database query is working correctly in isolation.</p>";
    echo "<p>If the API still fails in the browser, there may be a different issue such as:</p>";
    echo "<ul>";
    echo "<li>PHP caching or opcache issues</li>";
    echo "<li>Different database connection in the API context</li>";
    echo "<li>Session or authentication issues</li>";
    echo "<li>Missing dependencies or includes</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Trace:</strong></p>";
    echo "<pre style='font-size: 12px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

?>

<style>
table { font-family: Arial, sans-serif; font-size: 14px; }
th { background: #f8f9fa; padding: 8px; text-align: left; }
td { padding: 6px 8px; }
pre { font-size: 12px; overflow-x: auto; }
h2 { color: #1e753f; border-bottom: 2px solid #1e753f; padding-bottom: 5px; }
</style>
