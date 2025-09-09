<?php
/**
 * Job Applications Handler
 * Diocese of Byumba - Handle job application form submissions
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

// GET - Retrieve job applications
if ($method === 'GET') {
    try {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 10;
        $offset = ($page - 1) * $limit;
        
        $status_filter = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        // Build query with filters
        $where_conditions = ['ja.user_id = :user_id'];
        $params = [':user_id' => $user_id];
        
        if (!empty($status_filter)) {
            $where_conditions[] = 'ja.status = :status';
            $params[':status'] = $status_filter;
        }
        
        if (!empty($search)) {
            $where_conditions[] = '(ja.first_name LIKE :search OR ja.last_name LIKE :search OR ja.email LIKE :search OR ja.job_title LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }
        
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
        
        // Get total count
        $count_query = "SELECT COUNT(*) as total FROM job_applications ja $where_clause";
        $count_stmt = $db->prepare($count_query);
        $count_stmt->execute($params);
        $total_items = $count_stmt->fetch()['total'];
        
        // Get job applications with job information
        $query = "SELECT 
                    ja.id,
                    ja.application_number,
                    ja.first_name,
                    ja.last_name,
                    ja.email,
                    ja.phone,
                    ja.education_level,
                    ja.years_experience,
                    ja.job_title,
                    ja.job_department,
                    ja.job_type,
                    ja.status,
                    ja.priority,
                    ja.interview_date,
                    ja.interview_location,
                    ja.submitted_at,
                    ja.created_at,
                    ja.updated_at,
                    j.title as actual_job_title,
                    jt.title as job_title_translated
                  FROM job_applications ja
                  LEFT JOIN jobs j ON ja.job_id = j.id
                  LEFT JOIN job_translations jt ON j.id = jt.job_id AND jt.language_code = 'en'
                  $where_clause
                  ORDER BY ja.submitted_at DESC
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
            $applications[] = [
                'id' => $row['application_number'],
                'application_id' => $row['id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'full_name' => $row['first_name'] . ' ' . $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'education_level' => $row['education_level'],
                'years_experience' => $row['years_experience'],
                'job_title' => $row['actual_job_title'] ?? $row['job_title'],
                'job_department' => $row['job_department'],
                'job_type' => $row['job_type'],
                'status' => $row['status'],
                'priority' => $row['priority'],
                'interview_date' => $row['interview_date'],
                'interview_location' => $row['interview_location'],
                'submitted_date' => $row['submitted_at'],
                'created_date' => $row['created_at'],
                'last_updated' => $row['updated_at']
            ];
        }
        
        // Get summary statistics
        $summary_query = "SELECT 
                            COUNT(*) as total,
                            SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as submitted,
                            SUM(CASE WHEN status = 'under_review' THEN 1 ELSE 0 END) as under_review,
                            SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                            SUM(CASE WHEN status = 'interview_scheduled' THEN 1 ELSE 0 END) as interview_scheduled,
                            SUM(CASE WHEN status = 'interviewed' THEN 1 ELSE 0 END) as interviewed,
                            SUM(CASE WHEN status = 'selected' THEN 1 ELSE 0 END) as selected,
                            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                            SUM(CASE WHEN status = 'withdrawn' THEN 1 ELSE 0 END) as withdrawn
                          FROM job_applications 
                          WHERE user_id = :user_id";
        
        $summary_stmt = $db->prepare($summary_query);
        $summary_stmt->execute([':user_id' => $user_id]);
        $summary = $summary_stmt->fetch();
        
        ResponseHelper::success([
            'applications' => $applications,
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
                    'under_review' => (int)$summary['under_review'],
                    'shortlisted' => (int)$summary['shortlisted'],
                    'interview_scheduled' => (int)$summary['interview_scheduled'],
                    'interviewed' => (int)$summary['interviewed'],
                    'selected' => (int)$summary['selected'],
                    'rejected' => (int)$summary['rejected'],
                    'withdrawn' => (int)$summary['withdrawn']
                ]
            ]
        ]);
        
    } catch (PDOException $e) {
        error_log("Database error in job-applications.php: " . $e->getMessage());
        ResponseHelper::error('Database error occurred', 500);
    } catch (Exception $e) {
        error_log("General error in job-applications.php: " . $e->getMessage());
        ResponseHelper::error('An error occurred while retrieving applications', 500);
    }
}

// POST - Submit new job application
if ($method === 'POST') {
    try {
        // Handle multipart form data (for file uploads)
        $input = [];
        
        // Get form data
        foreach ($_POST as $key => $value) {
            $input[$key] = $value;
        }
        
        // Validate required fields
        $required_fields = ['firstName', 'lastName', 'email', 'phone', 'education', 'coverLetter', 'jobTitle', 'jobDepartment', 'jobType'];
        foreach ($required_fields as $field) {
            if (!isset($input[$field]) || empty(trim($input[$field]))) {
                ResponseHelper::error("Field '$field' is required", 400);
            }
        }
        
        // Validate email format
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            ResponseHelper::error('Invalid email format', 400);
        }
        
        // Validate phone format
        $phone = preg_replace('/[^0-9+]/', '', $input['phone']);
        if (strlen($phone) < 10) {
            ResponseHelper::error('Invalid phone number format', 400);
        }
        
        // Validate required checkboxes
        if (!isset($input['terms']) || $input['terms'] !== 'on') {
            ResponseHelper::error('You must accept the terms and conditions', 400);
        }
        
        if (!isset($input['dataConsent']) || $input['dataConsent'] !== 'on') {
            ResponseHelper::error('You must consent to data processing', 400);
        }
        
        // Validate required resume file
        if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== UPLOAD_ERR_OK) {
            ResponseHelper::error('Resume file is required', 400);
        }
        
        // Start database transaction
        $db->beginTransaction();
        
        try {
            // Generate unique application number (will be set by trigger)
            $application_number = null;
            
            // Insert job application
            $query = "INSERT INTO job_applications (
                        user_id, first_name, last_name, email, phone, address,
                        education_level, years_experience, skills, cover_letter,
                        job_title, job_department, job_type,
                        terms_accepted, data_consent, status, priority,
                        notification_methods
                      ) VALUES (
                        :user_id, :first_name, :last_name, :email, :phone, :address,
                        :education_level, :years_experience, :skills, :cover_letter,
                        :job_title, :job_department, :job_type,
                        1, 1, 'submitted', 'medium',
                        :notification_methods
                      )";
            
            $stmt = $db->prepare($query);
            $result = $stmt->execute([
                ':user_id' => $user_id,
                ':first_name' => trim($input['firstName']),
                ':last_name' => trim($input['lastName']),
                ':email' => trim($input['email']),
                ':phone' => $phone,
                ':address' => trim($input['address'] ?? ''),
                ':education_level' => $input['education'],
                ':years_experience' => $input['experience'] ?? null,
                ':skills' => trim($input['skills'] ?? ''),
                ':cover_letter' => trim($input['coverLetter']),
                ':job_title' => trim($input['jobTitle']),
                ':job_department' => trim($input['jobDepartment']),
                ':job_type' => $input['jobType'],
                ':notification_methods' => json_encode(['email'])
            ]);
            
            if (!$result) {
                throw new Exception('Failed to insert job application');
            }
            
            $application_id = $db->lastInsertId();
            
            // Get the generated application number
            $app_query = "SELECT application_number FROM job_applications WHERE id = :id";
            $app_stmt = $db->prepare($app_query);
            $app_stmt->execute([':id' => $application_id]);
            $app_result = $app_stmt->fetch();
            $application_number = $app_result['application_number'];
            
            // Handle file uploads
            $upload_dir = '../uploads/job_applications/' . $application_number . '/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $uploaded_files = [];
            
            // Handle resume file (required)
            if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                $file_result = handleFileUpload($_FILES['resume'], $upload_dir, 'resume', $application_id);
                if ($file_result['success']) {
                    $uploaded_files[] = $file_result;
                } else {
                    throw new Exception('Failed to upload resume: ' . $file_result['error']);
                }
            }
            
            // Handle cover letter file (optional)
            if (isset($_FILES['coverLetterFile']) && $_FILES['coverLetterFile']['error'] === UPLOAD_ERR_OK) {
                $file_result = handleFileUpload($_FILES['coverLetterFile'], $upload_dir, 'cover_letter', $application_id);
                if ($file_result['success']) {
                    $uploaded_files[] = $file_result;
                }
            }
            
            // Handle certificates (optional, multiple files)
            if (isset($_FILES['certificates']) && is_array($_FILES['certificates']['name'])) {
                for ($i = 0; $i < count($_FILES['certificates']['name']); $i++) {
                    if ($_FILES['certificates']['error'][$i] === UPLOAD_ERR_OK) {
                        $file_data = [
                            'name' => $_FILES['certificates']['name'][$i],
                            'type' => $_FILES['certificates']['type'][$i],
                            'tmp_name' => $_FILES['certificates']['tmp_name'][$i],
                            'error' => $_FILES['certificates']['error'][$i],
                            'size' => $_FILES['certificates']['size'][$i]
                        ];
                        $file_result = handleFileUpload($file_data, $upload_dir, 'certificate', $application_id);
                        if ($file_result['success']) {
                            $uploaded_files[] = $file_result;
                        }
                    }
                }
            }
            
            // Commit transaction
            $db->commit();
            
            // Log the successful submission
            error_log("Job application submitted: $application_number by user $user_id");
            
            ResponseHelper::success([
                'application_number' => $application_number,
                'application_id' => $application_id,
                'message' => 'Job application submitted successfully. You will receive confirmation via email.',
                'status' => 'submitted',
                'uploaded_files' => count($uploaded_files)
            ]);
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        
    } catch (PDOException $e) {
        error_log("Database error in job-applications.php POST: " . $e->getMessage());
        ResponseHelper::error('Database error occurred while submitting application', 500);
    } catch (Exception $e) {
        error_log("General error in job-applications.php POST: " . $e->getMessage());
        ResponseHelper::error('An error occurred while submitting your application: ' . $e->getMessage(), 500);
    }
}

// File upload handler function
function handleFileUpload($file, $upload_dir, $document_type, $application_id) {
    global $db;
    
    // Validate file
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only PDF, DOC, DOCX, JPG, PNG allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File too large. Maximum size is 5MB.'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $stored_filename = $document_type . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    $file_path = $upload_dir . $stored_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Insert document record
        $doc_query = "INSERT INTO application_documents (
                        application_id, document_type, original_filename, 
                        stored_filename, file_path, file_size, mime_type
                      ) VALUES (
                        :application_id, :document_type, :original_filename,
                        :stored_filename, :file_path, :file_size, :mime_type
                      )";
        
        $doc_stmt = $db->prepare($doc_query);
        $doc_stmt->execute([
            ':application_id' => $application_id,
            ':document_type' => $document_type,
            ':original_filename' => $file['name'],
            ':stored_filename' => $stored_filename,
            ':file_path' => $file_path,
            ':file_size' => $file['size'],
            ':mime_type' => $file['type']
        ]);
        
        return [
            'success' => true,
            'filename' => $stored_filename,
            'original_name' => $file['name'],
            'size' => $file['size']
        ];
    } else {
        return ['success' => false, 'error' => 'Failed to save file'];
    }
}

// Handle unsupported methods
ResponseHelper::error('Method not allowed', 405);
?>
