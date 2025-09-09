<?php
/**
 * View Report Details
 * Diocese of Byumba - School Management System
 */

session_start();
$pageTitle = 'View Report';
require_once 'includes/functions.php';

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

<?php require_once 'includes/footer.php'; ?>
