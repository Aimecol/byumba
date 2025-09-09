<?php
/**
 * School Administration API
 * Diocese of Byumba - School Management System
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
require_once '../../config/database.php';
require_once '../includes/functions.php';

// Get endpoint and method
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Response helper
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Authentication check for protected endpoints
function requireAuth() {
    global $schoolAuth;
    if (!$schoolAuth->isLoggedIn()) {
        jsonResponse([
            'success' => false,
            'message' => 'Authentication required'
        ], 401);
    }
}

// Route requests
try {
    switch ($endpoint) {
        case 'auth':
            handleAuth($method);
            break;
            
        case 'dashboard':
            requireAuth();
            handleDashboard($method);
            break;
            
        case 'reports':
            requireAuth();
            handleReports($method);
            break;
            
        case 'report-types':
            requireAuth();
            handleReportTypes($method);
            break;
            
        default:
            jsonResponse([
                'success' => false,
                'message' => 'Endpoint not found'
            ], 404);
    }
} catch (Exception $e) {
    error_log("School API error: " . $e->getMessage());
    jsonResponse([
        'success' => false,
        'message' => 'Internal server error'
    ], 500);
}

/**
 * Handle authentication endpoints
 */
function handleAuth($method) {
    global $schoolAuth;
    
    switch ($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? '';
            
            if ($action === 'login') {
                $username = $input['username'] ?? '';
                $password = $input['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    jsonResponse([
                        'success' => false,
                        'message' => 'Username and password are required'
                    ], 400);
                }
                
                $result = $schoolAuth->login($username, $password);
                
                if ($result['success']) {
                    jsonResponse([
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => $schoolAuth->getCurrentUser()
                    ]);
                } else {
                    jsonResponse([
                        'success' => false,
                        'message' => $result['message']
                    ], 401);
                }
            } elseif ($action === 'logout') {
                $schoolAuth->logout();
                jsonResponse([
                    'success' => true,
                    'message' => 'Logout successful'
                ]);
            } else {
                jsonResponse([
                    'success' => false,
                    'message' => 'Invalid action'
                ], 400);
            }
            break;
            
        case 'GET':
            if ($schoolAuth->isLoggedIn()) {
                jsonResponse([
                    'success' => true,
                    'authenticated' => true,
                    'user' => $schoolAuth->getCurrentUser()
                ]);
            } else {
                jsonResponse([
                    'success' => true,
                    'authenticated' => false
                ]);
            }
            break;
            
        default:
            jsonResponse([
                'success' => false,
                'message' => 'Method not allowed'
            ], 405);
    }
}

/**
 * Handle dashboard endpoints
 */
function handleDashboard($method) {
    global $db, $schoolAuth;
    
    if ($method !== 'GET') {
        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed'
        ], 405);
    }
    
    $currentUser = $schoolAuth->getCurrentUser();
    $schoolId = $currentUser['school_id'];
    
    try {
        // Get statistics
        $stats = [];
        
        // Total reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $schoolId);
        $stmt->execute();
        $stats['total_reports'] = $stmt->fetch()['total'];
        
        // Pending reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id AND status IN ('draft', 'submitted')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $schoolId);
        $stmt->execute();
        $stats['pending_reports'] = $stmt->fetch()['total'];
        
        // Approved reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id AND status = 'approved'";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $schoolId);
        $stmt->execute();
        $stats['approved_reports'] = $stmt->fetch()['total'];
        
        // Monthly reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $schoolId);
        $stmt->execute();
        $stats['monthly_reports'] = $stmt->fetch()['total'];
        
        // Recent reports
        $query = "SELECT sr.*, rt.type_name, rt.type_code 
                 FROM school_reports sr
                 JOIN report_types rt ON sr.report_type_id = rt.id
                 WHERE sr.school_id = :school_id
                 ORDER BY sr.created_at DESC
                 LIMIT 5";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':school_id', $schoolId);
        $stmt->execute();
        $recentReports = $stmt->fetchAll();
        
        jsonResponse([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'recent_reports' => $recentReports,
                'school' => $currentUser
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Dashboard API error: " . $e->getMessage());
        jsonResponse([
            'success' => false,
            'message' => 'Failed to load dashboard data'
        ], 500);
    }
}

/**
 * Handle reports endpoints
 */
function handleReports($method) {
    global $db, $schoolAuth, $schoolReports;
    
    $currentUser = $schoolAuth->getCurrentUser();
    $schoolId = $currentUser['school_id'];
    
    switch ($method) {
        case 'GET':
            $reportId = $_GET['id'] ?? null;
            
            if ($reportId) {
                // Get specific report
                try {
                    $query = "SELECT sr.*, rt.type_name, rt.type_code, su.full_name as submitted_by_name
                             FROM school_reports sr
                             JOIN report_types rt ON sr.report_type_id = rt.id
                             LEFT JOIN school_users su ON sr.submitted_by = su.id
                             WHERE sr.id = :id AND sr.school_id = :school_id";
                    
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':id', $reportId);
                    $stmt->bindParam(':school_id', $schoolId);
                    $stmt->execute();
                    
                    $report = $stmt->fetch();
                    
                    if ($report) {
                        // Decode report data
                        $report['report_data'] = json_decode($report['report_data'], true);
                        
                        jsonResponse([
                            'success' => true,
                            'data' => $report
                        ]);
                    } else {
                        jsonResponse([
                            'success' => false,
                            'message' => 'Report not found'
                        ], 404);
                    }
                } catch (PDOException $e) {
                    error_log("Get report API error: " . $e->getMessage());
                    jsonResponse([
                        'success' => false,
                        'message' => 'Failed to load report'
                    ], 500);
                }
            } else {
                // Get reports list with filters
                $status = $_GET['status'] ?? '';
                $type = $_GET['type'] ?? '';
                $search = $_GET['search'] ?? '';
                $page = max(1, intval($_GET['page'] ?? 1));
                $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
                $offset = ($page - 1) * $limit;
                
                $reports = $schoolReports->getSchoolReports($schoolId, null, $status);
                
                jsonResponse([
                    'success' => true,
                    'data' => [
                        'reports' => $reports,
                        'pagination' => [
                            'page' => $page,
                            'limit' => $limit,
                            'total' => count($reports)
                        ]
                    ]
                ]);
            }
            break;
            
        case 'POST':
            // Create new report
            $input = json_decode(file_get_contents('php://input'), true);
            
            $reportTypeId = intval($input['report_type_id'] ?? 0);
            $title = trim($input['title'] ?? '');
            $reportingPeriod = trim($input['reporting_period'] ?? '');
            $reportData = $input['report_data'] ?? [];
            $status = $input['status'] ?? 'draft';
            $priority = $input['priority'] ?? 'normal';
            
            if (!$reportTypeId || !$title) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Report type and title are required'
                ], 400);
            }
            
            // Get report type
            $reportTypes = $schoolReports->getReportTypes();
            $reportType = null;
            foreach ($reportTypes as $type) {
                if ($type['id'] == $reportTypeId) {
                    $reportType = $type;
                    break;
                }
            }
            
            if (!$reportType) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Invalid report type'
                ], 400);
            }
            
            $createData = [
                'school_id' => $schoolId,
                'school_code' => $currentUser['school_code'],
                'report_type_id' => $reportTypeId,
                'report_type_code' => $reportType['type_code'],
                'title' => $title,
                'reporting_period' => $reportingPeriod,
                'report_data' => $reportData,
                'submitted_by' => $currentUser['user_id'],
                'status' => $status
            ];
            
            $result = $schoolReports->createReport($createData);
            
            if ($result['success']) {
                jsonResponse([
                    'success' => true,
                    'message' => 'Report created successfully',
                    'data' => [
                        'report_id' => $result['report_id'],
                        'report_number' => $result['report_number']
                    ]
                ]);
            } else {
                jsonResponse([
                    'success' => false,
                    'message' => $result['message'] ?? 'Failed to create report'
                ], 500);
            }
            break;
            
        default:
            jsonResponse([
                'success' => false,
                'message' => 'Method not allowed'
            ], 405);
    }
}

/**
 * Handle report types endpoints
 */
function handleReportTypes($method) {
    global $schoolReports;
    
    if ($method !== 'GET') {
        jsonResponse([
            'success' => false,
            'message' => 'Method not allowed'
        ], 405);
    }
    
    $reportTypes = $schoolReports->getReportTypes();
    
    jsonResponse([
        'success' => true,
        'data' => $reportTypes
    ]);
}
?>
