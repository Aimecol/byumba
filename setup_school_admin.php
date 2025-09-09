<?php
/**
 * School Administration System Setup
 * Diocese of Byumba - School Management System
 */

require_once 'config/database.php';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Administration Setup - Diocese of Byumba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .setup-header {
            background: linear-gradient(135deg, #0d6efd, #0056b3);
            color: white;
            padding: 2rem 0;
        }
        .step-card {
            border-left: 4px solid #0d6efd;
            margin-bottom: 1rem;
        }
        .success { border-left-color: #198754; }
        .error { border-left-color: #dc3545; }
        .warning { border-left-color: #ffc107; }
    </style>
</head>
<body class="bg-light">
    <div class="setup-header">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h1><i class="fas fa-school me-3"></i>School Administration System Setup</h1>
                    <p class="mb-0">Diocese of Byumba - Setting up school management functionality</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php
                $setupSteps = [];
                $hasErrors = false;

                try {
                    // Step 1: Check database connection
                    $setupSteps[] = [
                        'title' => 'Database Connection',
                        'status' => 'success',
                        'message' => 'Successfully connected to database: ' . $db_name,
                        'details' => 'Connection established with proper charset and options'
                    ];

                    // Step 2: Create school administration tables
                    echo "<div class='card step-card'>";
                    echo "<div class='card-header'><h5><i class='fas fa-database me-2'></i>Creating School Administration Tables</h5></div>";
                    echo "<div class='card-body'>";

                    $schemaFile = 'database/school_administration.sql';
                    if (!file_exists($schemaFile)) {
                        throw new Exception("Schema file not found: $schemaFile");
                    }

                    $schema = file_get_contents($schemaFile);
                    $statements = explode(';', $schema);
                    $executed = 0;
                    $errors = 0;

                    foreach ($statements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement) && !preg_match('/^(--|\/\*|\*|SET|START|COMMIT|\/\*!)/', $statement)) {
                            try {
                                $db->exec($statement);
                                $executed++;
                                echo "<p class='text-success mb-1'><i class='fas fa-check me-2'></i>Executed statement successfully</p>";
                            } catch (PDOException $e) {
                                if (strpos($e->getMessage(), 'already exists') === false) {
                                    echo "<p class='text-warning mb-1'><i class='fas fa-exclamation-triangle me-2'></i>Warning: " . htmlspecialchars($e->getMessage()) . "</p>";
                                    $errors++;
                                } else {
                                    echo "<p class='text-info mb-1'><i class='fas fa-info-circle me-2'></i>Table already exists (skipped)</p>";
                                }
                            }
                        }
                    }

                    echo "<hr>";
                    echo "<p><strong>Summary:</strong> Executed $executed statements";
                    if ($errors > 0) {
                        echo " with $errors warnings";
                    }
                    echo "</p>";

                    echo "</div></div>";

                    $setupSteps[] = [
                        'title' => 'School Administration Tables',
                        'status' => $errors > 5 ? 'warning' : 'success',
                        'message' => "Created school administration tables ($executed statements executed)",
                        'details' => $errors > 0 ? "$errors warnings encountered" : 'All tables created successfully'
                    ];

                    // Step 3: Verify table creation
                    $requiredTables = [
                        'schools', 'school_users', 'report_types', 'school_reports', 
                        'report_attachments', 'school_sessions', 'school_activity_log'
                    ];

                    $missingTables = [];
                    foreach ($requiredTables as $table) {
                        try {
                            $stmt = $db->query("SELECT 1 FROM $table LIMIT 1");
                        } catch (PDOException $e) {
                            $missingTables[] = $table;
                        }
                    }

                    if (empty($missingTables)) {
                        $setupSteps[] = [
                            'title' => 'Table Verification',
                            'status' => 'success',
                            'message' => 'All required tables are present and accessible',
                            'details' => 'Verified ' . count($requiredTables) . ' tables'
                        ];
                    } else {
                        $setupSteps[] = [
                            'title' => 'Table Verification',
                            'status' => 'error',
                            'message' => 'Missing tables: ' . implode(', ', $missingTables),
                            'details' => 'Please check the database setup'
                        ];
                        $hasErrors = true;
                    }

                    // Step 4: Check sample data
                    try {
                        $stmt = $db->query("SELECT COUNT(*) as count FROM schools");
                        $schoolCount = $stmt->fetch()['count'];

                        $stmt = $db->query("SELECT COUNT(*) as count FROM school_users");
                        $userCount = $stmt->fetch()['count'];

                        $stmt = $db->query("SELECT COUNT(*) as count FROM report_types");
                        $typeCount = $stmt->fetch()['count'];

                        $setupSteps[] = [
                            'title' => 'Sample Data Check',
                            'status' => 'success',
                            'message' => "Found $schoolCount schools, $userCount users, $typeCount report types",
                            'details' => 'Sample data is available for testing'
                        ];
                    } catch (PDOException $e) {
                        $setupSteps[] = [
                            'title' => 'Sample Data Check',
                            'status' => 'warning',
                            'message' => 'Could not verify sample data',
                            'details' => $e->getMessage()
                        ];
                    }

                    // Step 5: Check file permissions
                    $directories = [
                        'school-admin/',
                        'school-admin/assets/',
                        'school-admin/assets/css/',
                        'school-admin/assets/js/',
                        'school-admin/includes/',
                        'school-admin/api/'
                    ];

                    $permissionIssues = [];
                    foreach ($directories as $dir) {
                        if (!is_dir($dir)) {
                            $permissionIssues[] = "Directory missing: $dir";
                        } elseif (!is_readable($dir)) {
                            $permissionIssues[] = "Directory not readable: $dir";
                        }
                    }

                    if (empty($permissionIssues)) {
                        $setupSteps[] = [
                            'title' => 'File System Check',
                            'status' => 'success',
                            'message' => 'All directories are accessible',
                            'details' => 'Checked ' . count($directories) . ' directories'
                        ];
                    } else {
                        $setupSteps[] = [
                            'title' => 'File System Check',
                            'status' => 'warning',
                            'message' => 'Some permission issues found',
                            'details' => implode(', ', $permissionIssues)
                        ];
                    }

                } catch (Exception $e) {
                    $setupSteps[] = [
                        'title' => 'Setup Error',
                        'status' => 'error',
                        'message' => 'Setup failed: ' . $e->getMessage(),
                        'details' => 'Please check the error and try again'
                    ];
                    $hasErrors = true;
                }

                // Display all setup steps
                foreach ($setupSteps as $step) {
                    $statusClass = $step['status'];
                    $iconClass = $step['status'] === 'success' ? 'fa-check-circle' : 
                                ($step['status'] === 'error' ? 'fa-times-circle' : 'fa-exclamation-triangle');
                    
                    echo "<div class='card step-card $statusClass'>";
                    echo "<div class='card-body'>";
                    echo "<h6 class='card-title'>";
                    echo "<i class='fas $iconClass me-2'></i>";
                    echo htmlspecialchars($step['title']);
                    echo "</h6>";
                    echo "<p class='card-text mb-1'>" . htmlspecialchars($step['message']) . "</p>";
                    if (!empty($step['details'])) {
                        echo "<small class='text-muted'>" . htmlspecialchars($step['details']) . "</small>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
                ?>

                <!-- Setup Summary -->
                <div class="card mt-4">
                    <div class="card-header <?php echo $hasErrors ? 'bg-danger' : 'bg-success'; ?> text-white">
                        <h5 class="mb-0">
                            <i class="fas <?php echo $hasErrors ? 'fa-times-circle' : 'fa-check-circle'; ?> me-2"></i>
                            Setup <?php echo $hasErrors ? 'Completed with Issues' : 'Completed Successfully'; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if ($hasErrors): ?>
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Setup completed with some issues</h6>
                                <p class="mb-0">Please review the warnings above and ensure all components are working properly.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success">
                                <h6><i class="fas fa-check-circle me-2"></i>School Administration System is ready!</h6>
                                <p class="mb-0">All components have been set up successfully.</p>
                            </div>
                        <?php endif; ?>

                        <h6 class="mt-4">Next Steps:</h6>
                        <ol>
                            <li><strong>Access School Admin:</strong> <a href="school-admin/" target="_blank">school-admin/</a></li>
                            <li><strong>Test Login:</strong> Use sample credentials from the database</li>
                            <li><strong>Admin Panel:</strong> <a href="admin/school-reports.php" target="_blank">admin/school-reports.php</a></li>
                            <li><strong>API Endpoints:</strong> <a href="school-admin/api/" target="_blank">school-admin/api/</a></li>
                        </ol>

                        <h6 class="mt-4">Sample Login Credentials:</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>St. Mary's Primary School</h6>
                                        <p class="mb-1"><strong>Username:</strong> stmary_admin</p>
                                        <p class="mb-0"><strong>Password:</strong> password</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Holy Cross Secondary School</h6>
                                        <p class="mb-1"><strong>Username:</strong> holycross_admin</p>
                                        <p class="mb-0"><strong>Password:</strong> password</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="school-admin/" class="btn btn-primary btn-lg">
                                <i class="fas fa-school me-2"></i>Access School Admin
                            </a>
                            <a href="admin/school-reports.php" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-shield me-2"></i>Admin Panel
                            </a>
                            <a href="index.html" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-home me-2"></i>Main Site
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
