<?php
/**
 * Test School Administration System
 * Diocese of Byumba - School Management System
 */

require_once 'config/database.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test School Administration System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-school me-2"></i>School Administration System Test</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        $tests = [];
                        $allPassed = true;

                        // Test 1: Database Connection
                        try {
                            if ($db) {
                                $tests[] = [
                                    'name' => 'Database Connection',
                                    'status' => 'success',
                                    'message' => 'Successfully connected to database'
                                ];
                            } else {
                                throw new Exception('Database connection failed');
                            }
                        } catch (Exception $e) {
                            $tests[] = [
                                'name' => 'Database Connection',
                                'status' => 'error',
                                'message' => $e->getMessage()
                            ];
                            $allPassed = false;
                        }

                        // Test 2: Check Required Tables
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
                            $tests[] = [
                                'name' => 'Required Tables',
                                'status' => 'success',
                                'message' => 'All ' . count($requiredTables) . ' required tables exist'
                            ];
                        } else {
                            $tests[] = [
                                'name' => 'Required Tables',
                                'status' => 'error',
                                'message' => 'Missing tables: ' . implode(', ', $missingTables)
                            ];
                            $allPassed = false;
                        }

                        // Test 3: Check Sample Data
                        try {
                            $stmt = $db->query("SELECT COUNT(*) as count FROM schools");
                            $schoolCount = $stmt->fetch()['count'];

                            $stmt = $db->query("SELECT COUNT(*) as count FROM school_users");
                            $userCount = $stmt->fetch()['count'];

                            $stmt = $db->query("SELECT COUNT(*) as count FROM report_types");
                            $typeCount = $stmt->fetch()['count'];

                            if ($schoolCount > 0 && $userCount > 0 && $typeCount > 0) {
                                $tests[] = [
                                    'name' => 'Sample Data',
                                    'status' => 'success',
                                    'message' => "Found $schoolCount schools, $userCount users, $typeCount report types"
                                ];
                            } else {
                                $tests[] = [
                                    'name' => 'Sample Data',
                                    'status' => 'warning',
                                    'message' => 'Some sample data is missing'
                                ];
                            }
                        } catch (PDOException $e) {
                            $tests[] = [
                                'name' => 'Sample Data',
                                'status' => 'error',
                                'message' => 'Error checking sample data: ' . $e->getMessage()
                            ];
                            $allPassed = false;
                        }

                        // Test 4: Check File Structure
                        $requiredFiles = [
                            'school-admin/index.php',
                            'school-admin/dashboard.php',
                            'school-admin/includes/functions.php',
                            'school-admin/assets/css/school-admin.css',
                            'school-admin/api/index.php'
                        ];

                        $missingFiles = [];
                        foreach ($requiredFiles as $file) {
                            if (!file_exists($file)) {
                                $missingFiles[] = $file;
                            }
                        }

                        if (empty($missingFiles)) {
                            $tests[] = [
                                'name' => 'File Structure',
                                'status' => 'success',
                                'message' => 'All required files exist'
                            ];
                        } else {
                            $tests[] = [
                                'name' => 'File Structure',
                                'status' => 'error',
                                'message' => 'Missing files: ' . implode(', ', $missingFiles)
                            ];
                            $allPassed = false;
                        }

                        // Test 5: Test Functions
                        try {
                            require_once 'school-admin/includes/functions.php';
                            
                            // Test if classes can be instantiated
                            $schoolAuth = new SchoolAuth($db);
                            $schoolReports = new SchoolReports($db);
                            
                            $tests[] = [
                                'name' => 'Function Loading',
                                'status' => 'success',
                                'message' => 'All classes and functions loaded successfully'
                            ];
                        } catch (Exception $e) {
                            $tests[] = [
                                'name' => 'Function Loading',
                                'status' => 'error',
                                'message' => 'Error loading functions: ' . $e->getMessage()
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
                                <p class="mb-0">The School Administration System is ready to use.</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-times-circle me-2"></i>Some Tests Failed</h5>
                                <p class="mb-0">Please fix the issues above before using the system.</p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <h5>Quick Links:</h5>
                            <div class="btn-group" role="group">
                                <a href="school-admin/" class="btn btn-primary">
                                    <i class="fas fa-school me-2"></i>School Admin Login
                                </a>
                                <a href="admin/school-reports.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user-shield me-2"></i>Admin Panel
                                </a>
                                <a href="setup_school_admin.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-cog me-2"></i>Setup System
                                </a>
                            </div>
                        </div>

                        <?php if ($allPassed): ?>
                            <div class="mt-4">
                                <h6>Sample Login Credentials:</h6>
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
