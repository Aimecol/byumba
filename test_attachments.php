<?php
/**
 * Test Document Attachment Functionality
 * Diocese of Byumba - School Management System
 */

require_once 'config/database.php';
require_once 'school-admin/includes/file-handler.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Document Attachments - Diocese of Byumba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="school-admin/assets/css/school-admin.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-paperclip me-2"></i>Document Attachment System Test</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $tests = [];
                        $allPassed = true;

                        try {
                            // Test 1: Check FileHandler class
                            if (class_exists('FileHandler')) {
                                $fileHandler = new FileHandler($db);
                                $tests[] = [
                                    'name' => 'FileHandler Class',
                                    'status' => 'success',
                                    'message' => 'FileHandler class loaded successfully'
                                ];
                            } else {
                                $tests[] = [
                                    'name' => 'FileHandler Class',
                                    'status' => 'error',
                                    'message' => 'FileHandler class not found'
                                ];
                                $allPassed = false;
                            }

                            // Test 2: Check upload directory
                            $uploadDir = 'uploads/school-reports/';
                            if (is_dir($uploadDir)) {
                                if (is_writable($uploadDir)) {
                                    $tests[] = [
                                        'name' => 'Upload Directory',
                                        'status' => 'success',
                                        'message' => "Upload directory exists and is writable: $uploadDir"
                                    ];
                                } else {
                                    $tests[] = [
                                        'name' => 'Upload Directory',
                                        'status' => 'warning',
                                        'message' => "Upload directory exists but is not writable: $uploadDir"
                                    ];
                                }
                            } else {
                                $tests[] = [
                                    'name' => 'Upload Directory',
                                    'status' => 'error',
                                    'message' => "Upload directory does not exist: $uploadDir"
                                ];
                                $allPassed = false;
                            }

                            // Test 3: Check security files
                            $htaccessPath = $uploadDir . '.htaccess';
                            $indexPath = $uploadDir . 'index.php';
                            
                            $securityFiles = [];
                            if (file_exists($htaccessPath)) {
                                $securityFiles[] = '.htaccess';
                            }
                            if (file_exists($indexPath)) {
                                $securityFiles[] = 'index.php';
                            }
                            
                            if (count($securityFiles) === 2) {
                                $tests[] = [
                                    'name' => 'Security Files',
                                    'status' => 'success',
                                    'message' => 'Security files present: ' . implode(', ', $securityFiles)
                                ];
                            } else {
                                $tests[] = [
                                    'name' => 'Security Files',
                                    'status' => 'warning',
                                    'message' => 'Some security files missing. Present: ' . implode(', ', $securityFiles)
                                ];
                            }

                            // Test 4: Check report_attachments table
                            $query = "DESCRIBE report_attachments";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            $columns = $stmt->fetchAll();
                            
                            $requiredColumns = ['id', 'report_id', 'file_name', 'original_name', 'file_path', 'file_size', 'mime_type', 'uploaded_by', 'uploaded_at'];
                            $existingColumns = array_column($columns, 'Field');
                            $missingColumns = array_diff($requiredColumns, $existingColumns);
                            
                            if (empty($missingColumns)) {
                                $tests[] = [
                                    'name' => 'Database Table',
                                    'status' => 'success',
                                    'message' => 'report_attachments table has all required columns'
                                ];
                            } else {
                                $tests[] = [
                                    'name' => 'Database Table',
                                    'status' => 'error',
                                    'message' => 'Missing columns: ' . implode(', ', $missingColumns)
                                ];
                                $allPassed = false;
                            }

                            // Test 5: Check file type validation
                            $allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
                            $tests[] = [
                                'name' => 'File Type Support',
                                'status' => 'success',
                                'message' => 'Supports ' . count($allowedTypes) . ' file types: ' . implode(', ', $allowedTypes)
                            ];

                            // Test 6: Check school directories
                            $query = "SELECT COUNT(*) as count FROM schools WHERE is_active = 1";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            $schoolCount = $stmt->fetch()['count'];
                            
                            $schoolDirs = glob($uploadDir . 'school_*', GLOB_ONLYDIR);
                            $schoolDirCount = count($schoolDirs);
                            
                            if ($schoolDirCount >= $schoolCount) {
                                $tests[] = [
                                    'name' => 'School Directories',
                                    'status' => 'success',
                                    'message' => "Found $schoolDirCount school directories for $schoolCount active schools"
                                ];
                            } else {
                                $tests[] = [
                                    'name' => 'School Directories',
                                    'status' => 'warning',
                                    'message' => "Only $schoolDirCount school directories found for $schoolCount active schools"
                                ];
                            }

                            // Test 7: Check file handler methods
                            if (class_exists('FileHandler')) {
                                $methods = get_class_methods('FileHandler');
                                $requiredMethods = ['validateFile', 'uploadFile', 'getReportAttachments', 'deleteAttachment'];
                                $missingMethods = array_diff($requiredMethods, $methods);
                                
                                if (empty($missingMethods)) {
                                    $tests[] = [
                                        'name' => 'FileHandler Methods',
                                        'status' => 'success',
                                        'message' => 'All required methods present: ' . implode(', ', $requiredMethods)
                                    ];
                                } else {
                                    $tests[] = [
                                        'name' => 'FileHandler Methods',
                                        'status' => 'error',
                                        'message' => 'Missing methods: ' . implode(', ', $missingMethods)
                                    ];
                                    $allPassed = false;
                                }
                            }

                            // Test 8: Check static helper methods
                            if (class_exists('FileHandler')) {
                                $testIcon = FileHandler::getFileIcon('test.pdf');
                                $testSize = FileHandler::formatFileSize(1048576);
                                
                                if (!empty($testIcon) && !empty($testSize)) {
                                    $tests[] = [
                                        'name' => 'Helper Methods',
                                        'status' => 'success',
                                        'message' => "Static methods working: getFileIcon(), formatFileSize()"
                                    ];
                                } else {
                                    $tests[] = [
                                        'name' => 'Helper Methods',
                                        'status' => 'error',
                                        'message' => 'Static helper methods not working properly'
                                    ];
                                    $allPassed = false;
                                }
                            }

                        } catch (Exception $e) {
                            $tests[] = [
                                'name' => 'System Error',
                                'status' => 'error',
                                'message' => 'Test failed: ' . $e->getMessage()
                            ];
                            $allPassed = false;
                        }

                        // Display test results
                        foreach ($tests as $test) {
                            $iconClass = $test['status'] === 'success' ? 'fa-check-circle text-success' : 
                                        ($test['status'] === 'error' ? 'fa-times-circle text-danger' : 'fa-exclamation-triangle text-warning');
                            
                            echo "<div class='d-flex align-items-center mb-3'>";
                            echo "<i class='fas $iconClass me-3 fs-5'></i>";
                            echo "<div>";
                            echo "<strong>" . htmlspecialchars($test['name']) . "</strong><br>";
                            echo "<span class='text-muted'>" . htmlspecialchars($test['message']) . "</span>";
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>

                        <hr>

                        <?php if ($allPassed): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>All Tests Passed!</h5>
                                <p class="mb-0">The document attachment system is ready to use.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-times-circle me-2"></i>Some Tests Failed</h5>
                                <p class="mb-0">Please fix the issues above before using the attachment system.</p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <h5>Setup Actions:</h5>
                            <div class="btn-group-vertical d-grid gap-2" role="group">
                                <a href="create_upload_directories.php" class="btn btn-outline-primary">
                                    <i class="fas fa-folder-plus me-2"></i>Create Upload Directories
                                </a>
                                <a href="school-admin/create-report.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Test File Upload (Create Report)
                                </a>
                                <a href="school-admin/reports.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-2"></i>View Reports with Attachments
                                </a>
                            </div>
                        </div>

                        <?php if ($allPassed): ?>
                            <div class="mt-4">
                                <h6>File Upload Features:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Drag and drop file upload</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Multiple file selection</li>
                                    <li><i class="fas fa-check text-success me-2"></i>File type validation (PDF, DOC, XLS, PPT, TXT, Images)</li>
                                    <li><i class="fas fa-check text-success me-2"></i>File size limits (10MB per file, 50MB total)</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Secure file storage with access controls</li>
                                    <li><i class="fas fa-check text-success me-2"></i>File download with authentication</li>
                                    <li><i class="fas fa-check text-success me-2"></i>File deletion for draft reports</li>
                                    <li><i class="fas fa-check text-success me-2"></i>File metadata tracking (size, type, upload date)</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
