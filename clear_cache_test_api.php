<?php
/**
 * Clear Cache and Test API
 * This script clears PHP opcache and tests the jobs API
 */

echo "<h1>🧹 Clear Cache & Test Jobs API</h1>";
echo "<hr>";

// Clear opcache if available
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p style='color: green;'>✅ OPcache cleared</p>";
} else {
    echo "<p style='color: orange;'>⚠️ OPcache not available</p>";
}

// Clear any file stat cache
clearstatcache();
echo "<p style='color: green;'>✅ File stat cache cleared</p>";

echo "<h2>🔍 File Modification Check</h2>";

$jobs_api_file = 'api/jobs.php';
if (file_exists($jobs_api_file)) {
    $mod_time = filemtime($jobs_api_file);
    echo "<p><strong>jobs.php last modified:</strong> " . date('Y-m-d H:i:s', $mod_time) . "</p>";
    
    // Check if the fix is actually in the file
    $content = file_get_contents($jobs_api_file);
    if (strpos($content, 'COALESCE(p.name_en, p.name)') !== false) {
        echo "<p style='color: green;'>✅ Fix is present in the file</p>";
    } else {
        echo "<p style='color: red;'>❌ Fix is NOT present in the file</p>";
    }
    
    // Show the relevant line
    $lines = file($jobs_api_file);
    if (isset($lines[54])) { // Line 55 (0-indexed)
        echo "<p><strong>Line 55 content:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px;'>" . htmlspecialchars(trim($lines[54])) . "</pre>";
    }
} else {
    echo "<p style='color: red;'>❌ jobs.php file not found</p>";
}

echo "<h2>🌐 Direct API Test</h2>";

// Test the API endpoint directly via HTTP
$api_url = 'http://localhost/byumba/api/index.php?endpoint=jobs';
echo "<p><strong>Testing URL:</strong> <a href='$api_url' target='_blank'>$api_url</a></p>";

// Use cURL to test the API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

$headers = substr($response, 0, $header_size);
$body = substr($response, $header_size);

echo "<h3>HTTP Response</h3>";
echo "<p><strong>Status Code:</strong> $http_code</p>";

if ($http_code == 200) {
    echo "<p style='color: green;'>✅ HTTP 200 - Success</p>";
    
    // Try to parse JSON
    $json_data = json_decode($body, true);
    if ($json_data) {
        echo "<p style='color: green;'>✅ Valid JSON response</p>";
        
        if (isset($json_data['success']) && $json_data['success']) {
            echo "<p style='color: green;'>✅ API returned success</p>";
            echo "<p><strong>Jobs found:</strong> " . count($json_data['data']['jobs'] ?? []) . "</p>";
            echo "<p><strong>Categories found:</strong> " . count($json_data['data']['categories'] ?? []) . "</p>";
            
            // Show first job as sample
            if (!empty($json_data['data']['jobs'])) {
                $first_job = $json_data['data']['jobs'][0];
                echo "<h4>Sample Job:</h4>";
                echo "<ul>";
                echo "<li><strong>ID:</strong> " . $first_job['id'] . "</li>";
                echo "<li><strong>Title:</strong> " . htmlspecialchars($first_job['title']) . "</li>";
                echo "<li><strong>Location:</strong> " . htmlspecialchars($first_job['location']) . "</li>";
                echo "</ul>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ API returned error: " . ($json_data['message'] ?? 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Invalid JSON response</p>";
        echo "<p><strong>Response body:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($body) . "</pre>";
    }
    
} else {
    echo "<p style='color: red;'>❌ HTTP $http_code - Error</p>";
    echo "<p><strong>Response headers:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px;'>" . htmlspecialchars($headers) . "</pre>";
    echo "<p><strong>Response body:</strong></p>";
    echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow-y: auto;'>" . htmlspecialchars($body) . "</pre>";
}

echo "<h2>🔧 Troubleshooting Actions</h2>";

if ($http_code != 200 || (isset($json_data) && !$json_data['success'])) {
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border: 1px solid #ffeaa7;'>";
    echo "<h4>Recommended Actions:</h4>";
    echo "<ol>";
    echo "<li><strong>Restart Apache:</strong> Restart the Apache service in XAMPP to clear any cached modules</li>";
    echo "<li><strong>Check Error Logs:</strong> Look at Apache error logs for detailed error information</li>";
    echo "<li><strong>Verify Database:</strong> Ensure MySQL is running and the database is accessible</li>";
    echo "<li><strong>Test Database Connection:</strong> Run a simple database test script</li>";
    echo "<li><strong>Check File Permissions:</strong> Ensure PHP can read the API files</li>";
    echo "</ol>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
    echo "<h4>✅ API is Working!</h4>";
    echo "<p>The jobs API is responding correctly. If you're still seeing errors in the frontend:</p>";
    echo "<ol>";
    echo "<li>Clear your browser cache</li>";
    echo "<li>Check browser console for JavaScript errors</li>";
    echo "<li>Verify the frontend is calling the correct API endpoint</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>🔗 Quick Links</h2>";
echo "<ul>";
echo "<li><a href='$api_url' target='_blank'>🌐 Direct API Test</a></li>";
echo "<li><a href='jobs.html' target='_blank'>📋 Jobs Page</a></li>";
echo "<li><a href='debug_jobs_api.php' target='_blank'>🔍 Detailed Debug</a></li>";
echo "</ul>";

?>

<style>
h2 { color: #1e753f; border-bottom: 2px solid #1e753f; padding-bottom: 5px; }
pre { font-size: 12px; overflow-x: auto; }
</style>
