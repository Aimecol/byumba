<?php
/**
 * Create Upload Directories for School Administration System
 * Diocese of Byumba - School Management System
 */

// Create main uploads directory
$uploadDir = 'uploads/school-reports/';

if (!file_exists($uploadDir)) {
    if (mkdir($uploadDir, 0755, true)) {
        echo "✅ Created directory: $uploadDir\n";
    } else {
        echo "❌ Failed to create directory: $uploadDir\n";
        exit(1);
    }
} else {
    echo "ℹ️  Directory already exists: $uploadDir\n";
}

// Create .htaccess for security
$htaccessPath = $uploadDir . '.htaccess';
$htaccessContent = "# Deny direct access to uploaded files\n";
$htaccessContent .= "Options -Indexes\n";
$htaccessContent .= "<Files *.php>\n";
$htaccessContent .= "    Deny from all\n";
$htaccessContent .= "</Files>\n";
$htaccessContent .= "\n";
$htaccessContent .= "# Allow only specific file types\n";
$htaccessContent .= "<FilesMatch \"\\.(pdf|doc|docx|xls|xlsx|ppt|pptx|txt|jpg|jpeg|png)$\">\n";
$htaccessContent .= "    Order Allow,Deny\n";
$htaccessContent .= "    Allow from all\n";
$htaccessContent .= "</FilesMatch>\n";

if (file_put_contents($htaccessPath, $htaccessContent)) {
    echo "✅ Created security file: $htaccessPath\n";
} else {
    echo "❌ Failed to create security file: $htaccessPath\n";
}

// Create index.php to prevent directory listing
$indexPath = $uploadDir . 'index.php';
$indexContent = "<?php\n";
$indexContent .= "// Prevent directory access\n";
$indexContent .= "header('HTTP/1.0 403 Forbidden');\n";
$indexContent .= "exit('Access denied');\n";
$indexContent .= "?>";

if (file_put_contents($indexPath, $indexContent)) {
    echo "✅ Created index file: $indexPath\n";
} else {
    echo "❌ Failed to create index file: $indexPath\n";
}

// Create school-specific directories (if schools exist in database)
try {
    require_once 'config/database.php';
    
    $query = "SELECT id, school_code FROM schools WHERE is_active = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $schools = $stmt->fetchAll();
    
    foreach ($schools as $school) {
        $schoolDir = $uploadDir . 'school_' . $school['id'] . '/';
        
        if (!file_exists($schoolDir)) {
            if (mkdir($schoolDir, 0755, true)) {
                echo "✅ Created school directory: $schoolDir (for {$school['school_code']})\n";
                
                // Create index.php for each school directory
                $schoolIndexPath = $schoolDir . 'index.php';
                if (file_put_contents($schoolIndexPath, $indexContent)) {
                    echo "✅ Created index file: $schoolIndexPath\n";
                }
            } else {
                echo "❌ Failed to create school directory: $schoolDir\n";
            }
        } else {
            echo "ℹ️  School directory already exists: $schoolDir\n";
        }
    }
    
    echo "\n✅ Upload directory structure created successfully!\n";
    echo "📁 Main directory: $uploadDir\n";
    echo "🔒 Security files created\n";
    echo "🏫 " . count($schools) . " school directories created\n";
    
} catch (Exception $e) {
    echo "⚠️  Could not create school-specific directories: " . $e->getMessage() . "\n";
    echo "ℹ️  You can create them manually later or run this script again after setting up the database.\n";
}

echo "\n🎉 Setup complete! The file upload system is ready to use.\n";
?>
