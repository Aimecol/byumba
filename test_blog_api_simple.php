<?php
/**
 * Simple Blog API Test
 */

// Test database connection first
require_once 'config/database.php';

echo "<h1>Simple Blog API Test</h1>\n";

try {
    // Test database connection
    echo "<h2>Database Connection Test</h2>\n";
    $stmt = $db->query("SELECT COUNT(*) as count FROM blog_posts");
    $result = $stmt->fetch();
    echo "<p>✓ Database connected. Found {$result['count']} blog posts.</p>\n";
    
    // Test blog_post_translations table
    $stmt = $db->query("SELECT COUNT(*) as count FROM blog_post_translations");
    $result = $stmt->fetch();
    echo "<p>✓ Blog translations table exists. Found {$result['count']} translations.</p>\n";
    
    // Test a simple query with translations
    echo "<h2>Blog Posts with Translations Test</h2>\n";
    
    $languages = ['en', 'rw', 'fr'];
    
    foreach ($languages as $lang) {
        echo "<h3>Language: " . strtoupper($lang) . "</h3>\n";
        
        $query = "
            SELECT 
                bp.id,
                bp.post_number,
                COALESCE(bpt.title, bp.title) as title,
                COALESCE(bpt.excerpt, bp.excerpt) as excerpt,
                bc.category_key,
                COALESCE(bct.name, bc.category_key) as category_name
            FROM blog_posts bp
            LEFT JOIN blog_categories bc ON bp.blog_category_id = bc.id
            LEFT JOIN blog_category_translations bct ON bc.id = bct.blog_category_id AND bct.language_code = :lang
            LEFT JOIN blog_post_translations bpt ON bp.id = bpt.blog_post_id AND bpt.language_code = :lang
            WHERE bp.is_published = 1
            ORDER BY bp.published_at DESC
            LIMIT 3
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':lang', $lang);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($posts) {
            foreach ($posts as $post) {
                echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>\n";
                echo "<strong>ID:</strong> {$post['id']}<br>\n";
                echo "<strong>Title:</strong> " . htmlspecialchars($post['title']) . "<br>\n";
                echo "<strong>Excerpt:</strong> " . htmlspecialchars(substr($post['excerpt'], 0, 100)) . "...<br>\n";
                echo "<strong>Category:</strong> " . htmlspecialchars($post['category_name']) . " ({$post['category_key']})<br>\n";
                echo "</div>\n";
            }
        } else {
            echo "<p>No posts found for language: $lang</p>\n";
        }
    }
    
    echo "<h2>✓ All tests completed successfully!</h2>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
    echo "<p>Stack trace:</p><pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>
