<?php
/**
 * Create New Report
 * Diocese of Byumba - School Management System
 */

session_start();
$pageTitle = 'Create Report';
require_once 'includes/functions.php';
require_once 'includes/file-handler.php';

$currentUser = $schoolAuth->getCurrentUser();

// Get report types
$reportTypes = $schoolReports->getReportTypes();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportTypeId = intval($_POST['report_type_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $reportingPeriod = trim($_POST['reporting_period'] ?? '');
    $schoolNotes = trim($_POST['school_notes'] ?? '');
    $priority = $_POST['priority'] ?? 'normal';
    $status = $_POST['action'] === 'submit' ? 'submitted' : 'draft';
    
    // Validate required fields
    if (!$reportTypeId || !$title) {
        $error = 'Please fill in all required fields.';
    } else {
        // Get report type info
        $reportType = null;
        foreach ($reportTypes as $type) {
            if ($type['id'] == $reportTypeId) {
                $reportType = $type;
                break;
            }
        }
        
        if (!$reportType) {
            $error = 'Invalid report type selected.';
        } else {
            // Collect report data based on required fields
            $reportData = [];
            $requiredFields = json_decode($reportType['required_fields'], true) ?: [];
            
            foreach ($requiredFields as $field) {
                $fieldValue = $_POST['field_' . $field] ?? '';
                $reportData[$field] = $fieldValue;
            }
            
            // Create report
            $createData = [
                'school_id' => $currentUser['school_id'],
                'school_code' => $currentUser['school_code'],
                'report_type_id' => $reportTypeId,
                'report_type_code' => $reportType['type_code'],
                'title' => $title,
                'reporting_period' => $reportingPeriod,
                'report_data' => $reportData,
                'submitted_by' => $currentUser['user_id'],
                'status' => $status
            ];
            
            $result = $schoolReports->createReport($createData);

            if ($result['success']) {
                $reportId = $result['report_id'];
                $uploadErrors = [];
                $uploadedFiles = [];

                // Handle file uploads if any files were selected
                if (!empty($_FILES['attachments']['name'][0])) {
                    $fileHandler = new FileHandler($db);

                    // Process each uploaded file
                    for ($i = 0; $i < count($_FILES['attachments']['name']); $i++) {
                        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {
                            $file = [
                                'name' => $_FILES['attachments']['name'][$i],
                                'type' => $_FILES['attachments']['type'][$i],
                                'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                                'error' => $_FILES['attachments']['error'][$i],
                                'size' => $_FILES['attachments']['size'][$i]
                            ];

                            $uploadResult = $fileHandler->uploadFile($file, $reportId, $currentUser['user_id']);

                            if ($uploadResult['success']) {
                                $uploadedFiles[] = $uploadResult['original_name'];
                            } else {
                                $uploadErrors = array_merge($uploadErrors, $uploadResult['errors']);
                            }
                        }
                    }
                }

                // Log activity
                $action = $status === 'submitted' ? 'submit_report' : 'create_draft';
                $description = $status === 'submitted' ?
                    "Submitted report: {$result['report_number']}" :
                    "Created draft report: {$result['report_number']}";

                if (!empty($uploadedFiles)) {
                    $description .= " with " . count($uploadedFiles) . " attachment(s): " . implode(', ', $uploadedFiles);
                }

                $schoolAuth->logActivity(
                    $currentUser['school_id'],
                    $currentUser['user_id'],
                    $action,
                    $description
                );

                // Set success message
                $successMessage = $status === 'submitted' ?
                    'Report submitted successfully!' :
                    'Report draft saved successfully!';

                if (!empty($uploadedFiles)) {
                    $successMessage .= ' ' . count($uploadedFiles) . ' file(s) uploaded.';
                }

                if (!empty($uploadErrors)) {
                    $successMessage .= ' However, some files failed to upload: ' . implode(', ', $uploadErrors);
                }

                $_SESSION['success_message'] = $successMessage;

                header('Location: view-report.php?id=' . $reportId);
                exit;
            } else {
                $error = $result['message'] ?? 'Failed to create report. Please try again.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-plus me-2"></i>Create New Report
            </h2>
            <a href="reports.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="needs-validation auto-save" id="reportForm" novalidate>
    <!-- Report Type Selection -->
    <div class="form-section">
        <h5><i class="fas fa-clipboard-list me-2"></i>Report Information</h5>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="report_type_id" class="form-label">Report Type <span class="text-danger">*</span></label>
                <select class="form-select" id="report_type_id" name="report_type_id" required>
                    <option value="">Select Report Type</option>
                    <?php foreach ($reportTypes as $type): ?>
                        <option value="<?php echo $type['id']; ?>" 
                                data-fields="<?php echo htmlspecialchars($type['required_fields']); ?>"
                                data-frequency="<?php echo $type['submission_frequency']; ?>"
                                <?php echo (isset($_POST['report_type_id']) && $_POST['report_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['type_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="invalid-feedback">Please select a report type.</div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-select" id="priority" name="priority">
                    <option value="normal" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'normal') ? 'selected' : ''; ?>>Normal</option>
                    <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'urgent') ? 'selected' : ''; ?>>Urgent</option>
                    <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-8 mb-3">
                <label for="title" class="form-label">Report Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                       placeholder="Enter a descriptive title for this report" required>
                <div class="invalid-feedback">Please provide a report title.</div>
            </div>
            
            <div class="col-md-4 mb-3">
                <label for="reporting_period" class="form-label">Reporting Period</label>
                <input type="text" class="form-control" id="reporting_period" name="reporting_period" 
                       value="<?php echo htmlspecialchars($_POST['reporting_period'] ?? ''); ?>" 
                       placeholder="e.g., Q1 2024, January 2024">
                <small class="form-text text-muted">The period this report covers</small>
            </div>
        </div>
    </div>
    
    <!-- Dynamic Fields Section -->
    <div class="form-section" id="dynamicFields" style="display: none;">
        <h5><i class="fas fa-edit me-2"></i>Report Details</h5>
        <div id="fieldsContainer">
            <!-- Dynamic fields will be inserted here -->
        </div>
    </div>
    
    <!-- Notes Section -->
    <div class="form-section">
        <h5><i class="fas fa-sticky-note me-2"></i>Additional Notes</h5>
        
        <div class="mb-3">
            <label for="school_notes" class="form-label">School Notes</label>
            <textarea class="form-control" id="school_notes" name="school_notes" rows="4" 
                      placeholder="Add any additional information, context, or notes about this report..."><?php echo htmlspecialchars($_POST['school_notes'] ?? ''); ?></textarea>
            <small class="form-text text-muted">Optional notes that will be visible to diocese administrators</small>
        </div>
    </div>

    <!-- File Attachments Section -->
    <div class="form-section">
        <h5><i class="fas fa-paperclip me-2"></i>Document Attachments</h5>
        <p class="text-muted mb-3">Upload supporting documents for this report. Accepted formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, JPG, PNG</p>

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
                    <input type="file" name="attachments[]" id="fileInput" multiple
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png"
                           style="display: none;">
                </div>
            </div>
        </div>

        <!-- File List -->
        <div id="fileList" class="mt-3" style="display: none;">
            <h6>Selected Files:</h6>
            <div id="fileListContainer"></div>
        </div>

        <!-- File Size Info -->
        <div class="mt-2">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                Maximum file size: 10MB per file, 50MB total
            </small>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="form-section">
        <div class="d-flex justify-content-between">
            <div>
                <button type="submit" name="action" value="draft" class="btn btn-outline-primary">
                    <i class="fas fa-save me-2"></i>Save as Draft
                </button>
                <button type="submit" name="action" value="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Submit Report
                </button>
            </div>
            <a href="reports.php" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Cancel
            </a>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reportTypeSelect = document.getElementById('report_type_id');
    const dynamicFieldsSection = document.getElementById('dynamicFields');
    const fieldsContainer = document.getElementById('fieldsContainer');
    
    reportTypeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const fields = JSON.parse(selectedOption.dataset.fields || '[]');
            const frequency = selectedOption.dataset.frequency;
            
            generateDynamicFields(fields, frequency);
            dynamicFieldsSection.style.display = 'block';
        } else {
            dynamicFieldsSection.style.display = 'none';
            fieldsContainer.innerHTML = '';
        }
    });
    
    // Trigger change event if a type is already selected
    if (reportTypeSelect.value) {
        reportTypeSelect.dispatchEvent(new Event('change'));
    }

    // File upload functionality
    initFileUpload();
});

function generateDynamicFields(fields, frequency) {
    const container = document.getElementById('fieldsContainer');
    container.innerHTML = '';
    
    if (fields.length === 0) {
        container.innerHTML = '<p class="text-muted">No specific fields required for this report type.</p>';
        return;
    }
    
    const row = document.createElement('div');
    row.className = 'row';
    
    fields.forEach((field, index) => {
        const col = document.createElement('div');
        col.className = 'col-md-6 mb-3';
        
        const fieldName = field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        const fieldId = 'field_' + field;
        
        let inputHtml = '';
        
        // Determine input type based on field name
        if (field.includes('date')) {
            inputHtml = `<input type="date" class="form-control" id="${fieldId}" name="${fieldId}" value="<?php echo htmlspecialchars($_POST['${fieldId}'] ?? ''); ?>">`;
        } else if (field.includes('email')) {
            inputHtml = `<input type="email" class="form-control" id="${fieldId}" name="${fieldId}" value="<?php echo htmlspecialchars($_POST['${fieldId}'] ?? ''); ?>" placeholder="Enter ${fieldName.toLowerCase()}">`;
        } else if (field.includes('phone')) {
            inputHtml = `<input type="tel" class="form-control" id="${fieldId}" name="${fieldId}" value="<?php echo htmlspecialchars($_POST['${fieldId}'] ?? ''); ?>" placeholder="Enter ${fieldName.toLowerCase()}">`;
        } else if (field.includes('count') || field.includes('number') || field.includes('total') || field.includes('rate')) {
            inputHtml = `<input type="number" class="form-control" id="${fieldId}" name="${fieldId}" value="<?php echo htmlspecialchars($_POST['${fieldId}'] ?? ''); ?>" placeholder="Enter ${fieldName.toLowerCase()}" min="0">`;
        } else if (field.includes('description') || field.includes('details') || field.includes('notes')) {
            inputHtml = `<textarea class="form-control" id="${fieldId}" name="${fieldId}" rows="3" placeholder="Enter ${fieldName.toLowerCase()}"><?php echo htmlspecialchars($_POST['${fieldId}'] ?? ''); ?></textarea>`;
        } else {
            inputHtml = `<input type="text" class="form-control" id="${fieldId}" name="${fieldId}" value="<?php echo htmlspecialchars($_POST['${fieldId}'] ?? ''); ?>" placeholder="Enter ${fieldName.toLowerCase()}">`;
        }
        
        col.innerHTML = `
            <label for="${fieldId}" class="form-label">${fieldName}</label>
            ${inputHtml}
        `;
        
        row.appendChild(col);
        
        // Add row break every 2 fields
        if ((index + 1) % 2 === 0 || index === fields.length - 1) {
            container.appendChild(row);
            if (index < fields.length - 1) {
                const newRow = document.createElement('div');
                newRow.className = 'row';
                row = newRow;
            }
        }
    });
}

// File upload functionality
let selectedFiles = [];
let totalSize = 0;
const maxFileSize = 10 * 1024 * 1024; // 10MB
const maxTotalSize = 50 * 1024 * 1024; // 50MB

function initFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const selectFilesBtn = document.getElementById('selectFilesBtn');
    const fileList = document.getElementById('fileList');
    const fileListContainer = document.getElementById('fileListContainer');

    if (!uploadZone || !fileInput || !selectFilesBtn) return;

    // Select files button
    selectFilesBtn.addEventListener('click', () => {
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
        updateFileInput(e.target.files);
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
        handleFiles(e.dataTransfer.files);
        updateFileInput(e.dataTransfer.files);
    });
}

function updateFileInput(files) {
    // Update the actual file input with the selected files
    const fileInput = document.getElementById('fileInput');
    const dt = new DataTransfer();

    for (let file of files) {
        dt.items.add(file);
    }

    fileInput.files = dt.files;
}

function handleFiles(files) {
    const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
    const validFiles = [];
    let newTotalSize = 0;

    // Clear previous selections
    selectedFiles = [];
    totalSize = 0;

    for (let file of files) {
        const extension = file.name.split('.').pop().toLowerCase();

        // Validate file type
        if (!allowedTypes.includes(extension)) {
            showAlert('error', `File "${file.name}" has an unsupported format.`);
            continue;
        }

        // Validate file size
        if (file.size > maxFileSize) {
            showAlert('error', `File "${file.name}" exceeds 10MB limit.`);
            continue;
        }

        validFiles.push(file);
        newTotalSize += file.size;
    }

    // Check total size
    if (newTotalSize > maxTotalSize) {
        showAlert('error', 'Total file size exceeds 50MB limit.');
        return;
    }

    // Set valid files
    selectedFiles = validFiles;
    totalSize = newTotalSize;

    updateFileList();

    if (validFiles.length > 0) {
        showAlert('success', `${validFiles.length} file(s) selected and ready for upload.`);
    }
}

function updateFileList() {
    const fileList = document.getElementById('fileList');
    const fileListContainer = document.getElementById('fileListContainer');

    if (selectedFiles.length === 0) {
        fileList.style.display = 'none';
        return;
    }

    fileList.style.display = 'block';
    fileListContainer.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item d-flex align-items-center justify-content-between p-2 border rounded mb-2';

        const fileIcon = getFileIcon(file.name);
        const fileSize = formatFileSize(file.size);

        fileItem.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="${fileIcon} me-2"></i>
                <div>
                    <div class="fw-medium">${file.name}</div>
                    <small class="text-muted">${fileSize}</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                <i class="fas fa-times"></i>
            </button>
        `;

        fileListContainer.appendChild(fileItem);
    });

    // Update total size display
    const totalSizeElement = document.createElement('div');
    totalSizeElement.className = 'mt-2 text-muted';
    totalSizeElement.innerHTML = `<small>Total size: ${formatFileSize(totalSize)} / ${formatFileSize(maxTotalSize)}</small>`;
    fileListContainer.appendChild(totalSizeElement);
}

function removeFile(index) {
    totalSize -= selectedFiles[index].size;
    selectedFiles.splice(index, 1);
    updateFileList();

    // Update the file input
    const fileInput = document.getElementById('fileInput');
    const dt = new DataTransfer();

    for (let file of selectedFiles) {
        dt.items.add(file);
    }

    fileInput.files = dt.files;
}

function getFileIcon(filename) {
    const extension = filename.split('.').pop().toLowerCase();

    switch (extension) {
        case 'pdf': return 'fas fa-file-pdf text-danger';
        case 'doc':
        case 'docx': return 'fas fa-file-word text-primary';
        case 'xls':
        case 'xlsx': return 'fas fa-file-excel text-success';
        case 'ppt':
        case 'pptx': return 'fas fa-file-powerpoint text-warning';
        case 'txt': return 'fas fa-file-alt text-secondary';
        case 'jpg':
        case 'jpeg':
        case 'png': return 'fas fa-file-image text-info';
        default: return 'fas fa-file text-muted';
    }
}

function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
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

// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reportForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            const submitButtons = form.querySelectorAll('button[type="submit"]');
            const fileInput = document.getElementById('fileInput');

            // Show loading state
            submitButtons.forEach(btn => {
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

                // Store original text for restoration
                btn.dataset.originalText = originalText;
            });

            // Show progress message if files are selected
            if (fileInput && fileInput.files.length > 0) {
                showAlert('info', `Creating report and uploading ${fileInput.files.length} file(s)... Please wait.`);
            }

            // Re-enable buttons after a timeout (in case of errors)
            setTimeout(() => {
                submitButtons.forEach(btn => {
                    btn.disabled = false;
                    if (btn.dataset.originalText) {
                        btn.innerHTML = btn.dataset.originalText;
                    }
                });
            }, 30000); // 30 seconds timeout
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
