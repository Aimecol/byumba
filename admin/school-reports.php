<?php
/**
 * School Reports Management - Admin Panel
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'auth.php';
require_once 'functions.php';

requirePermission('view_reports');

// Helper functions for school reports
if (!function_exists('getStatusBadgeClass')) {
    function getStatusBadgeClass($status) {
        switch ($status) {
            case 'draft': return 'bg-secondary';
            case 'submitted': return 'bg-primary';
            case 'under_review': return 'bg-warning';
            case 'approved': return 'bg-success';
            case 'rejected': return 'bg-danger';
            case 'requires_revision': return 'bg-info';
            default: return 'bg-secondary';
        }
    }
}

if (!function_exists('getPriorityBadgeClass')) {
    function getPriorityBadgeClass($priority) {
        switch ($priority) {
            case 'low': return 'bg-light text-dark';
            case 'normal': return 'bg-secondary';
            case 'high': return 'bg-warning';
            case 'urgent': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }
}

$pageTitle = 'School Reports';

// Get filter parameters
$status = $_GET['status'] ?? '';
$school_id = $_GET['school_id'] ?? '';
$type = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;
$offset = ($page - 1) * $limit;

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $reportId = intval($_POST['report_id'] ?? 0);
    $action = $_POST['action'];
    
    if ($reportId && hasPermission('manage_reports')) {
        try {
            switch ($action) {
                case 'approve':
                    $query = "UPDATE school_reports SET status = 'approved', reviewed_by = :admin_id, reviewed_at = NOW() WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindValue(':admin_id', $_SESSION['admin_id']);
                    $stmt->bindValue(':id', $reportId);
                    $stmt->execute();
                    
                    logAdminActivity('approve_school_report', "Approved school report ID: $reportId");
                    $message = 'Report approved successfully.';
                    break;
                    
                case 'reject':
                    $adminNotes = trim($_POST['admin_notes'] ?? '');
                    $query = "UPDATE school_reports SET status = 'rejected', admin_notes = :notes, reviewed_by = :admin_id, reviewed_at = NOW() WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindValue(':notes', $adminNotes);
                    $stmt->bindValue(':admin_id', $_SESSION['admin_id']);
                    $stmt->bindValue(':id', $reportId);
                    $stmt->execute();
                    
                    logAdminActivity('reject_school_report', "Rejected school report ID: $reportId");
                    $message = 'Report rejected successfully.';
                    break;
                    
                case 'request_revision':
                    $adminNotes = trim($_POST['admin_notes'] ?? '');
                    $query = "UPDATE school_reports SET status = 'requires_revision', admin_notes = :notes, reviewed_by = :admin_id, reviewed_at = NOW() WHERE id = :id";
                    $stmt = $db->prepare($query);
                    $stmt->bindValue(':notes', $adminNotes);
                    $stmt->bindValue(':admin_id', $_SESSION['admin_id']);
                    $stmt->bindValue(':id', $reportId);
                    $stmt->execute();
                    
                    logAdminActivity('request_revision_school_report', "Requested revision for school report ID: $reportId");
                    $message = 'Revision requested successfully.';
                    break;
            }
        } catch(PDOException $e) {
            error_log("School report action error: " . $e->getMessage());
            $error = 'Failed to update report status.';
        }
    }
}

// Build query for reports
$whereConditions = ['1=1'];
$params = [];

if ($status) {
    $whereConditions[] = 'sr.status = :status';
    $params[':status'] = $status;
}

if ($school_id) {
    $whereConditions[] = 'sr.school_id = :school_id';
    $params[':school_id'] = $school_id;
}

if ($type) {
    $whereConditions[] = 'rt.type_code = :type';
    $params[':type'] = $type;
}

if ($search) {
    $whereConditions[] = '(sr.title LIKE :search OR sr.report_number LIKE :search OR s.school_name LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$whereClause = implode(' AND ', $whereConditions);

// Get total count
try {
    $countQuery = "SELECT COUNT(*) as total 
                   FROM school_reports sr 
                   JOIN schools s ON sr.school_id = s.id
                   JOIN report_types rt ON sr.report_type_id = rt.id 
                   WHERE $whereClause";
    $countStmt = $db->prepare($countQuery);
    $countStmt->execute($params);
    $totalReports = $countStmt->fetch()['total'];
    $totalPages = ceil($totalReports / $limit);
} catch(PDOException $e) {
    error_log("Count school reports error: " . $e->getMessage());
    $totalReports = 0;
    $totalPages = 1;
}

// Get reports
try {
    $query = "SELECT sr.*, rt.type_name, rt.type_code, s.school_name, s.school_code, s.school_type,
                     su.full_name as submitted_by_name, au.first_name as reviewed_by_name
              FROM school_reports sr
              JOIN schools s ON sr.school_id = s.id
              JOIN report_types rt ON sr.report_type_id = rt.id
              LEFT JOIN school_users su ON sr.submitted_by = su.id
              LEFT JOIN users au ON sr.reviewed_by = au.id
              WHERE $whereClause
              ORDER BY sr.created_at DESC
              LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $reports = $stmt->fetchAll();
} catch(PDOException $e) {
    error_log("Get school reports error: " . $e->getMessage());
    $reports = [];
}

// Get schools for filter
try {
    $schoolsQuery = "SELECT id, school_name, school_code FROM schools WHERE is_active = 1 ORDER BY school_name";
    $schoolsStmt = $db->prepare($schoolsQuery);
    $schoolsStmt->execute();
    $schools = $schoolsStmt->fetchAll();
} catch(PDOException $e) {
    error_log("Get schools error: " . $e->getMessage());
    $schools = [];
}

// Get report types for filter
try {
    $typesQuery = "SELECT DISTINCT rt.type_code, rt.type_name 
                   FROM report_types rt 
                   JOIN school_reports sr ON rt.id = sr.report_type_id 
                   ORDER BY rt.type_name";
    $typesStmt = $db->prepare($typesQuery);
    $typesStmt->execute();
    $reportTypes = $typesStmt->fetchAll();
} catch(PDOException $e) {
    error_log("Get report types error: " . $e->getMessage());
    $reportTypes = [];
}

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-school me-2"></i>School Reports
                    <span class="badge bg-secondary ms-2"><?php echo $totalReports; ?></span>
                </h2>
                <div>
                    <a href="school-reports-export.php<?php echo $search || $status || $school_id || $type ? '?' . http_build_query($_GET) : ''; ?>" 
                       class="btn btn-outline-success">
                        <i class="fas fa-download me-2"></i>Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($message)): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-2">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?php echo htmlspecialchars($search); ?>" 
                                   placeholder="Search reports...">
                        </div>
                        
                        <div class="col-md-2">
                            <label for="school_id" class="form-label">School</label>
                            <select class="form-select" id="school_id" name="school_id">
                                <option value="">All Schools</option>
                                <?php foreach ($schools as $school): ?>
                                    <option value="<?php echo $school['id']; ?>" 
                                            <?php echo $school_id == $school['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($school['school_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="submitted" <?php echo $status === 'submitted' ? 'selected' : ''; ?>>Submitted</option>
                                <option value="under_review" <?php echo $status === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                                <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="requires_revision" <?php echo $status === 'requires_revision' ? 'selected' : ''; ?>>Requires Revision</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Types</option>
                                <?php foreach ($reportTypes as $reportType): ?>
                                    <option value="<?php echo htmlspecialchars($reportType['type_code']); ?>" 
                                            <?php echo $type === $reportType['type_code'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($reportType['type_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filter
                            </button>
                            <a href="school-reports.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <?php if (empty($reports)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">No reports found</h5>
                            <p class="text-muted">
                                <?php if ($search || $status || $school_id || $type): ?>
                                    Try adjusting your filters or <a href="school-reports.php">view all reports</a>.
                                <?php else: ?>
                                    No school reports have been submitted yet.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Report #</th>
                                        <th>School</th>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $report): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($report['report_number']); ?></strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($report['school_name']); ?></strong>
                                                    <small class="text-muted d-block">
                                                        <?php echo htmlspecialchars($report['school_code']); ?> | 
                                                        <?php echo ucfirst($report['school_type']); ?>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo htmlspecialchars($report['type_name']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($report['title']); ?></td>
                                            <td>
                                                <span class="badge <?php echo getStatusBadgeClass($report['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo getPriorityBadgeClass($report['priority']); ?>">
                                                    <?php echo ucfirst($report['priority']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo formatDate($report['submitted_at']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="school-report-view.php?id=<?php echo $report['id']; ?>" 
                                                       class="btn btn-outline-primary" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if (hasPermission('manage_reports') && $report['status'] === 'submitted'): ?>
                                                        <button type="button" class="btn btn-outline-success" 
                                                                onclick="updateReportStatus(<?php echo $report['id']; ?>, 'approve')" 
                                                                title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="showNotesModal(<?php echo $report['id']; ?>, 'request_revision')" 
                                                                title="Request Revision">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="showNotesModal(<?php echo $report['id']; ?>, 'reject')" 
                                                                title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Reports pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notes Modal -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalTitle">Add Notes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="notesForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="modalReportId">
                    <input type="hidden" name="action" id="modalAction">
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Administrator Notes</label>
                        <textarea class="form-control" name="admin_notes" id="admin_notes" rows="4" 
                                  placeholder="Enter notes for the school..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateReportStatus(reportId, action) {
    if (confirm('Are you sure you want to ' + action + ' this report?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="report_id" value="${reportId}">
            <input type="hidden" name="action" value="${action}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function showNotesModal(reportId, action) {
    document.getElementById('modalReportId').value = reportId;
    document.getElementById('modalAction').value = action;
    
    const title = action === 'reject' ? 'Reject Report' : 'Request Revision';
    const btnText = action === 'reject' ? 'Reject' : 'Request Revision';
    const btnClass = action === 'reject' ? 'btn-danger' : 'btn-warning';
    
    document.getElementById('notesModalTitle').textContent = title;
    document.getElementById('modalSubmitBtn').textContent = btnText;
    document.getElementById('modalSubmitBtn').className = 'btn ' + btnClass;
    
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}
</script>

<?php require_once 'includes/footer.php'; ?>
