<?php
/**
 * File Upload API Endpoint
 * Handles document uploads for certificate applications
 */

require_once '../config/database.php';

// Set JSON response header
header('Content-Type: application/json');

// Check authentication
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    ResponseHelper::error('Authentication required', 401);
}

$user_id = $_SESSION['user_id'];

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ResponseHelper::error('Method not allowed', 405);
}

try {
    // Check if files were uploaded
    if (!isset($_FILES['documents']) || empty($_FILES['documents']['name'][0])) {
        ResponseHelper::error('No files uploaded', 400);
    }

    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/documents/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $uploaded_files = [];
    $files = $_FILES['documents'];
    $file_count = count($files['name']);

    // Validate and process each file
    for ($i = 0; $i < $file_count; $i++) {
        // Skip empty files
        if (empty($files['name'][$i])) {
            continue;
        }

        $file_name = $files['name'][$i];
        $file_tmp = $files['tmp_name'][$i];
        $file_size = $files['size'][$i];
        $file_error = $files['error'][$i];
        $file_type = $files['type'][$i];

        // Check for upload errors
        if ($file_error !== UPLOAD_ERR_OK) {
            ResponseHelper::error("Upload error for file: $file_name", 400);
        }

        // Validate file size (5MB max)
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($file_size > $max_size) {
            ResponseHelper::error("File too large: $file_name (max 5MB)", 400);
        }

        // Validate file type
        $allowed_types = [
            'application/pdf',
            'image/jpeg',
            'image/jpg', 
            'image/png'
        ];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        if (!in_array($detected_type, $allowed_types)) {
            ResponseHelper::error("Invalid file type for: $file_name. Only PDF, JPG, and PNG files are allowed.", 400);
        }

        // Generate unique filename
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_name = uniqid('doc_' . $user_id . '_') . '.' . $file_extension;
        $file_path = $upload_dir . $unique_name;

        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $file_path)) {
            ResponseHelper::error("Failed to save file: $file_name", 500);
        }

        // Store file information
        $uploaded_files[] = [
            'original_name' => $file_name,
            'stored_name' => $unique_name,
            'file_path' => '/uploads/documents/' . $unique_name,
            'file_size' => $file_size,
            'mime_type' => $detected_type
        ];
    }

    if (empty($uploaded_files)) {
        ResponseHelper::error('No valid files were uploaded', 400);
    }

    ResponseHelper::success([
        'message' => 'Files uploaded successfully',
        'files' => $uploaded_files,
        'count' => count($uploaded_files)
    ]);

} catch (Exception $e) {
    ResponseHelper::error('Upload failed: ' . $e->getMessage(), 500);
}
?>
