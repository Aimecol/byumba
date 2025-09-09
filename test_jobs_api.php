<?php
/**
 * Test Jobs API Endpoint
 * Diocese of Byumba - Debug jobs API issues
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Jobs API Test</h1>";
echo "<hr>";

try {
    // Include database configuration
    require_once 'config/database.php';
    
    echo "<h2>✅ Database Connection Test</h2>";
    if ($db) {
        echo "<p style='color: green;'>✅ Database connected successfully</p>";
        
        // Test parishes table structure
        echo "<h3>📋 Parishes Table Structure</h3>";
        $columns_query = "SHOW COLUMNS FROM parishes";
        $stmt = $db->prepare($columns_query);
        $stmt->execute();
        $columns = $stmt->fetchAll();
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . $column['Default'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Test the problematic query
        echo "<h3>🔍 Test Jobs Query</h3>";
        
        // Set up variables like the API would
        $current_language = $current_language ?? 'en';
        $category = 'all';
        $employment_type = 'all';
        $location = 'all';
        $page = 1;
        $limit = 20;
        $offset = 0;
        
        // Build query conditions
        $where_conditions = ['j.is_active = 1', 'jct.language_code = :language'];
        $params = [':language' => $current_language];
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Test the fixed query
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
                  LIMIT :limit OFFSET :offset";
        
        echo "<p><strong>Query:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; font-size: 12px;'>" . htmlspecialchars($query) . "</pre>";
        
        echo "<p><strong>Parameters:</strong></p>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
        print_r($params);
        echo "job_language: " . $current_language . "\n";
        echo "limit: " . $limit . "\n";
        echo "offset: " . $offset . "\n";
        echo "</pre>";
        
        // Execute the query
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':job_language', $current_language);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $jobs = $stmt->fetchAll();
        
        echo "<h3>📊 Query Results</h3>";
        echo "<p><strong>Jobs found:</strong> " . count($jobs) . "</p>";
        
        if (count($jobs) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
            echo "<tr><th>ID</th><th>Job Number</th><th>Title</th><th>Parish Name</th><th>Category</th><th>Employment Type</th></tr>";
            foreach (array_slice($jobs, 0, 5) as $job) { // Show first 5 jobs
                echo "<tr>";
                echo "<td>" . $job['id'] . "</td>";
                echo "<td>" . $job['job_number'] . "</td>";
                echo "<td>" . htmlspecialchars($job['translated_title']) . "</td>";
                echo "<td>" . htmlspecialchars($job['parish_name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($job['category_name']) . "</td>";
                echo "<td>" . $job['employment_type'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Test categories query
        echo "<h3>📂 Test Categories Query</h3>";
        $categories_query = "SELECT jc.*, jct.name, jct.description 
                            FROM job_categories jc 
                            JOIN job_category_translations jct ON jc.id = jct.job_category_id 
                            WHERE jc.is_active = 1 AND jct.language_code = :language 
                            ORDER BY jct.name";
        
        $stmt = $db->prepare($categories_query);
        $stmt->bindParam(':language', $current_language);
        $stmt->execute();
        $categories = $stmt->fetchAll();
        
        echo "<p><strong>Categories found:</strong> " . count($categories) . "</p>";
        
        if (count($categories) > 0) {
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr><th>ID</th><th>Category Key</th><th>Name</th><th>Icon</th></tr>";
            foreach ($categories as $category) {
                echo "<tr>";
                echo "<td>" . $category['id'] . "</td>";
                echo "<td>" . $category['category_key'] . "</td>";
                echo "<td>" . htmlspecialchars($category['name']) . "</td>";
                echo "<td>" . $category['icon'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Test the actual API endpoint
        echo "<h3>🌐 Test API Endpoint</h3>";
        echo "<p><a href='api/index.php?endpoint=jobs' target='_blank'>🔗 Test Jobs API Endpoint</a></p>";
        
        echo "<h3>✅ Summary</h3>";
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border: 1px solid #c3e6cb;'>";
        echo "<p><strong>✅ Database Connection:</strong> Working</p>";
        echo "<p><strong>✅ Parishes Table:</strong> Structure verified</p>";
        echo "<p><strong>✅ Jobs Query:</strong> Fixed and working</p>";
        echo "<p><strong>✅ Categories Query:</strong> Working</p>";
        echo "<p><strong>✅ Jobs Found:</strong> " . count($jobs) . "</p>";
        echo "<p><strong>✅ Categories Found:</strong> " . count($categories) . "</p>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

?>

<style>
table {
    font-family: Arial, sans-serif;
    font-size: 14px;
}
th {
    background: #f8f9fa;
    padding: 8px;
    text-align: left;
}
td {
    padding: 6px 8px;
}
pre {
    font-size: 12px;
    overflow-x: auto;
}
</style>
