<?php
/**
 * Direct API Test - Test the jobs endpoint directly
 */

// Set up the environment like the API expects
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['endpoint'] = 'jobs';
$method = 'GET';

// Capture output
ob_start();

try {
    // Include the API router
    include 'api/index.php';
    
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "<h1>🧪 Direct API Test Results</h1>";
    echo "<hr>";
    
    echo "<h2>📤 API Response</h2>";
    echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; border: 1px solid #ddd; overflow-x: auto;'>";
    echo htmlspecialchars($output);
    echo "</pre>";
    
    // Try to decode JSON
    $json_data = json_decode($output, true);
    if ($json_data) {
        echo "<h2>📊 Parsed JSON Data</h2>";
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
        
        if (isset($json_data['success']) && $json_data['success']) {
            echo "<p><strong>✅ Status:</strong> Success</p>";
            
            if (isset($json_data['data']['jobs'])) {
                echo "<p><strong>📋 Jobs Count:</strong> " . count($json_data['data']['jobs']) . "</p>";
                
                if (count($json_data['data']['jobs']) > 0) {
                    echo "<p><strong>📝 Sample Job:</strong></p>";
                    $sample_job = $json_data['data']['jobs'][0];
                    echo "<ul>";
                    echo "<li><strong>ID:</strong> " . $sample_job['id'] . "</li>";
                    echo "<li><strong>Title:</strong> " . htmlspecialchars($sample_job['title']) . "</li>";
                    echo "<li><strong>Job Number:</strong> " . $sample_job['job_number'] . "</li>";
                    echo "<li><strong>Location:</strong> " . htmlspecialchars($sample_job['location']) . "</li>";
                    echo "<li><strong>Employment Type:</strong> " . $sample_job['employment_type'] . "</li>";
                    echo "</ul>";
                }
            }
            
            if (isset($json_data['data']['categories'])) {
                echo "<p><strong>📂 Categories Count:</strong> " . count($json_data['data']['categories']) . "</p>";
            }
            
            if (isset($json_data['data']['pagination'])) {
                $pagination = $json_data['data']['pagination'];
                echo "<p><strong>📄 Pagination:</strong> Page " . $pagination['current_page'] . " of " . $pagination['total_pages'] . " (Total: " . $pagination['total_items'] . " items)</p>";
            }
            
        } else {
            echo "<p><strong>❌ Status:</strong> Error</p>";
            echo "<p><strong>Message:</strong> " . ($json_data['message'] ?? 'Unknown error') . "</p>";
        }
        
        echo "</div>";
    } else {
        echo "<h2>❌ JSON Parse Error</h2>";
        echo "<p>Could not parse the API response as JSON. Raw output shown above.</p>";
    }
    
} catch (Exception $e) {
    ob_end_clean();
    echo "<h1>❌ API Test Error</h1>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

?>

<style>
pre {
    font-size: 12px;
    max-height: 400px;
    overflow-y: auto;
}
</style>

<hr>
<p><em>This test simulates calling the jobs API endpoint directly.</em></p>
<p><a href="api/index.php?endpoint=jobs" target="_blank">🔗 Test in Browser</a></p>
