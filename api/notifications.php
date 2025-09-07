<?php
/**
 * Notifications API Endpoint
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

if ($method === 'GET') {
    try {
        // Get query parameters
        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $priority = $_GET['priority'] ?? 'all';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        // Build query
        $where_conditions = ['n.user_id = :user_id', 'ntt.language_code = :language'];
        $params = [':user_id' => $user_id, ':language' => $current_language];
        
        if ($status === 'unread') {
            $where_conditions[] = 'n.is_read = 0';
        } elseif ($status === 'read') {
            $where_conditions[] = 'n.is_read = 1';
        }
        
        if ($type !== 'all') {
            $where_conditions[] = 'nt.type_key = :type';
            $params[':type'] = $type;
        }
        
        if ($priority !== 'all') {
            $where_conditions[] = 'nt.priority = :priority';
            $params[':priority'] = $priority;
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total 
                       FROM notifications n 
                       JOIN notification_types nt ON n.notification_type_id = nt.id 
                       JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id 
                       $where_clause";
        
        $stmt = $db->prepare($count_query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = $stmt->fetch()['total'];
        
        // Get notifications
        $query = "SELECT n.*, nt.type_key, nt.icon, nt.priority, ntt.category 
                  FROM notifications n 
                  JOIN notification_types nt ON n.notification_type_id = nt.id 
                  JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id 
                  $where_clause 
                  ORDER BY n.created_at DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $notifications = [];
        while ($row = $stmt->fetch()) {
            $notifications[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'message' => $row['message'],
                'type' => $row['type_key'],
                'category' => $row['category'],
                'priority' => $row['priority'],
                'icon' => $row['icon'],
                'action_required' => (bool)$row['action_required'],
                'action_text' => $row['action_text'],
                'action_url' => $row['action_url'],
                'is_read' => (bool)$row['is_read'],
                'read_at' => $row['read_at'],
                'created_at' => $row['created_at']
            ];
        }
        
        // Get summary statistics
        $summary = getNotificationsSummary($db, $user_id);
        
        ResponseHelper::success([
            'notifications' => $notifications,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_items' => $total,
                'items_per_page' => $limit
            ],
            'summary' => $summary
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load notifications: ' . $e->getMessage(), 500);
    }
}

if ($method === 'PUT') {
    try {
        $notification_id = $path_parts[1] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$notification_id) {
            ResponseHelper::error('Notification ID is required', 400);
        }
        
        // Mark as read
        if (isset($input['mark_read']) && $input['mark_read']) {
            $query = "UPDATE notifications SET is_read = 1, read_at = NOW() 
                      WHERE id = :id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            ResponseHelper::success(['message' => 'Notification marked as read']);
        }
        
        // Mark all as read
        if (isset($input['mark_all_read']) && $input['mark_all_read']) {
            $query = "UPDATE notifications SET is_read = 1, read_at = NOW() 
                      WHERE user_id = :user_id AND is_read = 0";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            $affected_rows = $stmt->rowCount();
            ResponseHelper::success(['message' => "$affected_rows notifications marked as read"]);
        }
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to update notification: ' . $e->getMessage(), 500);
    }
}

if ($method === 'DELETE') {
    try {
        $notification_id = $path_parts[1] ?? null;
        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($notification_id) {
            // Delete single notification
            $query = "DELETE FROM notifications WHERE id = :id AND user_id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            ResponseHelper::success(['message' => 'Notification deleted']);
        } elseif (isset($input['notification_ids']) && is_array($input['notification_ids'])) {
            // Delete multiple notifications
            $placeholders = str_repeat('?,', count($input['notification_ids']) - 1) . '?';
            $query = "DELETE FROM notifications WHERE id IN ($placeholders) AND user_id = ?";
            $stmt = $db->prepare($query);
            $params = array_merge($input['notification_ids'], [$user_id]);
            $stmt->execute($params);
            
            $affected_rows = $stmt->rowCount();
            ResponseHelper::success(['message' => "$affected_rows notifications deleted"]);
        } else {
            ResponseHelper::error('Notification ID(s) required', 400);
        }
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to delete notification: ' . $e->getMessage(), 500);
    }
}

function getNotificationsSummary($db, $user_id) {
    $summary = [];
    
    // Unread count
    $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND is_read = 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary['unread'] = (int)$stmt->fetch()['count'];
    
    // Today's notifications count
    $query = "SELECT COUNT(*) as count FROM notifications 
              WHERE user_id = :user_id AND DATE(created_at) = CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary['today'] = (int)$stmt->fetch()['count'];
    
    // High priority count
    $query = "SELECT COUNT(*) as count FROM notifications n 
              JOIN notification_types nt ON n.notification_type_id = nt.id 
              WHERE n.user_id = :user_id AND nt.priority = 'high' AND n.is_read = 0";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary['high_priority'] = (int)$stmt->fetch()['count'];
    
    // Total count
    $query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary['total'] = (int)$stmt->fetch()['count'];
    
    return $summary;
}
?>
