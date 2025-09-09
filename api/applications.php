<?php
/**
 * Applications API Endpoint
 */

// Include required files
require_once '../config/database.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session and check authentication
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// For certificate applications, we'll allow public access but track user if logged in
$user_id = $_SESSION['user_id'] ?? null;
$is_authenticated = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;

// Get request method and language
$method = $_SERVER['REQUEST_METHOD'];
$current_language = $_GET['lang'] ?? 'en';

// Database connection should be available from config/database.php
// If not, create a new connection
if (!isset($db) || !$db) {
    try {
        $db = new PDO(
            'mysql:host=localhost;dbname=diocese_byumba;charset=utf8mb4',
            'root',
            '',
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    } catch (PDOException $e) {
        ResponseHelper::error('Database connection failed: ' . $e->getMessage(), 500);
    }
}

if ($method === 'GET') {
    try {
        // Get query parameters
        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        // Build query - if user is authenticated, filter by user_id, otherwise show public applications
        $where_conditions = ['ctt.language_code = :language'];
        $params = [':language' => $current_language];

        if ($is_authenticated && $user_id) {
            $where_conditions[] = 'a.user_id = :user_id';
            $params[':user_id'] = $user_id;
        }
        
        if ($status !== 'all') {
            $where_conditions[] = 'a.status = :status';
            $params[':status'] = $status;
        }
        
        if ($type !== 'all') {
            $where_conditions[] = 'ct.type_key = :type';
            $params[':type'] = $type;
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total 
                       FROM applications a 
                       JOIN certificate_types ct ON a.certificate_type_id = ct.id 
                       JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id 
                       $where_clause";
        
        $stmt = $db->prepare($count_query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $total = $stmt->fetch()['total'];
        
        // Get applications
        $query = "SELECT a.*, ct.fee, ct.processing_days, ct.icon, 
                         ctt.name as type_name, ctt.description, ctt.required_documents 
                  FROM applications a 
                  JOIN certificate_types ct ON a.certificate_type_id = ct.id 
                  JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id 
                  $where_clause 
                  ORDER BY a.submitted_date DESC 
                  LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $applications = [];
        while ($row = $stmt->fetch()) {
            // Get documents for this application
            $doc_query = "SELECT * FROM application_documents WHERE application_id = :app_id";
            $doc_stmt = $db->prepare($doc_query);
            $doc_stmt->bindValue(':app_id', $row['id']);
            $doc_stmt->execute();
            $documents = $doc_stmt->fetchAll();
            
            $applications[] = [
                'id' => $row['application_number'],
                'type' => $row['type_name'],
                'type_key' => $row['type_key'] ?? '',
                'description' => $row['description'],
                'status' => $row['status'],
                'submitted_date' => $row['submitted_date'],
                'approved_date' => $row['approved_date'],
                'completed_date' => $row['completed_date'],
                'fee' => 'RWF ' . number_format($row['fee']),
                'fee_amount' => $row['fee'],
                'processing_days' => $row['processing_days'],
                'payment_code' => $row['payment_code'],
                'payment_status' => $row['payment_status'],
                'payment_date' => $row['payment_date'],
                'notes' => $row['notes'],
                'icon' => $row['icon'],
                'required_documents' => json_decode($row['required_documents'], true),
                'uploaded_documents' => $documents
            ];
        }
        
        // Get summary statistics
        $summary = getApplicationsSummary($db, $user_id);
        
        ResponseHelper::success([
            'applications' => $applications,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total_items' => $total,
                'items_per_page' => $limit
            ],
            'summary' => $summary
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to load applications: ' . $e->getMessage(), 500);
    }
}

if ($method === 'POST') {
    try {
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (!isset($input['certificate_type_id'])) {
            ResponseHelper::error('Certificate type is required', 400);
        }

        if (!isset($input['form_data'])) {
            ResponseHelper::error('Form data is required', 400);
        }

        // Start transaction
        $db->beginTransaction();

        // Generate application number
        $application_number = generateUniqueNumber('APP');

        // Prepare notification methods JSON
        $notification_methods = isset($input['notification_methods']) ?
            json_encode($input['notification_methods']) : json_encode(['email']);

        // For public applications, use a default user_id or null
        $effective_user_id = $user_id ?? 1; // Use user_id 1 for public applications or create a public user

        // Insert application
        $query = "INSERT INTO applications (user_id, certificate_type_id, application_number, notes, notification_methods)
                  VALUES (:user_id, :certificate_type_id, :application_number, :notes, :notification_methods)";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':user_id', $effective_user_id);
        $stmt->bindValue(':certificate_type_id', $input['certificate_type_id']);
        $stmt->bindValue(':application_number', $application_number);
        $stmt->bindValue(':notes', $input['notes'] ?? '');
        $stmt->bindValue(':notification_methods', $notification_methods);
        $stmt->execute();

        $application_id = $db->lastInsertId();

        // Insert form data
        $form_data_query = "INSERT INTO application_form_data (application_id, field_name, field_value)
                           VALUES (:application_id, :field_name, :field_value)";
        $form_data_stmt = $db->prepare($form_data_query);

        foreach ($input['form_data'] as $field_name => $field_value) {
            // Skip empty values and files (files are handled separately)
            if ($field_value !== '' && $field_value !== null && !is_array($field_value)) {
                $form_data_stmt->bindValue(':application_id', $application_id);
                $form_data_stmt->bindValue(':field_name', $field_name);
                $form_data_stmt->bindValue(':field_value', $field_value);
                $form_data_stmt->execute();
            }
        }

        // Handle document uploads if provided
        if (isset($input['documents']) && is_array($input['documents'])) {
            $doc_query = "INSERT INTO application_documents (application_id, document_name, file_path, file_size, mime_type)
                         VALUES (:application_id, :document_name, :file_path, :file_size, :mime_type)";
            $doc_stmt = $db->prepare($doc_query);

            foreach ($input['documents'] as $document) {
                if (isset($document['name']) && isset($document['path'])) {
                    $doc_stmt->bindValue(':application_id', $application_id);
                    $doc_stmt->bindValue(':document_name', $document['name']);
                    $doc_stmt->bindValue(':file_path', $document['path']);
                    $doc_stmt->bindValue(':file_size', $document['size'] ?? null);
                    $doc_stmt->bindValue(':mime_type', $document['type'] ?? null);
                    $doc_stmt->execute();
                }
            }
        }

        // Commit transaction
        $db->commit();

        ResponseHelper::success([
            'application_number' => $application_number,
            'application_id' => $application_id,
            'message' => 'Application submitted successfully'
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
        if ($db->inTransaction()) {
            $db->rollback();
        }
        ResponseHelper::error('Failed to submit application: ' . $e->getMessage(), 500);
    }
}

function getApplicationsSummary($db, $user_id) {
    $summary = [];
    
    // Count by status
    $query = "SELECT status, COUNT(*) as count FROM applications WHERE user_id = :user_id GROUP BY status";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();
    
    $status_counts = [
        'pending' => 0,
        'processing' => 0,
        'approved' => 0,
        'completed' => 0,
        'rejected' => 0
    ];
    
    while ($row = $stmt->fetch()) {
        $status_counts[$row['status']] = (int)$row['count'];
    }
    
    $summary['by_status'] = $status_counts;
    
    // Total applications
    $summary['total'] = array_sum($status_counts);
    
    // Pending payment count
    $query = "SELECT COUNT(*) as count FROM applications WHERE user_id = :user_id AND payment_status = 'pending' AND status = 'approved'";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':user_id', $user_id);
    $stmt->execute();
    $summary['pending_payment'] = (int)$stmt->fetch()['count'];
    
    return $summary;
}
?>
