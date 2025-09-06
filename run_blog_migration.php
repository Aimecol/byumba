<?php
/**
 * Blog System Migration Runner
 * Executes the blog system migration SQL script
 */

require_once 'config/database.php';

echo "<h1>Blog System Migration</h1>\n";

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    echo "<p>✓ Database connection established</p>\n";
    
    // Read the migration SQL file
    $sql_file = 'database/blog_system_migration.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("Migration file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    if ($sql_content === false) {
        throw new Exception("Failed to read migration file");
    }
    
    echo "<p>✓ Migration file loaded</p>\n";
    
    // Split SQL into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    echo "<p>Found " . count($statements) . " SQL statements to execute</p>\n";
    
    // Execute each statement
    $executed = 0;
    $errors = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            $db->exec($statement);
            $executed++;
            echo "<p style='color: green;'>✓ Executed statement " . ($executed) . "</p>\n";
        } catch (PDOException $e) {
            $errors++;
            echo "<p style='color: red;'>✗ Error in statement " . ($executed + $errors) . ": " . $e->getMessage() . "</p>\n";
            echo "<pre style='color: red; font-size: 12px;'>" . htmlspecialchars(substr($statement, 0, 200)) . "...</pre>\n";
        }
    }
    
    echo "<h2>Migration Summary</h2>\n";
    echo "<p><strong>Executed successfully:</strong> $executed statements</p>\n";
    echo "<p><strong>Errors:</strong> $errors statements</p>\n";
    
    if ($errors === 0) {
        echo "<p style='color: green; font-weight: bold;'>✓ Blog system migration completed successfully!</p>\n";
        
        // Test the migration by checking if our posts exist
        $test_query = "SELECT COUNT(*) as count FROM blog_posts WHERE slug IN ('advent-reflection', 'youth-concert', 'community-outreach')";
        $stmt = $db->prepare($test_query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        echo "<p>✓ Verification: Found {$result['count']} blog posts in database</p>\n";
        
        // Test tags
        $tag_query = "SELECT COUNT(*) as count FROM blog_tags";
        $stmt = $db->prepare($tag_query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        echo "<p>✓ Verification: Found {$result['count']} blog tags in database</p>\n";
        
        // Test categories
        $cat_query = "SELECT COUNT(*) as count FROM blog_categories WHERE category_key IN ('spiritual-reflections', 'youth-ministry', 'family-life')";
        $stmt = $db->prepare($cat_query);
        $stmt->execute();
        $result = $stmt->fetch();
        
        echo "<p>✓ Verification: Found {$result['count']} new blog categories in database</p>\n";
        
    } else {
        echo "<p style='color: orange; font-weight: bold;'>⚠ Migration completed with some errors. Please review the errors above.</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>✗ Migration failed: " . $e->getMessage() . "</p>\n";
}

echo "<hr>\n";
echo "<p><a href='api/blog-posts.php?action=get_posts'>Test Blog Posts API</a></p>\n";
echo "<p><a href='blog-post.html?post=advent-reflection'>Test Blog Post Page</a></p>\n";
echo "<p><a href='blog.html'>Back to Blog</a></p>\n";
?>
