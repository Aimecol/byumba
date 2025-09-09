<?php
/**
 * View Report Details
 * Diocese of Byumba - School Management System
 */

session_start();
$pageTitle = 'View Report';
require_once 'includes/functions.php';
require_once 'includes/file-handler.php';

$currentUser = $schoolAuth->getCurrentUser();
$reportId = intval($_GET['id'] ?? 0);

if (!$reportId) {
    $_SESSION['error_message'] = 'Invalid report ID.';
    header('Location: reports.php');
    exit;
}

// Get report details
try {
    $query = "SELECT sr.*, rt.type_name, rt.type_code, rt.required_fields, su.full_name as submitted_by_name
             FROM school_reports sr
             JOIN report_types rt ON sr.report_type_id = rt.id
             LEFT JOIN school_users su ON sr.submitted_by = su.id
             WHERE sr.id = :id AND sr.school_id = :school_id";
    
    $stmt = $db->prepare($query);
    $stmt->bindValue(':id', $reportId);
    $stmt->bindValue(':school_id', $currentUser['school_id']);
    $stmt->execute();
    
    $report = $stmt->fetch();
    
    if (!$report) {
        $_SESSION['error_message'] = 'Report not found.';
        header('Location: reports.php');
        exit;
    }
    
    // Decode report data
    $reportData = json_decode($report['report_data'], true) ?: [];
    $requiredFields = json_decode($report['required_fields'], true) ?: [];

    // Get report attachments
    $fileHandler = new FileHandler($db);
    $attachments = $fileHandler->getReportAttachments($reportId);

} catch(PDOException $e) {
    error_log("View report error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Failed to load report details.';
    header('Location: reports.php');
    exit;
}

require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-file-alt me-2"></i>Report Details
            </h2>
            <div>
                <?php if (in_array($report['status'], ['draft', 'requires_revision'])): ?>
                    <a href="edit-report.php?id=<?php echo $report['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Report
                    </a>
                <?php endif; ?>
                <a href="reports.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Report Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-0"><?php echo htmlspecialchars($report['title']); ?></h4>
                        <small class="opacity-75">
                            Report #<?php echo htmlspecialchars($report['report_number']); ?>
                        </small>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge <?php echo getStatusBadgeClass($report['status']); ?> fs-6">
                            <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                        </span>
                        <span class="badge <?php echo getPriorityBadgeClass($report['priority']); ?> fs-6 ms-2">
                            <?php echo ucfirst($report['priority']); ?> Priority
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Report Type:</strong><br>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($report['type_name']); ?></span>
                    </div>
                    <div class="col-md-3">
                        <strong>Reporting Period:</strong><br>
                        <?php echo htmlspecialchars($report['reporting_period'] ?: 'Not specified'); ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Created:</strong><br>
                        <?php echo formatDateTime($report['created_at']); ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Submitted:</strong><br>
                        <?php echo $report['submitted_at'] ? formatDateTime($report['submitted_at']) : 'Not submitted'; ?>
                    </div>
                </div>
                
                <?php if ($report['submitted_by_name']): ?>
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <strong>Submitted By:</strong><br>
                            <?php echo htmlspecialchars($report['submitted_by_name']); ?>
                        </div>
                        <?php if ($report['reviewed_at']): ?>
                            <div class="col-md-3">
                                <strong>Reviewed:</strong><br>
                                <?php echo formatDateTime($report['reviewed_at']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Report Data -->
<?php if (!empty($reportData)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Report Data
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($reportData as $field => $value): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="border-start-primary ps-3">
                                    <strong><?php echo ucwords(str_replace('_', ' ', $field)); ?>:</strong><br>
                                    <span class="text-muted">
                                        <?php 
                                        if (is_numeric($value)) {
                                            echo number_format($value);
                                        } elseif (is_bool($value)) {
                                            echo $value ? 'Yes' : 'No';
                                        } else {
                                            echo htmlspecialchars($value ?: 'Not provided');
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Notes Section -->
<div class="row mb-4">
    <div class="col-md-6">
        <?php if ($report['school_notes']): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>School Notes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($report['school_notes'])); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <?php if ($report['admin_notes']): ?>
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user-shield me-2"></i>Administrator Notes
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($report['admin_notes'])); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Attachments Section -->
<?php if (!empty($attachments)): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-paperclip me-2"></i>Document Attachments
                    <span class="badge bg-secondary ms-2"><?php echo count($attachments); ?></span>
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($attachments as $attachment): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="attachment-item d-flex align-items-center">
                                <div class="attachment-icon me-3">
                                    <i class="<?php echo FileHandler::getFileIcon($attachment['original_name']); ?>"></i>
                                </div>
                                <div class="attachment-info">
                                    <div class="attachment-name">
                                        <?php echo htmlspecialchars($attachment['original_name']); ?>
                                    </div>
                                    <div class="attachment-meta">
                                        <small>
                                            <?php echo FileHandler::formatFileSize($attachment['file_size']); ?>
                                            <?php if ($attachment['uploaded_by_name']): ?>
                                                • by <?php echo htmlspecialchars($attachment['uploaded_by_name']); ?>
                                            <?php endif; ?>
                                            <br>
                                            <?php echo formatDateTime($attachment['uploaded_at'], 'M j, Y g:i A'); ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="attachment-actions ms-auto">
                                    <a href="download-attachment.php?id=<?php echo $attachment['id']; ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <?php if ($report['status'] === 'draft' && $attachment['uploaded_by'] == $currentUser['id']): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="deleteAttachment(<?php echo $attachment['id']; ?>)"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- File Upload Section (for draft reports) -->
<?php if ($report['status'] === 'draft'): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Add Attachments
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Upload additional documents for this report. Accepted formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG</p>

                <!-- File Upload Area -->
                <div class="file-upload-area" id="fileUploadArea">
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h6>Drag and drop files here</h6>
                            <p class="text-muted">or</p>
                            <button type="button" class="btn btn-outline-primary" id="selectFilesBtn">
                                <i class="fas fa-folder-open me-2"></i>Select Files
                            </button>
                            <input type="file" id="fileInput" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png" style="display: none;">
                        </div>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div id="uploadProgress" class="mt-3" style="display: none;">
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="text-muted mt-1 d-block">Uploading files...</small>
                </div>

                <!-- File Size Info -->
                <div class="mt-2">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Maximum file size: 10MB per file, 50MB total
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Actions
                </h6>
            </div>
            <div class="card-body">
                <div class="btn-group" role="group">
                    <?php if (in_array($report['status'], ['draft', 'requires_revision'])): ?>
                        <a href="edit-report.php?id=<?php echo $report['id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Edit Report
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($report['status'] === 'draft'): ?>
                        <form method="POST" action="submit-report.php" class="d-inline">
                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to submit this report? You will not be able to edit it after submission.')">
                                <i class="fas fa-paper-plane me-2"></i>Submit Report
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <button type="button" class="btn btn-outline-secondary print-btn">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    
                    <?php if ($report['status'] === 'draft'): ?>
                        <form method="POST" action="delete-report.php" class="d-inline">
                            <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                            <button type="submit" class="btn btn-outline-danger confirm-delete">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Print functionality
    document.querySelector('.print-btn')?.addEventListener('click', function() {
        window.print();
    });

    // Delete confirmation
    document.querySelectorAll('.confirm-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // File upload functionality (only for draft reports)
    <?php if ($report['status'] === 'draft'): ?>
    initFileUpload();
    <?php endif; ?>
});

<?php if ($report['status'] === 'draft'): ?>
// File upload functionality
function initFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const selectFilesBtn = document.getElementById('selectFilesBtn');
    const uploadProgress = document.getElementById('uploadProgress');

    if (!uploadZone || !fileInput || !selectFilesBtn) return;

    // Select files button
    selectFilesBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            uploadFiles(e.target.files);
        }
    });

    // Drag and drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('drag-over');
    });

    uploadZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('drag-over');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('drag-over');
        if (e.dataTransfer.files.length > 0) {
            uploadFiles(e.dataTransfer.files);
        }
    });
}

function uploadFiles(files) {
    const formData = new FormData();
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = uploadProgress.querySelector('.progress-bar');

    // Add files to form data
    for (let file of files) {
        formData.append('files[]', file);
    }

    formData.append('report_id', <?php echo $reportId; ?>);
    formData.append('action', 'upload');

    // Show progress
    uploadProgress.style.display = 'block';
    progressBar.style.width = '0%';

    // Upload files
    fetch('upload-attachment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        uploadProgress.style.display = 'none';

        if (data.success) {
            showAlert('success', `Successfully uploaded ${data.files.length} file(s).`);
            // Reload page to show new attachments
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.error || 'Upload failed.');
            if (data.errors && data.errors.length > 0) {
                data.errors.forEach(error => {
                    showAlert('warning', error);
                });
            }
        }
    })
    .catch(error => {
        uploadProgress.style.display = 'none';
        showAlert('danger', 'Upload failed. Please try again.');
        console.error('Upload error:', error);
    });

    // Simulate progress
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 30;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 200);

    // Clear interval when done
    setTimeout(() => {
        clearInterval(progressInterval);
        progressBar.style.width = '100%';
    }, 2000);
}

function deleteAttachment(attachmentId) {
    if (!confirm('Are you sure you want to delete this attachment?')) {
        return;
    }

    const formData = new FormData();
    formData.append('report_id', <?php echo $reportId; ?>);
    formData.append('attachment_id', attachmentId);
    formData.append('action', 'delete');

    fetch('upload-attachment.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Attachment deleted successfully.');
            // Reload page to update attachments
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('danger', data.error || 'Failed to delete attachment.');
        }
    })
    .catch(error => {
        showAlert('danger', 'Failed to delete attachment. Please try again.');
        console.error('Delete error:', error);
    });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>
