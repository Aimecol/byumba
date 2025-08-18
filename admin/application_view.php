<?php
/**
 * Application View Page
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_applications');

$application_id = $_GET['id'] ?? null;

if (!$application_id) {
    header('Location: applications.php');
    exit;
}

// Page configuration
$page_title = 'Application Details';
$breadcrumbs = [
    ['title' => 'Applications', 'url' => 'applications.php'],
    ['title' => 'Application Details']
];

$message = '';
$error = '';

// Get application details
try {
    $app_query = "SELECT a.*, 
                         u.first_name, u.last_name, u.email, u.phone, u.address,
                         ctt.name as certificate_type_name,
                         ct.description as certificate_description
                  FROM applications a
                  JOIN users u ON a.user_id = u.id
                  JOIN certificate_types ct ON a.certificate_type_id = ct.id
                  JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id
                  WHERE a.id = :id AND ctt.language_code = 'en'";
    
    $app_stmt = $db->prepare($app_query);
    $app_stmt->bindParam(':id', $application_id);
    $app_stmt->execute();
    $application = $app_stmt->fetch();
    
    if (!$application) {
        $error = 'Application not found.';
    }
} catch(PDOException $e) {
    $error = 'Error loading application: ' . $e->getMessage();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $application) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        try {
            $new_status = $_POST['status'];
            $notes = sanitizeInput($_POST['notes'] ?? '');
            
            $update_query = "UPDATE applications SET status = :status, notes = :notes";
            
            // Set approval/completion dates based on status
            if ($new_status === 'approved') {
                $update_query .= ", approved_date = NOW()";
            } elseif ($new_status === 'completed') {
                $update_query .= ", completed_date = NOW()";
            }
            
            $update_query .= " WHERE id = :id";
            
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':status', $new_status);
            $update_stmt->bindParam(':notes', $notes);
            $update_stmt->bindParam(':id', $application_id);
            $update_stmt->execute();
            
            // Update local data
            $application['status'] = $new_status;
            $application['notes'] = $notes;
            if ($new_status === 'approved') {
                $application['approved_date'] = date('Y-m-d H:i:s');
            } elseif ($new_status === 'completed') {
                $application['completed_date'] = date('Y-m-d H:i:s');
            }
            
            logAdminActivity('update_application_status', "Updated application $application_id status to $new_status");
            $message = 'Application status updated successfully.';
            
        } catch(PDOException $e) {
            $error = 'Error updating application: ' . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
    <div class="text-center">
        <a href="applications.php" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Applications
        </a>
    </div>
<?php else: ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Application Details</h1>
        <p class="text-muted">Certificate application information</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                <i class="fas fa-edit me-2"></i>Update Status
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <a href="applications.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Application Information -->
    <div class="col-lg-8 mb-4">
        <!-- Application Header -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        Application #<?php echo htmlspecialchars($application['application_number']); ?>
                    </h5>
                    <?php echo getStatusBadge($application['status']); ?>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Certificate Type</h6>
                        <p class="mb-3"><?php echo htmlspecialchars($application['certificate_type_name']); ?></p>
                        
                        <h6 class="text-muted">Submitted Date</h6>
                        <p class="mb-3"><?php echo formatDisplayDateTime($application['submitted_date']); ?></p>
                        
                        <?php if ($application['approved_date']): ?>
                            <h6 class="text-muted">Approved Date</h6>
                            <p class="mb-3"><?php echo formatDisplayDateTime($application['approved_date']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Payment Status</h6>
                        <p class="mb-3"><?php echo getStatusBadge($application['payment_status']); ?></p>
                        
                        <?php if ($application['payment_code']): ?>
                            <h6 class="text-muted">Payment Code</h6>
                            <p class="mb-3"><?php echo htmlspecialchars($application['payment_code']); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($application['completed_date']): ?>
                            <h6 class="text-muted">Completed Date</h6>
                            <p class="mb-3"><?php echo formatDisplayDateTime($application['completed_date']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($application['purpose']): ?>
                    <h6 class="text-muted">Purpose</h6>
                    <p class="mb-3"><?php echo htmlspecialchars($application['purpose']); ?></p>
                <?php endif; ?>
                
                <?php if ($application['notes']): ?>
                    <h6 class="text-muted">Admin Notes</h6>
                    <div class="alert alert-info">
                        <?php echo nl2br(htmlspecialchars($application['notes'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Application Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Application Details</h5>
            </div>
            <div class="card-body">
                <?php if ($application['certificate_description']): ?>
                    <div class="mb-4">
                        <h6 class="text-muted">Certificate Description</h6>
                        <p><?php echo nl2br(htmlspecialchars($application['certificate_description'])); ?></p>
                    </div>
                <?php endif; ?>
                
                <!-- Additional application fields can be added here based on certificate type -->
                <div class="row">
                    <?php if ($application['baptism_date']): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Baptism Date</h6>
                            <p><?php echo formatDisplayDate($application['baptism_date']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['baptism_place']): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Baptism Place</h6>
                            <p><?php echo htmlspecialchars($application['baptism_place']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['confirmation_date']): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Confirmation Date</h6>
                            <p><?php echo formatDisplayDate($application['confirmation_date']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['marriage_date']): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Marriage Date</h6>
                            <p><?php echo formatDisplayDate($application['marriage_date']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['spouse_name']): ?>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Spouse Name</h6>
                            <p><?php echo htmlspecialchars($application['spouse_name']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Application Timeline</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Application Submitted</h6>
                            <p class="text-muted mb-0"><?php echo formatDisplayDateTime($application['submitted_date']); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($application['status'] !== 'pending'): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Status: <?php echo ucfirst($application['status']); ?></h6>
                                <p class="text-muted mb-0">Status updated</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['approved_date']): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Application Approved</h6>
                                <p class="text-muted mb-0"><?php echo formatDisplayDateTime($application['approved_date']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($application['completed_date']): ?>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Application Completed</h6>
                                <p class="text-muted mb-0"><?php echo formatDisplayDateTime($application['completed_date']); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Applicant Information -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Applicant Information</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="user-avatar mb-2">
                        <div class="avatar-circle mx-auto" style="width: 80px; height: 80px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; font-weight: 600;">
                            <?php echo strtoupper(substr($application['first_name'], 0, 1) . substr($application['last_name'], 0, 1)); ?>
                        </div>
                    </div>
                    <h6 class="mb-1"><?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?></h6>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($application['email']); ?></p>
                </div>
                
                <hr>
                
                <div class="mb-3">
                    <h6 class="text-muted mb-1">Email</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($application['email']); ?></p>
                </div>
                
                <?php if ($application['phone']): ?>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Phone</h6>
                        <p class="mb-0"><?php echo htmlspecialchars($application['phone']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($application['address']): ?>
                    <div class="mb-3">
                        <h6 class="text-muted mb-1">Address</h6>
                        <p class="mb-0"><?php echo htmlspecialchars($application['address']); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="d-grid">
                    <a href="user_view.php?id=<?php echo $application['user_id']; ?>" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-user me-2"></i>View User Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <i class="fas fa-edit me-2"></i>Update Status
                    </button>
                    
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print Application
                    </button>
                    
                    <a href="mailto:<?php echo htmlspecialchars($application['email']); ?>" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-envelope me-2"></i>Email Applicant
                    </a>
                    
                    <?php if ($application['phone']): ?>
                        <a href="tel:<?php echo htmlspecialchars($application['phone']); ?>" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>Call Applicant
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <div class="modal-header">
                    <h5 class="modal-title">Update Application Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" <?php echo $application['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $application['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="approved" <?php echo $application['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="completed" <?php echo $application['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="rejected" <?php echo $application['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Add any notes about this status change..."><?php echo htmlspecialchars($application['notes'] ?? ''); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>

<?php
$additional_css = '
<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: "";
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
}

.timeline-marker {
    position: absolute;
    left: -1.75rem;
    top: 0.25rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    border-left: 3px solid #3498db;
}

@media print {
    .btn, .modal, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
}
</style>
';

include 'includes/footer.php';
?>
