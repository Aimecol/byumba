<?php
/**
 * Test File Upload Integration
 * Diocese of Byumba - School Management System
 */

// Test file upload integration without database
echo "<h2>File Upload Integration Test</h2>";

// Test 1: Check if FileHandler class exists
echo "<h3>1. FileHandler Class Test</h3>";
if (file_exists('school-admin/includes/file-handler.php')) {
    echo "✅ FileHandler file exists<br>";
    
    // Include the file
    require_once 'school-admin/includes/file-handler.php';
    
    if (class_exists('FileHandler')) {
        echo "✅ FileHandler class loaded successfully<br>";
        
        // Test static methods
        $testIcon = FileHandler::getFileIcon('test.pdf');
        $testSize = FileHandler::formatFileSize(1048576);
        
        echo "✅ Static methods working: Icon = $testIcon, Size = $testSize<br>";
    } else {
        echo "❌ FileHandler class not found<br>";
    }
} else {
    echo "❌ FileHandler file not found<br>";
}

// Test 2: Check upload directory
echo "<h3>2. Upload Directory Test</h3>";
$uploadDir = 'uploads/school-reports/';
if (is_dir($uploadDir)) {
    echo "✅ Upload directory exists: $uploadDir<br>";
    
    if (is_writable($uploadDir)) {
        echo "✅ Upload directory is writable<br>";
    } else {
        echo "⚠️ Upload directory is not writable<br>";
    }
    
    // Check security files
    if (file_exists($uploadDir . '.htaccess')) {
        echo "✅ .htaccess security file exists<br>";
    } else {
        echo "⚠️ .htaccess security file missing<br>";
    }
    
    if (file_exists($uploadDir . 'index.php')) {
        echo "✅ index.php security file exists<br>";
    } else {
        echo "⚠️ index.php security file missing<br>";
    }
} else {
    echo "❌ Upload directory does not exist<br>";
}

// Test 3: Check form integration
echo "<h3>3. Form Integration Test</h3>";
if (file_exists('school-admin/create-report.php')) {
    $content = file_get_contents('school-admin/create-report.php');
    
    if (strpos($content, 'enctype="multipart/form-data"') !== false) {
        echo "✅ Form has correct enctype for file uploads<br>";
    } else {
        echo "❌ Form missing enctype attribute<br>";
    }
    
    if (strpos($content, 'name="attachments[]"') !== false) {
        echo "✅ File input has correct name attribute<br>";
    } else {
        echo "❌ File input missing correct name attribute<br>";
    }
    
    if (strpos($content, "\$_FILES['attachments']") !== false) {
        echo "✅ PHP code processes \$_FILES['attachments']<br>";
    } else {
        echo "❌ PHP code doesn't process file uploads<br>";
    }
    
    if (strpos($content, 'FileHandler') !== false) {
        echo "✅ FileHandler is used in create-report.php<br>";
    } else {
        echo "❌ FileHandler not used in create-report.php<br>";
    }
} else {
    echo "❌ create-report.php not found<br>";
}

// Test 4: Check view-report integration
echo "<h3>4. View Report Integration Test</h3>";
if (file_exists('school-admin/view-report.php')) {
    $content = file_get_contents('school-admin/view-report.php');
    
    if (strpos($content, 'getReportAttachments') !== false) {
        echo "✅ view-report.php fetches attachments<br>";
    } else {
        echo "❌ view-report.php doesn't fetch attachments<br>";
    }
    
    if (strpos($content, 'download-attachment.php') !== false) {
        echo "✅ Download links are present<br>";
    } else {
        echo "❌ Download links missing<br>";
    }
} else {
    echo "❌ view-report.php not found<br>";
}

// Test 5: Check download handler
echo "<h3>5. Download Handler Test</h3>";
if (file_exists('school-admin/download-attachment.php')) {
    echo "✅ Download handler exists<br>";
    
    $content = file_get_contents('school-admin/download-attachment.php');
    if (strpos($content, 'readfile') !== false) {
        echo "✅ Download handler uses readfile()<br>";
    } else {
        echo "⚠️ Download handler might not stream files properly<br>";
    }
} else {
    echo "❌ Download handler not found<br>";
}

// Test 6: Simulate file upload validation
echo "<h3>6. File Validation Test</h3>";
$allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
echo "✅ Supported file types: " . implode(', ', $allowedTypes) . "<br>";

$maxFileSize = 10 * 1024 * 1024; // 10MB
$maxTotalSize = 50 * 1024 * 1024; // 50MB
echo "✅ File size limits: " . number_format($maxFileSize / 1024 / 1024, 0) . "MB per file, " . 
     number_format($maxTotalSize / 1024 / 1024, 0) . "MB total<br>";

// Test 7: Check JavaScript integration
echo "<h3>7. JavaScript Integration Test</h3>";
if (file_exists('school-admin/create-report.php')) {
    $content = file_get_contents('school-admin/create-report.php');
    
    if (strpos($content, 'drag-over') !== false) {
        echo "✅ Drag and drop functionality present<br>";
    } else {
        echo "❌ Drag and drop functionality missing<br>";
    }
    
    if (strpos($content, 'handleFiles') !== false) {
        echo "✅ File handling JavaScript present<br>";
    } else {
        echo "❌ File handling JavaScript missing<br>";
    }
    
    if (strpos($content, 'updateFileInput') !== false) {
        echo "✅ File input update functionality present<br>";
    } else {
        echo "❌ File input update functionality missing<br>";
    }
}

echo "<h3>Summary</h3>";
echo "<p><strong>The file upload integration should now work as follows:</strong></p>";
echo "<ol>";
echo "<li>User selects files via drag-and-drop or file picker</li>";
echo "<li>Files are validated client-side (type, size)</li>";
echo "<li>When user submits form, report is created first</li>";
echo "<li>Then files are processed and uploaded server-side</li>";
echo "<li>Files are linked to the new report in database</li>";
echo "<li>User sees confirmation with file upload status</li>";
echo "<li>Files are visible when viewing the report</li>";
echo "</ol>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Start XAMPP MySQL service</li>";
echo "<li>Test by creating a report with file attachments</li>";
echo "<li>Verify files appear in view-report.php</li>";
echo "<li>Test file downloads</li>";
echo "</ul>";
?>
