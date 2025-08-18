<?php
/**
 * Admin Authentication System
 * Diocese of Byumba Admin Panel
 */

require_once '../config/database.php';

class AdminAuth {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Authenticate admin user
     */
    public function login($email, $password) {
        try {
            // For now, we'll use a simple admin check
            // In production, you should have a separate admin users table
            $query = "SELECT id, first_name, last_name, email, password_hash 
                     FROM users 
                     WHERE email = :email 
                     AND email IN ('admin@diocesebyumba.rw', 'bishop@diocesebyumba.rw', 'secretary@diocesebyumba.rw')
                     LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Set admin session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_role'] = $this->getAdminRole($user['email']);
                
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Admin login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get admin role based on email
     */
    private function getAdminRole($email) {
        if ($email === 'bishop@diocesebyumba.rw') {
            return 'bishop';
        } elseif ($email === 'admin@diocesebyumba.rw') {
            return 'admin';
        } else {
            return 'secretary';
        }
    }
    
    /**
     * Check if user is logged in as admin
     */
    public function isLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Get current admin user info
     */
    public function getCurrentAdmin() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'],
                'name' => $_SESSION['admin_name'],
                'email' => $_SESSION['admin_email'],
                'role' => $_SESSION['admin_role']
            ];
        }
        return null;
    }
    
    /**
     * Check if admin has specific permission
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $role = $_SESSION['admin_role'];
        
        // Bishop has all permissions
        if ($role === 'bishop') {
            return true;
        }
        
        // Admin has most permissions
        if ($role === 'admin') {
            $admin_permissions = [
                'view_dashboard', 'manage_users', 'manage_applications', 
                'manage_meetings', 'manage_jobs', 'manage_blog', 
                'manage_parishes', 'manage_notifications', 'view_reports'
            ];
            return in_array($permission, $admin_permissions);
        }
        
        // Secretary has limited permissions
        if ($role === 'secretary') {
            $secretary_permissions = [
                'view_dashboard', 'view_users', 'manage_applications', 
                'manage_meetings', 'view_jobs', 'manage_blog'
            ];
            return in_array($permission, $secretary_permissions);
        }
        
        return false;
    }
    
    /**
     * Logout admin user
     */
    public function logout() {
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_role']);
        
        session_destroy();
    }
    
    /**
     * Require admin login
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }
    }
    
    /**
     * Require specific permission
     */
    public function requirePermission($permission) {
        $this->requireLogin();
        
        if (!$this->hasPermission($permission)) {
            header('Location: dashboard.php?error=insufficient_permissions');
            exit;
        }
    }
}

// Initialize admin auth
$adminAuth = new AdminAuth($db);

/**
 * Helper function to check if admin is logged in
 */
function isAdminLoggedIn() {
    global $adminAuth;
    return $adminAuth->isLoggedIn();
}

/**
 * Helper function to get current admin
 */
function getCurrentAdmin() {
    global $adminAuth;
    return $adminAuth->getCurrentAdmin();
}

/**
 * Helper function to check permission
 */
function hasPermission($permission) {
    global $adminAuth;
    return $adminAuth->hasPermission($permission);
}

/**
 * Helper function to require login
 */
function requireAdminLogin() {
    global $adminAuth;
    $adminAuth->requireLogin();
}

/**
 * Helper function to require permission
 */
function requirePermission($permission) {
    global $adminAuth;
    $adminAuth->requirePermission($permission);
}
?>
