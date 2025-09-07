<?php
/**
 * Dashboard API Endpoint
 */

// Start session and check authentication
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    ResponseHelper::error('Authentication required', 401);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Get global variables
$method = $_SERVER['REQUEST_METHOD'];
$current_language = $GLOBALS['current_language'] ?? 'en';
$db = $GLOBALS['db'] ?? null;

// Check if database connection is available
if (!$db) {
    ResponseHelper::error('Database connection not available', 500);
}

if ($method === 'GET') {
    try {
        // Get dashboard statistics
        $stats = getDashboardStats($db, $user_id);
        
        // Get recent applications
        $recent_applications = getRecentApplications($db, $user_id, $current_language);
        
        // Get upcoming meetings
        $upcoming_meetings = getUpcomingMeetings($db, $user_id, $current_language);
        
        // Get recent notifications
        $recent_notifications = getRecentNotifications($db, $user_id, $current_language);
        
        // Get activity timeline
        $activity_timeline = getActivityTimeline($db, $user_id, $current_language);
        
        ResponseHelper::success([
            'stats' => $stats,
            'recent_applications' => $recent_applications,
            'upcoming_meetings' => $upcoming_meetings,
            'recent_notifications' => $recent_notifications,
            'activity_timeline' => $activity_timeline
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load dashboard data: ' . $e->getMessage(), 500);
    }
}

function getDashboardStats($db, $user_id) {
    $stats = [];
    
    // Certificate applications count
    $query = "SELECT COUNT(*) as total FROM applications WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['applications'] = $stmt->fetch()['total'];
    
    // Job applications count (mock data for now)
    $stats['job_applications'] = 3;
    
    // Scheduled meetings count
    $query = "SELECT COUNT(*) as total FROM meetings WHERE user_id = :user_id AND meeting_date >= CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['meetings'] = $stmt->fetch()['total'];
    
    // Unread notifications count
    $query = "SELECT COUNT(*) as total FROM notifications WHERE user_id = :user_id AND is_read = 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $stats['notifications'] = $stmt->fetch()['total'];
    
    return $stats;
}

function getRecentApplications($db, $user_id, $language) {
    $query = "SELECT a.*, ct.fee, ctt.name as type_name 
              FROM applications a 
              JOIN certificate_types ct ON a.certificate_type_id = ct.id 
              JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id 
              WHERE a.user_id = :user_id AND ctt.language_code = :language 
              ORDER BY a.submitted_date DESC 
              LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
    
    $applications = [];
    while ($row = $stmt->fetch()) {
        $applications[] = [
            'id' => $row['application_number'],
            'type' => $row['type_name'],
            'status' => $row['status'],
            'date' => $row['submitted_date'],
            'fee' => 'RWF ' . number_format($row['fee']),
            'payment_code' => $row['payment_code']
        ];
    }
    
    return $applications;
}

function getUpcomingMeetings($db, $user_id, $language) {
    $query = "SELECT m.*, mtt.name as type_name 
              FROM meetings m 
              JOIN meeting_types mt ON m.meeting_type_id = mt.id 
              JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id 
              WHERE m.user_id = :user_id AND mtt.language_code = :language 
              AND m.meeting_date >= CURDATE() 
              ORDER BY m.meeting_date ASC, m.meeting_time ASC 
              LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
    
    $meetings = [];
    while ($row = $stmt->fetch()) {
        $meetings[] = [
            'id' => $row['meeting_number'],
            'title' => $row['title'],
            'type' => $row['type_name'],
            'date' => $row['meeting_date'],
            'time' => date('g:i A', strtotime($row['meeting_time'])),
            'status' => $row['status'],
            'location' => $row['location']
        ];
    }
    
    return $meetings;
}

function getRecentNotifications($db, $user_id, $language) {
    $query = "SELECT n.*, ntt.category 
              FROM notifications n 
              JOIN notification_types nt ON n.notification_type_id = nt.id 
              JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id 
              WHERE n.user_id = :user_id AND ntt.language_code = :language 
              ORDER BY n.created_at DESC 
              LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
    
    $notifications = [];
    while ($row = $stmt->fetch()) {
        $notifications[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'message' => $row['message'],
            'type' => $row['category'],
            'date' => $row['created_at'],
            'read' => (bool)$row['is_read'],
            'action_required' => (bool)$row['action_required'],
            'action_text' => $row['action_text'],
            'action_url' => $row['action_url']
        ];
    }
    
    return $notifications;
}

function getActivityTimeline($db, $user_id, $language) {
    $activities = [];
    
    // Get recent applications
    $query = "SELECT a.application_number, a.status, a.submitted_date, a.approved_date, a.completed_date, 
                     ctt.name as type_name 
              FROM applications a 
              JOIN certificate_types ct ON a.certificate_type_id = ct.id 
              JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id 
              WHERE a.user_id = :user_id AND ctt.language_code = :language 
              ORDER BY a.submitted_date DESC 
              LIMIT 10";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
    
    while ($row = $stmt->fetch()) {
        $status_text = getStatusText($row['status'], $language);
        $activities[] = [
            'id' => 'app_' . $row['application_number'],
            'type' => 'application',
            'title' => $row['type_name'] . ' Application ' . $status_text,
            'description' => 'Application ' . $row['application_number'] . ' has been ' . strtolower($status_text),
            'date' => $row['completed_date'] ?: $row['approved_date'] ?: $row['submitted_date'],
            'icon' => 'fas fa-file-alt',
            'color' => getStatusColor($row['status'])
        ];
    }
    
    // Get recent meetings
    $query = "SELECT m.meeting_number, m.title, m.meeting_date, m.status, mtt.name as type_name 
              FROM meetings m 
              JOIN meeting_types mt ON m.meeting_type_id = mt.id 
              JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id 
              WHERE m.user_id = :user_id AND mtt.language_code = :language 
              ORDER BY m.meeting_date DESC 
              LIMIT 5";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':language', $language);
    $stmt->execute();
    
    while ($row = $stmt->fetch()) {
        $status_text = getStatusText($row['status'], $language);
        $activities[] = [
            'id' => 'mtg_' . $row['meeting_number'],
            'type' => 'meeting',
            'title' => $row['title'] . ' ' . $status_text,
            'description' => $row['type_name'] . ' scheduled for ' . date('F j, Y', strtotime($row['meeting_date'])),
            'date' => $row['meeting_date'],
            'icon' => 'fas fa-calendar',
            'color' => getStatusColor($row['status'])
        ];
    }
    
    // Sort activities by date
    usort($activities, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    
    return array_slice($activities, 0, 10);
}

function getStatusText($status, $language) {
    $status_map = [
        'en' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'approved' => 'Approved',
            'completed' => 'Completed',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected'
        ],
        'rw' => [
            'pending' => 'Bitegereje',
            'processing' => 'Birakozwe',
            'approved' => 'Byemewe',
            'completed' => 'Byarangiye',
            'confirmed' => 'Byemejwe',
            'cancelled' => 'Byahagaritswe',
            'rejected' => 'Byanze'
        ],
        'fr' => [
            'pending' => 'En attente',
            'processing' => 'En cours',
            'approved' => 'Approuvé',
            'completed' => 'Terminé',
            'confirmed' => 'Confirmé',
            'cancelled' => 'Annulé',
            'rejected' => 'Rejeté'
        ]
    ];
    
    return $status_map[$language][$status] ?? $status;
}

function getStatusColor($status) {
    $color_map = [
        'pending' => 'warning',
        'processing' => 'info',
        'approved' => 'primary',
        'completed' => 'success',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'rejected' => 'danger'
    ];
    
    return $color_map[$status] ?? 'secondary';
}
?>
