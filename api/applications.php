<?php
/**
 * Applications API Endpoint
 */

// Mock user ID for demo
$user_id = 1;

if ($method === 'GET') {
    try {
        // Get query parameters
        $status = $_GET['status'] ?? 'all';
        $type = $_GET['type'] ?? 'all';
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $offset = ($page - 1) * $limit;
        
        // Build query
        $where_conditions = ['a.user_id = :user_id', 'ctt.language_code = :language'];
        $params = [':user_id' => $user_id, ':language' => $current_language];
        
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
            $doc_stmt->bindParam(':app_id', $row['id']);
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
        
        // Generate application number
        $application_number = generateUniqueNumber('APP');
        
        // Insert application
        $query = "INSERT INTO applications (user_id, certificate_type_id, application_number, notes) 
                  VALUES (:user_id, :certificate_type_id, :application_number, :notes)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':certificate_type_id', $input['certificate_type_id']);
        $stmt->bindParam(':application_number', $application_number);
        $stmt->bindParam(':notes', $input['notes'] ?? '');
        $stmt->execute();
        
        ResponseHelper::success([
            'application_number' => $application_number,
            'message' => 'Application submitted successfully'
        ]);
        
    } catch (Exception $e) {
        ResponseHelper::error('Failed to submit application: ' . $e->getMessage(), 500);
    }
}

function getApplicationsSummary($db, $user_id) {
    $summary = [];
    
    // Count by status
    $query = "SELECT status, COUNT(*) as count FROM applications WHERE user_id = :user_id GROUP BY status";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
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
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $summary['pending_payment'] = (int)$stmt->fetch()['count'];
    
    return $summary;
}
?>
