<?php
/**
 * Jobs Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_jobs');

// Page configuration
$page_title = 'Jobs Management';
$breadcrumbs = [
    ['title' => 'Jobs']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$job_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'toggle_status' && $job_id) {
        try {
            $is_active = $_POST['is_active'] ? 1 : 0;
            
            $query = "UPDATE jobs SET is_active = :is_active WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':id', $job_id);
            $stmt->execute();
            
            $status = $is_active ? 'activated' : 'deactivated';
            logAdminActivity('toggle_job_status', "Job $job_id $status");
            $message = "Job $status successfully.";
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error updating job: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete' && $job_id) {
        try {
            $query = "DELETE FROM jobs WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $job_id);
            $stmt->execute();
            
            logAdminActivity('delete_job', "Deleted job ID: $job_id");
            $message = 'Job deleted successfully.';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error deleting job: ' . $e->getMessage();
        }
    }
}

// Get jobs data
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$parish_filter = $_GET['parish'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(j.title LIKE :search OR j.job_number LIKE :search OR j.description LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($category_filter) {
    $where_conditions[] = "j.job_category_id = :category";
    $params[':category'] = $category_filter;
}

if ($status_filter) {
    if ($status_filter === 'active') {
        $where_conditions[] = "j.is_active = 1 AND j.application_deadline >= CURDATE()";
    } elseif ($status_filter === 'expired') {
        $where_conditions[] = "j.application_deadline < CURDATE()";
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = "j.is_active = 0";
    }
}

if ($parish_filter) {
    $where_conditions[] = "j.parish_id = :parish";
    $params[':parish'] = $parish_filter;
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM jobs j
                LEFT JOIN parishes p ON j.parish_id = p.id
                $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_jobs = $count_stmt->fetch()['total'];
$total_pages = ceil($total_jobs / $per_page);

// Get jobs
$query = "SELECT j.*, 
                 jct.name as category_name,
                 p.name as parish_name
          FROM jobs j
          JOIN job_categories jc ON j.job_category_id = jc.id
          JOIN job_category_translations jct ON jc.id = jct.job_category_id
          LEFT JOIN parishes p ON j.parish_id = p.id
          $where_clause AND jct.language_code = 'en'
          ORDER BY j.created_at DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$jobs = $stmt->fetchAll();

// Get categories and parishes for filters
$cat_query = "SELECT jc.id, jct.name 
              FROM job_categories jc
              JOIN job_category_translations jct ON jc.id = jct.job_category_id
              WHERE jct.language_code = 'en' AND jc.is_active = 1
              ORDER BY jct.name";
$cat_stmt = $db->prepare($cat_query);
$cat_stmt->execute();
$categories = $cat_stmt->fetchAll();

$parish_query = "SELECT id, name FROM parishes WHERE is_active = 1 ORDER BY name";
$parish_stmt = $db->prepare($parish_query);
$parish_stmt->execute();
$parishes = $parish_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Jobs Management</h1>
        <p class="text-muted">Manage job postings and employment opportunities</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <a href="job_form.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Post New Job
            </a>
            <a href="job_categories.php" class="btn btn-outline-secondary">
                <i class="fas fa-tags me-2"></i>Categories
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
            <div class="col-md-3">
                <label for="search" class="form-label">Search Jobs</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by title or description" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" 
                                <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="parish" class="form-label">Parish</label>
                <select class="form-select" id="parish" name="parish">
                    <option value="">All Parishes</option>
                    <?php foreach ($parishes as $parish): ?>
                        <option value="<?php echo $parish['id']; ?>" 
                                <?php echo $parish_filter == $parish['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($parish['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Jobs</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="expired" <?php echo $status_filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    <a href="jobs.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Jobs Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-briefcase text-primary mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($jobs, fn($j) => $j['is_active'] && strtotime($j['application_deadline']) >= time())); ?></h4>
                <p class="text-muted mb-0">Active Jobs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-clock text-warning mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($jobs, fn($j) => strtotime($j['application_deadline']) < time())); ?></h4>
                <p class="text-muted mb-0">Expired</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users text-success mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($jobs, fn($j) => $j['employment_type'] === 'full_time')); ?></h4>
                <p class="text-muted mb-0">Full Time</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-handshake text-info mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($jobs, fn($j) => $j['employment_type'] === 'volunteer')); ?></h4>
                <p class="text-muted mb-0">Volunteer</p>
            </div>
        </div>
    </div>
</div>

<!-- Jobs Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Job Postings 
            <span class="badge bg-primary"><?php echo number_format($total_jobs); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('jobsTable', 'jobs')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($jobs)): ?>
            <div class="text-center py-5">
                <i class="fas fa-briefcase text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Jobs Found</h4>
                <p class="text-muted">No job postings match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="jobsTable">
                    <thead>
                        <tr>
                            <th>Job Details</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Salary</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                            <?php 
                            $is_expired = strtotime($job['application_deadline']) < time();
                            $days_left = ceil((strtotime($job['application_deadline']) - time()) / (60 * 60 * 24));
                            ?>
                            <tr class="<?php echo $is_expired ? 'table-warning' : ''; ?>">
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($job['job_number']); ?></small>
                                        <?php if ($job['description']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($job['description'], 0, 80)) . '...'; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($job['category_name']); ?></span>
                                </td>
                                <td>
                                    <?php if ($job['parish_name']): ?>
                                        <strong><?php echo htmlspecialchars($job['parish_name']); ?></strong><br>
                                    <?php endif; ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($job['location'] ?? 'Not specified'); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $type_badges = [
                                        'full_time' => 'bg-primary',
                                        'part_time' => 'bg-secondary',
                                        'contract' => 'bg-warning',
                                        'volunteer' => 'bg-success'
                                    ];
                                    $badge_class = $type_badges[$job['employment_type']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $job['employment_type'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($job['salary_range']): ?>
                                        <small><?php echo htmlspecialchars($job['salary_range']); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo formatDisplayDate($job['application_deadline']); ?></strong>
                                    <?php if (!$is_expired): ?>
                                        <br><small class="text-success"><?php echo $days_left; ?> days left</small>
                                    <?php else: ?>
                                        <br><small class="text-danger">Expired</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($job['is_active']): ?>
                                        <?php if ($is_expired): ?>
                                            <span class="badge bg-warning">Expired</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="job_view.php?id=<?php echo $job['id']; ?>" 
                                           class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="job_form.php?id=<?php echo $job['id']; ?>" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-<?php echo $job['is_active'] ? 'warning' : 'success'; ?>" 
                                                onclick="toggleStatus(<?php echo $job['id']; ?>, <?php echo $job['is_active'] ? 'false' : 'true'; ?>)" 
                                                title="<?php echo $job['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-<?php echo $job['is_active'] ? 'pause' : 'play'; ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteJob(<?php echo $job['id']; ?>)" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_jobs); ?> 
                        of <?php echo number_format($total_jobs); ?> jobs
                    </div>
                    <?php 
                    $base_url = 'jobs.php?search=' . urlencode($search) . '&category=' . urlencode($category_filter) . '&status=' . urlencode($status_filter) . '&parish=' . urlencode($parish_filter);
                    echo generatePagination($page, $total_pages, $base_url); 
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Status Toggle Form -->
<form id="statusForm" method="POST" style="display: none;">
    <input type="hidden" name="is_active" id="statusValue">
</form>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
</form>

<?php
$additional_js = '
<script>
function toggleStatus(jobId, activate) {
    if (confirm("Are you sure you want to " + (activate ? "activate" : "deactivate") + " this job?")) {
        document.getElementById("statusValue").value = activate ? "1" : "0";
        document.getElementById("statusForm").action = "jobs.php?action=toggle_status&id=" + jobId;
        document.getElementById("statusForm").submit();
    }
}

function deleteJob(jobId) {
    if (confirm("Are you sure you want to delete this job? This action cannot be undone.")) {
        document.getElementById("deleteForm").action = "jobs.php?action=delete&id=" + jobId;
        document.getElementById("deleteForm").submit();
    }
}
</script>
';

include 'includes/footer.php';
?>
