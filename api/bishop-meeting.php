<?php
/**
 * Bishop Meeting Request Handler
 * Diocese of Byumba - Handle bishop meeting form submissions
 */

require_once '../config/database.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Start session conditionally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current user - use authenticated user or fallback to user ID 1
$user_id = $_SESSION['user_id'] ?? 1;
$is_authenticated = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

$method = $_SERVER['REQUEST_METHOD'];

// GET - Retrieve meeting requests
if ($method === 'GET') {
    try {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 10;
        $offset = ($page - 1) * $limit;
        
        $status_filter = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Build query with filters
        $where_conditions = ['m.user_id = :user_id'];
        $params = [':user_id' => $user_id];
        
        if (!empty($status_filter)) {
            $where_conditions[] = 'm.status = :status';
            $params[':status'] = $status_filter;
        }
        
        if (!empty($search)) {
            $where_conditions[] = '(m.first_name LIKE :search OR m.last_name LIKE :search OR m.email LIKE :search OR m.purpose LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM meetings m $where_clause";
        $count_stmt = $db->prepare($count_query);
        $count_stmt->execute($params);
        $total_items = $count_stmt->fetch()['total'];
        
        // Get meeting requests with type information
        $query = "SELECT 
                    m.id,
                    m.meeting_number,
                    m.first_name,
                    m.last_name,
                    m.email,
                    m.phone,
                    m.parish,
                    m.purpose,
                    m.status,
                    m.meeting_date,
                    m.meeting_time,
                    m.location,
                    m.notes,
                    m.created_at,
                    m.updated_at,
                    mt.type_key,
                    mtt.name as type_name,
                    mt.duration_minutes,
                    mt.icon
                  FROM meetings m
                  LEFT JOIN meeting_types mt ON m.meeting_type_id = mt.id
                  LEFT JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id 
                    AND mtt.language_code = 'en'
                  $where_clause
                  ORDER BY m.created_at DESC
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
                'meeting_id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'full_name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'parish' => $row['parish'],
                'purpose' => $row['purpose'],
                'type' => $row['type_name'] ?? 'General Meeting',
                'type_key' => $row['type_key'] ?? 'other',
                'status' => $row['status'],
                'meeting_date' => $row['meeting_date'],
                'meeting_time' => $row['meeting_time'] ? date('g:i A', strtotime($row['meeting_time'])) : null,
                'location' => $row['location'],
                'duration' => $row['duration_minutes'] ? $row['duration_minutes'] . ' minutes' : '30 minutes',
                'notes' => $row['notes'],
                'icon' => $row['icon'] ?? 'fa-calendar',
                'submitted_date' => $row['created_at'],
                'last_updated' => $row['updated_at']
            ];
        }
        
        // Get summary statistics
        $summary_query = "SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
                            SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
                            SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
                          FROM meetings 
                          WHERE user_id = :user_id";
        
        $summary_stmt = $db->prepare($summary_query);
        $summary_stmt->execute([':user_id' => $user_id]);
        $summary = $summary_stmt->fetch();
        
        ResponseHelper::success([
            'meetings' => $meetings,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total_items / $limit),
                'total_items' => (int)$total_items,
                'items_per_page' => $limit
            ],
            'summary' => [
                'total' => (int)$summary['total'],
                'by_status' => [
                    'submitted' => (int)$summary['submitted'],
                    'reviewed' => (int)$summary['reviewed'],
                    'scheduled' => (int)$summary['scheduled'],
                    'confirmed' => (int)$summary['confirmed'],
                    'completed' => (int)$summary['completed'],
                    'cancelled' => (int)$summary['cancelled'],
                    'rejected' => (int)$summary['rejected']
                ]
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error in bishop-meeting.php: " . $e->getMessage());
        ResponseHelper::error('Database error occurred', 500);
    } catch (Exception $e) {
        error_log("General error in bishop-meeting.php: " . $e->getMessage());
        ResponseHelper::error('An error occurred while retrieving meetings', 500);
    }
}

// POST - Submit new meeting request
if ($method === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ResponseHelper::error('Invalid JSON data', 400);
        }
        
        // Validate required fields
        $required_fields = ['firstName', 'lastName', 'email', 'phone', 'meetingType', 'purpose'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                ResponseHelper::error("Field '$field' is required", 400);
            }
        }
        
        // Validate email format
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Invalid email format', 400);
        }
        
        // Validate phone format (basic validation)
        $phone = preg_replace('/[^0-9+]/', '', $input['phone']);
        if (strlen($phone) < 10) {
            ResponseHelper::error('Invalid phone number format', 400);
        }
        
        // Get meeting type ID from type key
        $type_query = "SELECT id FROM meeting_types WHERE type_key = :type_key AND is_active = 1";
        $type_stmt = $db->prepare($type_query);
        $type_stmt->execute([':type_key' => $input['meetingType']]);
        $meeting_type = $type_stmt->fetch();
        
        if (!$meeting_type) {
            ResponseHelper::error('Invalid meeting type selected', 400);
        }
        
        // Generate unique meeting request number
        $meeting_number = generateUniqueNumber('REQ');
        
        // Insert meeting request
        $query = "INSERT INTO meetings (
                    user_id, meeting_type_id, meeting_number, 
                    first_name, last_name, email, phone, parish, purpose,
                    status, notes
                  ) VALUES (
                    :user_id, :meeting_type_id, :meeting_number,
                    :first_name, :last_name, :email, :phone, :parish, :purpose,
                    'submitted', :notes
                  )";
        
        $stmt = $db->prepare($query);
        $result = $stmt->execute([
            ':user_id' => $user_id,
            ':meeting_type_id' => $meeting_type['id'],
            ':meeting_number' => $meeting_number,
            ':first_name' => trim($input['firstName']),
            ':last_name' => trim($input['lastName']),
            ':email' => trim($input['email']),
            ':phone' => $phone,
            ':parish' => $input['parish'] ?? null,
            ':purpose' => trim($input['purpose']),
            ':notes' => 'Meeting request submitted via online form'
        ]);
        
        if ($result) {
            $meeting_id = $db->lastInsertId();
            
            // Log the successful submission
            error_log("Meeting request submitted: $meeting_number by user $user_id");
            
            ResponseHelper::success([
                'meeting_number' => $meeting_number,
                'meeting_id' => $meeting_id,
                'message' => 'Meeting request submitted successfully. You will receive confirmation within 24-48 hours.',
                'status' => 'submitted'
            ]);
        } else {
            ResponseHelper::error('Failed to submit meeting request', 500);
        }
        
    } catch (PDOException $e) {
        error_log("Database error in bishop-meeting.php POST: " . $e->getMessage());
        ResponseHelper::error('Database error occurred while submitting request', 500);
    } catch (Exception $e) {
        error_log("General error in bishop-meeting.php POST: " . $e->getMessage());
        ResponseHelper::error('An error occurred while submitting your request', 500);
    }
}

// Generate unique number function
function generateUniqueNumber($prefix = 'REQ') {
    global $db;
    
    do {
        $number = $prefix . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $check_query = "SELECT COUNT(*) FROM meetings WHERE meeting_number = :number";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([':number' => $number]);
        $exists = $check_stmt->fetchColumn() > 0;
    } while ($exists);
    
    return $number;
}

// Handle unsupported methods
ResponseHelper::error('Method not allowed', 405);
?>
