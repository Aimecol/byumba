<?php
/**
 * Create New Report
 * Diocese of Byumba - School Management System
 */

session_start();
$pageTitle = 'Create Report';
require_once 'includes/functions.php';

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
                // Log activity
                $action = $status === 'submitted' ? 'submit_report' : 'create_draft';
                $description = $status === 'submitted' ? 
                    "Submitted report: {$result['report_number']}" : 
                    "Created draft report: {$result['report_number']}";
                
                $schoolAuth->logActivity(
                    $currentUser['school_id'], 
                    $currentUser['user_id'], 
                    $action, 
                    $description
                );
                
                $_SESSION['success_message'] = $status === 'submitted' ? 
                    'Report submitted successfully!' : 
                    'Report draft saved successfully!';
                
                header('Location: view-report.php?id=' . $result['report_id']);
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

<form method="POST" class="needs-validation auto-save" id="reportForm" novalidate>
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
</script>

<?php require_once 'includes/footer.php'; ?>
