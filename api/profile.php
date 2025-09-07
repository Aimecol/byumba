<?php
/**
 * Profile API Endpoint
 */

// Start session and check authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    ResponseHelper::error('Authentication required', 401);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

if ($method === 'GET') {
    try {
        // Get user profile data
        $profile = getUserProfile($db, $user_id);
        
        // Get user parish membership
        $parish_membership = getUserParishMembership($db, $user_id);
        
        // Get user statistics
        $user_stats = getUserStatistics($db, $user_id);
        
        ResponseHelper::success([
            'profile' => $profile,
            'parish_membership' => $parish_membership,
            'statistics' => $user_stats
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load profile data: ' . $e->getMessage(), 500);
    }
}

if ($method === 'PUT') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate input
        $allowed_fields = ['first_name', 'last_name', 'phone', 'date_of_birth', 'place_of_birth', 'gender', 'address', 'preferred_language'];
        $update_fields = [];
        $params = [];
        
        foreach ($allowed_fields as $field) {
            if (isset($input[$field])) {
                $update_fields[] = "$field = :$field";
                $params[":$field"] = $input[$field];
            }
        }
        
        if (empty($update_fields)) {
            ResponseHelper::error('No valid fields to update', 400);
        }
        
        // Update user profile
        $query = "UPDATE users SET " . implode(', ', $update_fields) . ", updated_at = NOW() WHERE id = :user_id";
        $params[':user_id'] = $user_id;
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        ResponseHelper::success(['message' => 'Profile updated successfully']);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to update profile: ' . $e->getMessage(), 500);
    }
}

function getUserProfile($db, $user_id) {
    $query = "SELECT id, first_name, last_name, email, phone, national_id, date_of_birth, 
                     place_of_birth, gender, address, profile_picture, preferred_language, 
                     email_verified, phone_verified, created_at, updated_at 
              FROM users WHERE id = :user_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $profile = $stmt->fetch();
    
    if (!$profile) {
        throw new Exception('User profile not found');
    }
    
    // Format dates
    if ($profile['date_of_birth']) {
        $profile['date_of_birth'] = date('Y-m-d', strtotime($profile['date_of_birth']));
    }
    
    if ($profile['created_at']) {
        $profile['member_since'] = date('F Y', strtotime($profile['created_at']));
    }
    
    return $profile;
}

function getUserParishMembership($db, $user_id) {
    $query = "SELECT upm.*, p.name as parish_name, p.location 
              FROM user_parish_membership upm 
              JOIN parishes p ON upm.parish_id = p.id 
              WHERE upm.user_id = :user_id AND upm.is_active = 1 
              ORDER BY upm.created_at DESC 
              LIMIT 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    return $stmt->fetch();
}

function getUserStatistics($db, $user_id) {
    $stats = [];
    
    // Total applications
    $query = "SELECT COUNT(*) as total FROM applications WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['total_applications'] = $stmt->fetch()['total'];
    
    // Completed applications
    $query = "SELECT COUNT(*) as total FROM applications WHERE user_id = :user_id AND status = 'completed'";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['completed_applications'] = $stmt->fetch()['total'];
    
    // Total meetings
    $query = "SELECT COUNT(*) as total FROM meetings WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['total_meetings'] = $stmt->fetch()['total'];
    
    // Upcoming meetings
    $query = "SELECT COUNT(*) as total FROM meetings WHERE user_id = :user_id AND meeting_date >= CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['upcoming_meetings'] = $stmt->fetch()['total'];
    
    return $stats;
}
?>
