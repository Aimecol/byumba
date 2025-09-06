<?php
/**
 * Command Line Blog System Migration Runner
 */

require_once 'config/database.php';

echo "Blog System Migration - Command Line\n";
echo "====================================\n\n";

try {
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    echo "✓ Database connection established\n";
    
    // Read the migration SQL file
    $sql_file = 'database/blog_system_migration.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("Migration file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    if ($sql_content === false) {
        throw new Exception("Failed to read migration file");
    }
    
    echo "✓ Migration file loaded\n";
    
    // Execute the entire SQL content as one statement using multi_query
    echo "Executing migration SQL...\n";

    try {
        // Use multi_query to execute the entire SQL file
        $mysqli = new mysqli('localhost', 'root', '', 'diocese_byumba');

        if ($mysqli->connect_error) {
            throw new Exception('MySQL connection failed: ' . $mysqli->connect_error);
        }

        // Execute the multi-query
        if ($mysqli->multi_query($sql_content)) {
            $executed = 0;
            do {
                $executed++;
                echo "✓ Executed statement batch " . $executed . "\n";

                // Store first result set
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->next_result());

            $errors = 0;
        } else {
            throw new Exception('SQL execution failed: ' . $mysqli->error);
        }

        $mysqli->close();

        echo "\nMigration Summary:\n";
        echo "Executed successfully: $executed statement batches\n";
        echo "Errors: $errors statements\n";

        if ($errors === 0) {
            echo "\n✓ Blog system migration completed successfully!\n";

            // Test the migration by checking if our posts exist
            $test_query = "SELECT COUNT(*) as count FROM blog_posts WHERE slug IN ('advent-reflection', 'youth-concert', 'community-outreach')";
            $stmt = $db->prepare($test_query);
            $stmt->execute();
            $result = $stmt->fetch();

            echo "✓ Verification: Found {$result['count']} blog posts in database\n";

            // Test tags
            $tag_query = "SELECT COUNT(*) as count FROM blog_tags";
            $stmt = $db->prepare($tag_query);
            $stmt->execute();
            $result = $stmt->fetch();

            echo "✓ Verification: Found {$result['count']} blog tags in database\n";

            // Test categories
            $cat_query = "SELECT COUNT(*) as count FROM blog_categories WHERE category_key IN ('spiritual-reflections', 'youth-ministry', 'family-life')";
            $stmt = $db->prepare($cat_query);
            $stmt->execute();
            $result = $stmt->fetch();

            echo "✓ Verification: Found {$result['count']} new blog categories in database\n";

        } else {
            echo "\n⚠ Migration completed with some errors. Please review the errors above.\n";
        }

    } catch (Exception $e) {
        echo "✗ SQL execution error: " . $e->getMessage() . "\n";
        $errors = 1;
        $executed = 0;
    }
    
} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nMigration complete!\n";
?>
