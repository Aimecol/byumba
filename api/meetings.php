<?php
/**
 * Meetings API Endpoint
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
        // Get query parameters
        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $date_filter = $_GET['date'] ?? 'all';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        // Build query
        $where_conditions = ['m.user_id = :user_id', 'mtt.language_code = :language'];
        $params = [':user_id' => $user_id, ':language' => $current_language];
        
        if ($status !== 'all') {
            $where_conditions[] = 'm.status = :status';
            $params[':status'] = $status;
        }
        
        if ($type !== 'all') {
            $where_conditions[] = 'mt.type_key = :type';
            $params[':type'] = $type;
        }
        
        // Date filtering
        if ($date_filter !== 'all') {
            switch ($date_filter) {
                case 'upcoming':
                    $where_conditions[] = 'm.meeting_date >= CURDATE()';
                    break;
                case 'week':
                    $where_conditions[] = 'm.meeting_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)';
                    break;
                case 'month':
                    $where_conditions[] = 'm.meeting_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 1 MONTH)';
                    break;
                case 'past':
                    $where_conditions[] = 'm.meeting_date < CURDATE()';
                    break;
            }
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total 
                       FROM meetings m 
                       JOIN meeting_types mt ON m.meeting_type_id = mt.id 
                       JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id 
                       $where_clause";
        
        $stmt = $db->prepare($count_query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = $stmt->fetch()['total'];
        
        // Get meetings
        $query = "SELECT m.*, mt.duration_minutes, mt.icon, 
                         mtt.name as type_name, mtt.description 
                  FROM meetings m 
                  JOIN meeting_types mt ON m.meeting_type_id = mt.id 
                  JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id 
                  $where_clause 
                  ORDER BY m.meeting_date DESC, m.meeting_time DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $meetings = [];
        while ($row = $stmt->fetch()) {
            $meetings[] = [
                'id' => $row['meeting_number'],
                'title' => $row['title'],
                'type' => $row['type_name'],
                'type_key' => $row['type_key'] ?? '',
                'description' => $row['description'],
                'meeting_description' => $row['description'],
                'date' => $row['meeting_date'],
                'time' => date('g:i A', strtotime($row['meeting_time'])),
                'time_24' => $row['meeting_time'],
                'duration' => $row['duration_minutes'] . ' minutes',
                'duration_minutes' => $row['duration_minutes'],
                'location' => $row['location'],
                'status' => $row['status'],
                'notes' => $row['notes'],
                'icon' => $row['icon'],
                'created_date' => $row['created_at']
            ];
        }
        
        // Get summary statistics
        $summary = getMeetingsSummary($db, $user_id);
        
        // Get calendar data for current month
        $calendar_data = getCalendarData($db, $user_id, date('Y-m'));
        
        ResponseHelper::success([
            'meetings' => $meetings,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_items' => $total,
                'items_per_page' => $limit
            ],
            'summary' => $summary,
            'calendar' => $calendar_data
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load meetings: ' . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required_fields = ['meeting_type_id', 'title', 'meeting_date', 'meeting_time'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty($input[$field])) {
                ResponseHelper::error("Field '$field' is required", 400);
            }
        }
        
        // Generate meeting number
        $meeting_number = generateUniqueNumber('MTG');
        
        // Insert meeting
        $query = "INSERT INTO meetings (user_id, meeting_type_id, meeting_number, title, description, 
                                      meeting_date, meeting_time, duration_minutes, location, notes) 
                  VALUES (:user_id, :meeting_type_id, :meeting_number, :title, :description, 
                          :meeting_date, :meeting_time, :duration_minutes, :location, :notes)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':meeting_type_id', $input['meeting_type_id']);
        $stmt->bindParam(':meeting_number', $meeting_number);
        $stmt->bindParam(':title', $input['title']);
        $stmt->bindParam(':description', $input['description'] ?? '');
        $stmt->bindParam(':meeting_date', $input['meeting_date']);
        $stmt->bindParam(':meeting_time', $input['meeting_time']);
        $stmt->bindParam(':duration_minutes', $input['duration_minutes'] ?? 60);
        $stmt->bindParam(':location', $input['location'] ?? 'Bishop\'s Office');
        $stmt->bindParam(':notes', $input['notes'] ?? '');
        $stmt->execute();
        
        ResponseHelper::success([
            'meeting_number' => $meeting_number,
            'message' => 'Meeting scheduled successfully'
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to schedule meeting: ' . $e->getMessage(), 500);
    }
}

function getMeetingsSummary($db, $user_id) {
    $summary = [];
    
    // Count by status
    $query = "SELECT status, COUNT(*) as count FROM meetings WHERE user_id = :user_id GROUP BY status";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $status_counts = [
        'pending' => 0,
        'confirmed' => 0,
        'completed' => 0,
        'cancelled' => 0
    ];
    
    while ($row = $stmt->fetch()) {
        $status_counts[$row['status']] = (int)$row['count'];
    }
    
    $summary['by_status'] = $status_counts;
    
    // Upcoming meetings count
    $query = "SELECT COUNT(*) as count FROM meetings 
              WHERE user_id = :user_id AND meeting_date >= CURDATE() 
              AND status IN ('pending', 'confirmed')";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary['upcoming'] = (int)$stmt->fetch()['count'];
    
    // Total meetings
    $summary['total'] = array_sum($status_counts);
    
    return $summary;
}

function getCalendarData($db, $user_id, $month) {
    $query = "SELECT meeting_date, COUNT(*) as count, 
                     GROUP_CONCAT(CONCAT(title, '|', status, '|', TIME_FORMAT(meeting_time, '%H:%i')) SEPARATOR ';;') as meetings
              FROM meetings 
              WHERE user_id = :user_id AND DATE_FORMAT(meeting_date, '%Y-%m') = :month 
              GROUP BY meeting_date";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':month', $month);
    $stmt->execute();
    
    $calendar_data = [];
    while ($row = $stmt->fetch()) {
        $meetings = [];
        if ($row['meetings']) {
            $meeting_strings = explode(';;', $row['meetings']);
            foreach ($meeting_strings as $meeting_string) {
                $parts = explode('|', $meeting_string);
                if (count($parts) >= 3) {
                    $meetings[] = [
                        'title' => $parts[0],
                        'status' => $parts[1],
                        'time' => $parts[2]
                    ];
                }
            }
        }
        
        $calendar_data[$row['meeting_date']] = [
            'count' => (int)$row['count'],
            'meetings' => $meetings
        ];
    }
    
    return $calendar_data;
}
?>
