<?php
/**
 * Test Applications API
 */

echo "<h1>Testing Applications API</h1>";

// Test GET request
echo "<h2>Testing GET Request</h2>";
try {
    $url = 'http://localhost/byumba/api/applications.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    echo "<h3>Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<h3>Parsed JSON:</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "<h3>JSON Parse Error:</h3>";
        echo "<p>Response is not valid JSON</p>";
    }
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}

// Test POST request
echo "<h2>Testing POST Request</h2>";
try {
    $testData = [
        'certificate_type_id' => 1,
        'form_data' => [
            'fullName' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '250788123456'
        ],
        'notification_methods' => ['email'],
        'notes' => 'Test application from PHP script'
    ];
    
    $url = 'http://localhost/byumba/api/applications.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    echo "<h3>Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<h3>Parsed JSON:</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "<h3>JSON Parse Error:</h3>";
        echo "<p>Response is not valid JSON</p>";
    }
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}

// Test Certificate Types API
echo "<h2>Testing Certificate Types API</h2>";
try {
    $url = 'http://localhost/byumba/api/certificate_types.php';
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    echo "<h3>Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    $data = json_decode($response, true);
    if ($data) {
        echo "<h3>Parsed JSON:</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "<h3>JSON Parse Error:</h3>";
        echo "<p>Response is not valid JSON</p>";
    }
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
