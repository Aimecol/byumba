<?php
/**
 * Applications Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_applications');

// Page configuration
$page_title = 'Applications Management';
$breadcrumbs = [
    ['title' => 'Applications']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$application_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'update_status' && $application_id) {
        try {
            $new_status = $_POST['status'];
            $notes = $_POST['notes'] ?? '';
            
            $query = "UPDATE applications SET status = :status, notes = :notes";
            
            // Set approval/completion dates based on status
            if ($new_status === 'approved') {
                $query .= ", approved_date = NOW()";
            } elseif ($new_status === 'completed') {
                $query .= ", completed_date = NOW()";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':id', $application_id);
            $stmt->execute();
            
            logAdminActivity('update_application_status', "Updated application $application_id status to $new_status");
            $message = 'Application status updated successfully.';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error updating application: ' . $e->getMessage();
        }
    }
}

// Get applications data
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$certificate_filter = $_GET['certificate'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(a.application_number LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search OR u.email LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($status_filter) {
    $where_conditions[] = "a.status = :status";
    $params[':status'] = $status_filter;
}

if ($certificate_filter) {
    $where_conditions[] = "a.certificate_type_id = :certificate";
    $params[':certificate'] = $certificate_filter;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM applications a
                JOIN users u ON a.user_id = u.id
                $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_applications = $count_stmt->fetch()['total'];
$total_pages = ceil($total_applications / $per_page);

// Get applications
$query = "SELECT a.*, 
                 u.first_name, u.last_name, u.email, u.phone,
                 ctt.name as certificate_type_name
          FROM applications a
          JOIN users u ON a.user_id = u.id
          JOIN certificate_types ct ON a.certificate_type_id = ct.id
          JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id
          $where_clause AND ctt.language_code = 'en'
          ORDER BY a.created_at DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$applications = $stmt->fetchAll();

// Get certificate types for filter
$cert_query = "SELECT ct.id, ctt.name 
               FROM certificate_types ct
               JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id
               WHERE ctt.language_code = 'en' AND ct.is_active = 1
               ORDER BY ctt.name";
$cert_stmt = $db->prepare($cert_query);
$cert_stmt->execute();
$certificate_types = $cert_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Applications Management</h1>
        <p class="text-muted">Manage certificate applications and their status</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <a href="application_form.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Application
            </a>
            <button type="button" class="btn btn-outline-secondary" onclick="refreshPage()">
                <i class="fas fa-sync me-2"></i>Refresh
            </button>
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

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search Applications</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by number, name, or email" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="certificate" class="form-label">Certificate Type</label>
                <select class="form-select" id="certificate" name="certificate">
                    <option value="">All Types</option>
                    <?php foreach ($certificate_types as $cert): ?>
                        <option value="<?php echo $cert['id']; ?>" 
                                <?php echo $certificate_filter == $cert['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cert['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Applications Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Applications List 
            <span class="badge bg-primary"><?php echo number_format($total_applications); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('applicationsTable', 'applications')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($applications)): ?>
            <div class="text-center py-5">
                <i class="fas fa-file-alt text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Applications Found</h4>
                <p class="text-muted">No applications match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="applicationsTable">
                    <thead>
                        <tr>
                            <th>Application #</th>
                            <th>Applicant</th>
                            <th>Certificate Type</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($app['application_number']); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($app['certificate_type_name']); ?></td>
                                <td><?php echo getStatusBadge($app['status']); ?></td>
                                <td>
                                    <?php echo getStatusBadge($app['payment_status']); ?>
                                    <?php if ($app['payment_code']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($app['payment_code']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo formatDisplayDate($app['submitted_date']); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="application_view.php?id=<?php echo $app['id']; ?>" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary" 
                                                onclick="updateStatus(<?php echo $app['id']; ?>)" title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="application_documents.php?id=<?php echo $app['id']; ?>" 
                                           class="btn btn-outline-info" title="Documents">
                                            <i class="fas fa-paperclip"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_applications); ?> 
                        of <?php echo number_format($total_applications); ?> applications
                    </div>
                    <?php 
                    $base_url = 'applications.php?search=' . urlencode($search) . '&status=' . urlencode($status_filter) . '&certificate=' . urlencode($certificate_filter);
                    echo generatePagination($page, $total_pages, $base_url); 
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="statusForm">
                <div class="modal-header">
                    <h5 class="modal-title">Update Application Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="application_id" id="statusApplicationId">
                    
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Status</label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="approved">Approved</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="statusNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="statusNotes" name="notes" rows="3" 
                                  placeholder="Add any notes about this status change..."></textarea>
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

<?php
$additional_js = '
<script>
function updateStatus(applicationId) {
    document.getElementById("statusApplicationId").value = applicationId;
    document.getElementById("statusForm").action = "applications.php?action=update_status&id=" + applicationId;
    var statusModal = new bootstrap.Modal(document.getElementById("statusModal"));
    statusModal.show();
}
</script>
';

include 'includes/footer.php';
?>
