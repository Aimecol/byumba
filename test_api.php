<?php
/**
 * Test Blog API Translations
 */

// Set different languages and test the API
$languages = ['en', 'rw', 'fr'];

echo "<h1>Blog API Translation Test</h1>\n";

foreach ($languages as $lang) {
    echo "<h2>Testing Language: " . strtoupper($lang) . "</h2>\n";
    
    // Set language in session
    session_start();
    $_SESSION['language'] = $lang;
    session_write_close();
    
    // Test blog posts
    echo "<h3>Blog Posts:</h3>\n";
    $url = "http://localhost/new/byumba/api/index.php?endpoint=blog&action=posts&limit=2";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        foreach ($data['data']['posts'] as $post) {
            echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>\n";
            echo "<strong>Title:</strong> " . htmlspecialchars($post['title']) . "<br>\n";
            echo "<strong>Excerpt:</strong> " . htmlspecialchars(substr($post['excerpt'], 0, 100)) . "...<br>\n";
            echo "<strong>Category:</strong> " . htmlspecialchars($post['category']['name']) . "<br>\n";
            echo "</div>\n";
        }
    } else {
        echo "<p style='color: red;'>Error: " . ($data['message'] ?? 'Unknown error') . "</p>\n";
    }
    
    // Test categories
    echo "<h3>Categories:</h3>\n";
    $url = "http://localhost/new/byumba/api/index.php?endpoint=blog&action=categories";
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        foreach ($data['data'] as $category) {
            echo "<span style='background: #f0f0f0; padding: 5px; margin: 5px; display: inline-block;'>";
            echo htmlspecialchars($category['name']) . " (" . $category['category_key'] . ")";
            echo "</span>\n";
        }
    } else {
        echo "<p style='color: red;'>Error: " . ($data['message'] ?? 'Unknown error') . "</p>\n";
    }
    
    echo "<hr>\n";
}

echo "<p><strong>Test completed!</strong></p>\n";
?>
