<?php
/**
 * School Administration Functions
 * Diocese of Byumba - School Management System
 */

require_once '../config/database.php';

/**
 * School Authentication Class
 */
class SchoolAuth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Authenticate school user
     */
    public function login($username, $password) {
        try {
            $query = "SELECT su.*, s.school_name, s.school_code, s.school_type 
                     FROM school_users su 
                     JOIN schools s ON su.school_id = s.id 
                     WHERE su.username = :username AND su.is_active = 1 AND s.is_active = 1
                     LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                
                // Set session variables
                $_SESSION['school_logged_in'] = true;
                $_SESSION['school_user_id'] = $user['id'];
                $_SESSION['school_id'] = $user['school_id'];
                $_SESSION['school_name'] = $user['school_name'];
                $_SESSION['school_code'] = $user['school_code'];
                $_SESSION['school_type'] = $user['school_type'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                
                // Log activity
                $this->logActivity($user['school_id'], $user['id'], 'login', 'User logged in successfully');
                
                return ['success' => true, 'message' => 'Login successful'];
            }
            
            return ['success' => false, 'message' => 'Invalid username or password'];
        } catch(PDOException $e) {
            error_log("School login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        try {
            $query = "UPDATE school_users SET last_login = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['school_logged_in']) && $_SESSION['school_logged_in'] === true;
    }
    
    /**
     * Get current school user info
     */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'user_id' => $_SESSION['school_user_id'],
                'school_id' => $_SESSION['school_id'],
                'school_name' => $_SESSION['school_name'],
                'school_code' => $_SESSION['school_code'],
                'school_type' => $_SESSION['school_type'],
                'user_name' => $_SESSION['user_name'],
                'user_role' => $_SESSION['user_role'],
                'user_email' => $_SESSION['user_email']
            ];
        }
        return null;
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if ($this->isLoggedIn()) {
            $this->logActivity($_SESSION['school_id'], $_SESSION['school_user_id'], 'logout', 'User logged out');
        }
        
        // Clear session variables
        unset($_SESSION['school_logged_in']);
        unset($_SESSION['school_user_id']);
        unset($_SESSION['school_id']);
        unset($_SESSION['school_name']);
        unset($_SESSION['school_code']);
        unset($_SESSION['school_type']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        unset($_SESSION['user_email']);
        
        session_destroy();
    }
    
    /**
     * Require login
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: index.php?message=session_expired');
            exit;
        }
    }
    
    /**
     * Log activity
     */
    public function logActivity($schoolId, $userId, $activityType, $description) {
        try {
            $query = "INSERT INTO school_activity_log (school_id, school_user_id, activity_type, description, ip_address, user_agent)
                     VALUES (:school_id, :user_id, :activity_type, :description, :ip_address, :user_agent)";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':school_id', $schoolId);
            $stmt->bindValue(':user_id', $userId);
            $stmt->bindValue(':activity_type', $activityType);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':ip_address', $_SERVER['REMOTE_ADDR'] ?? '');
            $stmt->bindValue(':user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
            $stmt->execute();
        } catch(PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
}

/**
 * School Reports Class
 */
class SchoolReports {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get report types
     */
    public function getReportTypes() {
        try {
            $query = "SELECT * FROM report_types WHERE is_active = 1 ORDER BY type_name";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get report types error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get school reports
     */
    public function getSchoolReports($schoolId, $limit = null, $status = null) {
        try {
            $query = "SELECT sr.*, rt.type_name, rt.type_code, su.full_name as submitted_by_name
                     FROM school_reports sr
                     JOIN report_types rt ON sr.report_type_id = rt.id
                     LEFT JOIN school_users su ON sr.submitted_by = su.id
                     WHERE sr.school_id = :school_id";
            
            if ($status) {
                $query .= " AND sr.status = :status";
            }
            
            $query .= " ORDER BY sr.created_at DESC";
            
            if ($limit) {
                $query .= " LIMIT :limit";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':school_id', $schoolId);
            
            if ($status) {
                $stmt->bindParam(':status', $status);
            }
            
            if ($limit) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get school reports error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Generate report number
     */
    public function generateReportNumber($schoolCode, $reportTypeCode) {
        $year = date('Y');
        $month = date('m');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $schoolCode . '-' . $reportTypeCode . '-' . $year . $month . '-' . $random;
    }
    
    /**
     * Create new report
     */
    public function createReport($data) {
        try {
            $reportNumber = $this->generateReportNumber($data['school_code'], $data['report_type_code']);
            
            $query = "INSERT INTO school_reports (school_id, report_type_id, report_number, title, reporting_period, report_data, submitted_by, status) 
                     VALUES (:school_id, :report_type_id, :report_number, :title, :reporting_period, :report_data, :submitted_by, :status)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':school_id', $data['school_id']);
            $stmt->bindParam(':report_type_id', $data['report_type_id']);
            $stmt->bindParam(':report_number', $reportNumber);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':reporting_period', $data['reporting_period']);
            $stmt->bindParam(':report_data', json_encode($data['report_data']));
            $stmt->bindParam(':submitted_by', $data['submitted_by']);
            $stmt->bindParam(':status', $data['status']);
            
            $stmt->execute();
            
            return [
                'success' => true,
                'report_id' => $this->db->lastInsertId(),
                'report_number' => $reportNumber
            ];
        } catch(PDOException $e) {
            error_log("Create report error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create report'];
        }
    }
}

/**
 * Helper Functions
 */

/**
 * Format datetime for display (school admin specific)
 */
function formatSchoolDateTime($datetime, $format = 'M j, Y g:i A') {
    if (!$datetime) return 'N/A';
    return date($format, strtotime($datetime));
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'draft': return 'bg-secondary';
        case 'submitted': return 'bg-primary';
        case 'under_review': return 'bg-warning';
        case 'approved': return 'bg-success';
        case 'rejected': return 'bg-danger';
        case 'requires_revision': return 'bg-info';
        default: return 'bg-secondary';
    }
}

/**
 * Get priority badge class
 */
function getPriorityBadgeClass($priority) {
    switch ($priority) {
        case 'low': return 'bg-light text-dark';
        case 'normal': return 'bg-secondary';
        case 'high': return 'bg-warning';
        case 'urgent': return 'bg-danger';
        default: return 'bg-secondary';
    }
}

// Initialize global objects
$schoolAuth = new SchoolAuth($db);
$schoolReports = new SchoolReports($db);

// Make objects available globally
$GLOBALS['schoolAuth'] = $schoolAuth;
$GLOBALS['schoolReports'] = $schoolReports;
?>
