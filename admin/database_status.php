<?php
/**
 * Database Status Check
 * Diocese of Byumba Admin Panel
 */

// Don't require admin login for this diagnostic page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Status - Diocese of Byumba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-database me-2"></i>Database Status Check</h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $checks = [];
                        
                        // Check 1: MySQL Extension
                        if (extension_loaded('pdo_mysql')) {
                            $checks[] = [
                                'name' => 'PDO MySQL Extension',
                                'status' => 'success',
                                'message' => 'PDO MySQL extension is loaded'
                            ];
                        } else {
                            $checks[] = [
                                'name' => 'PDO MySQL Extension',
                                'status' => 'error',
                                'message' => 'PDO MySQL extension is not loaded'
                            ];
                        }
                        
                        // Check 2: Database Connection
                        try {
                            $host = 'localhost';
                            $db_name = 'diocese_byumba';
                            $username = 'root';
                            $password = '';
                            $charset = 'utf8mb4';
                            
                            $dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
                            $options = [
                                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                PDO::ATTR_EMULATE_PREPARES => false,
                            ];
                            
                            $pdo = new PDO($dsn, $username, $password, $options);
                            
                            $checks[] = [
                                'name' => 'Database Connection',
                                'status' => 'success',
                                'message' => 'Successfully connected to diocese_byumba database'
                            ];
                            
                            // Check 3: Required Tables
                            $required_tables = [
                                'users', 'applications', 'meetings', 'notifications', 
                                'blog_posts', 'parishes', 'admin_activity_log'
                            ];
                            
                            $stmt = $pdo->query("SHOW TABLES");
                            $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            
                            $missing_tables = array_diff($required_tables, $existing_tables);
                            
                            if (empty($missing_tables)) {
                                $checks[] = [
                                    'name' => 'Required Tables',
                                    'status' => 'success',
                                    'message' => 'All required tables are present (' . count($required_tables) . ' tables)'
                                ];
                            } else {
                                $checks[] = [
                                    'name' => 'Required Tables',
                                    'status' => 'warning',
                                    'message' => 'Missing tables: ' . implode(', ', $missing_tables)
                                ];
                            }
                            
                            // Check 4: Sample Data
                            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                            $user_count = $stmt->fetch()['count'];
                            
                            $checks[] = [
                                'name' => 'Sample Data',
                                'status' => $user_count > 0 ? 'success' : 'warning',
                                'message' => "Found $user_count users in the database"
                            ];
                            
                        } catch(PDOException $e) {
                            $error_code = $e->getCode();
                            $error_message = $e->getMessage();
                            
                            if ($error_code == 2002) {
                                $checks[] = [
                                    'name' => 'Database Connection',
                                    'status' => 'error',
                                    'message' => 'MySQL server is not running or not accessible'
                                ];
                            } elseif ($error_code == 1049) {
                                $checks[] = [
                                    'name' => 'Database Connection',
                                    'status' => 'error',
                                    'message' => 'Database "diocese_byumba" does not exist'
                                ];
                            } elseif ($error_code == 1045) {
                                $checks[] = [
                                    'name' => 'Database Connection',
                                    'status' => 'error',
                                    'message' => 'Access denied - check username/password'
                                ];
                            } else {
                                $checks[] = [
                                    'name' => 'Database Connection',
                                    'status' => 'error',
                                    'message' => "Connection failed: $error_message (Code: $error_code)"
                                ];
                            }
                        }
                        
                        // Display results
                        foreach ($checks as $check):
                        ?>
                            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                <div class="me-3">
                                    <?php if ($check['status'] === 'success'): ?>
                                        <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                                    <?php elseif ($check['status'] === 'warning'): ?>
                                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 1.5rem;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($check['name']); ?></h6>
                                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($check['message']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6>Quick Actions:</h6>
                            <div class="d-flex gap-2 flex-wrap">
                                <button onclick="location.reload()" class="btn btn-primary btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i>Refresh Status
                                </button>
                                <a href="../" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-home me-1"></i>Go to Website
                                </a>
                                <a href="index.php" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sign-in-alt me-1"></i>Admin Login
                                </a>
                                <?php if (isset($pdo)): ?>
                                    <a href="test_connection.php" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-tools me-1"></i>Full Test Suite
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-3 bg-info bg-opacity-10 rounded">
                            <h6><i class="fas fa-info-circle me-2"></i>Troubleshooting Tips:</h6>
                            <ul class="mb-0 small">
                                <li><strong>MySQL not running:</strong> Start XAMPP and click "Start" next to MySQL</li>
                                <li><strong>Database missing:</strong> Import <code>database/diocese_byumba.sql</code> via phpMyAdmin</li>
                                <li><strong>Missing tables:</strong> Import <code>database/admin_activity_log.sql</code></li>
                                <li><strong>Access denied:</strong> Check database credentials in <code>config/database.php</code></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
