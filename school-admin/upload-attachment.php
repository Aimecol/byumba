<?php
/**
 * AJAX File Upload Handler
 * Diocese of Byumba - School Management System
 */

session_start();
require_once 'includes/functions.php';
require_once 'includes/file-handler.php';

header('Content-Type: application/json');

$currentUser = $schoolAuth->getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$reportId = intval($_POST['report_id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$reportId) {
    echo json_encode(['success' => false, 'error' => 'Invalid report ID']);
    exit;
}

// Verify report belongs to current school
try {
    $query = "SELECT id FROM school_reports WHERE id = :id AND school_id = :school_id";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $reportId);
    $stmt->bindValue(':school_id', $currentUser['school_id']);
    $stmt->execute();
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Report not found or access denied']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Report verification error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
    exit;
}

$fileHandler = new FileHandler($db);

if ($action === 'upload') {
    // Handle file upload
    if (empty($_FILES['files']['name'][0])) {
        echo json_encode(['success' => false, 'error' => 'No files selected']);
        exit;
    }
    
    $uploadedFiles = [];
    $errors = [];
    $totalSize = 0;
    
    // Prepare files array for processing
    $files = [];
    for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
            $files[] = [
                'name' => $_FILES['files']['name'][$i],
                'type' => $_FILES['files']['type'][$i],
                'tmp_name' => $_FILES['files']['tmp_name'][$i],
                'error' => $_FILES['files']['error'][$i],
                'size' => $_FILES['files']['size'][$i]
            ];
            $totalSize += $_FILES['files']['size'][$i];
        }
    }
    
    // Validate total size
    if (!$fileHandler->validateTotalSize($files)) {
        echo json_encode(['success' => false, 'error' => 'Total file size exceeds 50MB limit']);
        exit;
    }
    
    // Process each file
    foreach ($files as $file) {
        $result = $fileHandler->uploadFile($file, $reportId, $currentUser['id']);
        
        if ($result['success']) {
            $uploadedFiles[] = [
                'id' => $result['attachment_id'],
                'name' => $result['original_name'],
                'size' => FileHandler::formatFileSize($result['file_size']),
                'icon' => FileHandler::getFileIcon($result['original_name'])
            ];
        } else {
            $errors = array_merge($errors, $result['errors']);
        }
    }
    
    // Log upload activity
    if (!empty($uploadedFiles)) {
        $fileNames = array_column($uploadedFiles, 'name');
        $schoolAuth->logActivity(
            $currentUser['school_id'],
            $currentUser['id'],
            'upload_attachment',
            "Uploaded " . count($uploadedFiles) . " file(s) to report ID: $reportId - " . implode(', ', $fileNames)
        );
    }
    
    echo json_encode([
        'success' => !empty($uploadedFiles),
        'files' => $uploadedFiles,
        'errors' => $errors
    ]);
    
} elseif ($action === 'delete') {
    // Handle file deletion
    $attachmentId = intval($_POST['attachment_id'] ?? 0);
    
    if (!$attachmentId) {
        echo json_encode(['success' => false, 'error' => 'Invalid attachment ID']);
        exit;
    }
    
    $result = $fileHandler->deleteAttachment($attachmentId, $currentUser['id']);
    
    if ($result['success']) {
        // Log deletion activity
        $schoolAuth->logActivity(
            $currentUser['school_id'],
            $currentUser['id'],
            'delete_attachment',
            "Deleted attachment ID: $attachmentId from report ID: $reportId"
        );
    }
    
    echo json_encode($result);
    
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
