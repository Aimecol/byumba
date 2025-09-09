<?php
/**
 * File Upload Handler for School Reports
 * Diocese of Byumba - School Management System
 */

class FileHandler {
    private $db;
    private $uploadDir;
    private $allowedTypes;
    private $maxFileSize;
    private $maxTotalSize;
    
    public function __construct($database) {
        $this->db = $database;
        $this->uploadDir = '../uploads/school-reports/';
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB per file
        $this->maxTotalSize = 50 * 1024 * 1024; // 50MB total
        
        $this->allowedTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png'
        ];
        
        $this->ensureUploadDirectory();
    }
    
    /**
     * Ensure upload directory exists and is secure
     */
    private function ensureUploadDirectory() {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
        
        // Create .htaccess for security
        $htaccessPath = $this->uploadDir . '.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "# Deny direct access to uploaded files\n";
            $htaccessContent .= "Options -Indexes\n";
            $htaccessContent .= "<Files *.php>\n";
            $htaccessContent .= "    Deny from all\n";
            $htaccessContent .= "</Files>\n";
            file_put_contents($htaccessPath, $htaccessContent);
        }
    }
    
    /**
     * Validate uploaded file
     */
    public function validateFile($file) {
        $errors = [];
        
        // Check if file was uploaded
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'File is too large.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'File upload was interrupted.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'No file was uploaded.';
                    break;
                default:
                    $errors[] = 'File upload failed.';
            }
            return $errors;
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            $errors[] = 'File size exceeds 10MB limit.';
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($extension, $this->allowedTypes)) {
            $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', array_keys($this->allowedTypes));
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedTypes)) {
            $errors[] = 'Invalid file type detected.';
        }
        
        // Sanitize filename
        $sanitizedName = $this->sanitizeFilename($file['name']);
        if (empty($sanitizedName)) {
            $errors[] = 'Invalid filename.';
        }
        
        return $errors;
    }
    
    /**
     * Sanitize filename to prevent security issues
     */
    private function sanitizeFilename($filename) {
        // Remove any path information
        $filename = basename($filename);
        
        // Remove dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Limit length
        if (strlen($filename) > 100) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 95);
            $filename = $name . '.' . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Upload file and save to database
     */
    public function uploadFile($file, $reportId, $userId) {
        // Validate file
        $errors = $this->validateFile($file);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        try {
            // Create school-specific directory
            $schoolDir = $this->uploadDir . 'school_' . $this->getSchoolIdFromUser($userId) . '/';
            if (!file_exists($schoolDir)) {
                mkdir($schoolDir, 0755, true);
            }
            
            // Generate unique filename
            $originalName = $file['name'];
            $sanitizedName = $this->sanitizeFilename($originalName);
            $extension = pathinfo($sanitizedName, PATHINFO_EXTENSION);
            $uniqueName = uniqid() . '_' . time() . '.' . $extension;
            $filePath = $schoolDir . $uniqueName;
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return ['success' => false, 'errors' => ['Failed to save file.']];
            }
            
            // Get file info
            $fileSize = filesize($filePath);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);
            
            // Save to database
            $query = "INSERT INTO report_attachments (report_id, file_name, original_name, file_path, file_size, mime_type, uploaded_by) 
                     VALUES (:report_id, :file_name, :original_name, :file_path, :file_size, :mime_type, :uploaded_by)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':report_id', $reportId);
            $stmt->bindValue(':file_name', $uniqueName);
            $stmt->bindValue(':original_name', $originalName);
            $stmt->bindValue(':file_path', $filePath);
            $stmt->bindValue(':file_size', $fileSize);
            $stmt->bindValue(':mime_type', $mimeType);
            $stmt->bindValue(':uploaded_by', $userId);
            $stmt->execute();
            
            $attachmentId = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'attachment_id' => $attachmentId,
                'file_name' => $uniqueName,
                'original_name' => $originalName,
                'file_size' => $fileSize
            ];
            
        } catch (Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            return ['success' => false, 'errors' => ['Failed to upload file.']];
        }
    }
    
    /**
     * Get school ID from user ID
     */
    private function getSchoolIdFromUser($userId) {
        try {
            $query = "SELECT school_id FROM school_users WHERE id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result ? $result['school_id'] : 0;
        } catch (PDOException $e) {
            error_log("Get school ID error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get attachments for a report
     */
    public function getReportAttachments($reportId) {
        try {
            $query = "SELECT ra.*, su.full_name as uploaded_by_name 
                     FROM report_attachments ra
                     LEFT JOIN school_users su ON ra.uploaded_by = su.id
                     WHERE ra.report_id = :report_id
                     ORDER BY ra.uploaded_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':report_id', $reportId);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get attachments error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete attachment
     */
    public function deleteAttachment($attachmentId, $userId) {
        try {
            // Get attachment info
            $query = "SELECT * FROM report_attachments WHERE id = :id AND uploaded_by = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $attachmentId);
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();
            
            $attachment = $stmt->fetch();
            if (!$attachment) {
                return ['success' => false, 'error' => 'Attachment not found or access denied.'];
            }
            
            // Delete file from filesystem
            if (file_exists($attachment['file_path'])) {
                unlink($attachment['file_path']);
            }
            
            // Delete from database
            $query = "DELETE FROM report_attachments WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $attachmentId);
            $stmt->execute();
            
            return ['success' => true];
            
        } catch (Exception $e) {
            error_log("Delete attachment error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to delete attachment.'];
        }
    }
    
    /**
     * Get file icon based on extension
     */
    public static function getFileIcon($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'pdf':
                return 'fas fa-file-pdf text-danger';
            case 'doc':
            case 'docx':
                return 'fas fa-file-word text-primary';
            case 'xls':
            case 'xlsx':
                return 'fas fa-file-excel text-success';
            case 'ppt':
            case 'pptx':
                return 'fas fa-file-powerpoint text-warning';
            case 'txt':
                return 'fas fa-file-alt text-secondary';
            case 'jpg':
            case 'jpeg':
            case 'png':
                return 'fas fa-file-image text-info';
            default:
                return 'fas fa-file text-muted';
        }
    }
    
    /**
     * Format file size for display
     */
    public static function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Validate total file size for multiple uploads
     */
    public function validateTotalSize($files) {
        $totalSize = 0;
        foreach ($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $totalSize += $file['size'];
            }
        }
        
        return $totalSize <= $this->maxTotalSize;
    }
}
?>
