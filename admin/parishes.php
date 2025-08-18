<?php
/**
 * Parishes Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_parishes');

// Page configuration
$page_title = 'Parishes Management';
$breadcrumbs = [
    ['title' => 'Parishes']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$parish_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'toggle_status' && $parish_id) {
        try {
            $is_active = $_POST['is_active'] ? 1 : 0;
            
            $query = "UPDATE parishes SET is_active = :is_active WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':is_active', $is_active);
            $stmt->bindParam(':id', $parish_id);
            $stmt->execute();
            
            $status = $is_active ? 'activated' : 'deactivated';
            logAdminActivity('toggle_parish_status', "Parish $parish_id $status");
            $message = "Parish $status successfully.";
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error updating parish: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete' && $parish_id) {
        try {
            // Check if parish has associated records
            $check_query = "SELECT COUNT(*) as count FROM jobs WHERE parish_id = :id";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->bindParam(':id', $parish_id);
            $check_stmt->execute();
            $job_count = $check_stmt->fetch()['count'];
            
            if ($job_count > 0) {
                $error = "Cannot delete parish. It has $job_count associated job(s).";
            } else {
                $query = "DELETE FROM parishes WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':id', $parish_id);
                $stmt->execute();
                
                logAdminActivity('delete_parish', "Deleted parish ID: $parish_id");
                $message = 'Parish deleted successfully.';
                $action = 'list';
            }
        } catch(PDOException $e) {
            $error = 'Error deleting parish: ' . $e->getMessage();
        }
    }
}

// Get parishes data
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(p.name LIKE :search OR p.location LIKE :search OR p.priest_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($status_filter) {
    if ($status_filter === 'active') {
        $where_conditions[] = "p.is_active = 1";
    } elseif ($status_filter === 'inactive') {
        $where_conditions[] = "p.is_active = 0";
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM parishes p $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_parishes = $count_stmt->fetch()['total'];
$total_pages = ceil($total_parishes / $per_page);

// Get parishes with statistics
$query = "SELECT p.*, 
                 COUNT(DISTINCT j.id) as job_count,
                 COUNT(DISTINCT upm.user_id) as member_count
          FROM parishes p
          LEFT JOIN jobs j ON p.id = j.parish_id
          LEFT JOIN user_parish_membership upm ON p.id = upm.parish_id AND upm.is_active = 1
          $where_clause
          GROUP BY p.id
          ORDER BY p.name ASC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$parishes = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Parishes Management</h1>
        <p class="text-muted">Manage diocese parishes and their information</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <a href="parish_form.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Parish
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
            <div class="col-md-6">
                <label for="search" class="form-label">Search Parishes</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by name, location, or priest" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Parishes</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                    <a href="parishes.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Parish Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-church text-primary mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($parishes, fn($p) => $p['is_active'])); ?></h4>
                <p class="text-muted mb-0">Active Parishes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-user-tie text-success mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($parishes, fn($p) => !empty($p['priest_name']))); ?></h4>
                <p class="text-muted mb-0">With Priests</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users text-info mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo array_sum(array_column($parishes, 'member_count')); ?></h4>
                <p class="text-muted mb-0">Total Members</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-briefcase text-warning mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo array_sum(array_column($parishes, 'job_count')); ?></h4>
                <p class="text-muted mb-0">Job Postings</p>
            </div>
        </div>
    </div>
</div>

<!-- Parishes Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Parishes List 
            <span class="badge bg-primary"><?php echo number_format($total_parishes); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('parishesTable', 'parishes')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($parishes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-church text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Parishes Found</h4>
                <p class="text-muted">No parishes match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="parishesTable">
                    <thead>
                        <tr>
                            <th>Parish Name</th>
                            <th>Location</th>
                            <th>Priest</th>
                            <th>Contact</th>
                            <th>Members</th>
                            <th>Jobs</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($parishes as $parish): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="parish-icon me-3" style="width: 40px; height: 40px; background: #3498db; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="fas fa-church"></i>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($parish['name']); ?></strong>
                                            <br><small class="text-muted">ID: <?php echo $parish['id']; ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($parish['location']): ?>
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <?php echo htmlspecialchars($parish['location']); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($parish['priest_name']): ?>
                                        <strong><?php echo htmlspecialchars($parish['priest_name']); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">No priest assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($parish['contact_email'] || $parish['contact_phone']): ?>
                                        <?php if ($parish['contact_email']): ?>
                                            <div><i class="fas fa-envelope text-muted me-1"></i><?php echo htmlspecialchars($parish['contact_email']); ?></div>
                                        <?php endif; ?>
                                        <?php if ($parish['contact_phone']): ?>
                                            <div><i class="fas fa-phone text-muted me-1"></i><?php echo htmlspecialchars($parish['contact_phone']); ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No contact info</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo number_format($parish['member_count']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo number_format($parish['job_count']); ?></span>
                                </td>
                                <td>
                                    <?php if ($parish['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="parish_view.php?id=<?php echo $parish['id']; ?>" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="parish_form.php?id=<?php echo $parish['id']; ?>" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-<?php echo $parish['is_active'] ? 'warning' : 'success'; ?>" 
                                                onclick="toggleStatus(<?php echo $parish['id']; ?>, <?php echo $parish['is_active'] ? 'false' : 'true'; ?>)" 
                                                title="<?php echo $parish['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-<?php echo $parish['is_active'] ? 'pause' : 'play'; ?>"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteParish(<?php echo $parish['id']; ?>)" title="Delete">
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
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_parishes); ?> 
                        of <?php echo number_format($total_parishes); ?> parishes
                    </div>
                    <?php 
                    $base_url = 'parishes.php?search=' . urlencode($search) . '&status=' . urlencode($status_filter);
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
function toggleStatus(parishId, activate) {
    if (confirm("Are you sure you want to " + (activate ? "activate" : "deactivate") + " this parish?")) {
        document.getElementById("statusValue").value = activate ? "1" : "0";
        document.getElementById("statusForm").action = "parishes.php?action=toggle_status&id=" + parishId;
        document.getElementById("statusForm").submit();
    }
}

function deleteParish(parishId) {
    if (confirm("Are you sure you want to delete this parish? This action cannot be undone and will affect all associated records.")) {
        document.getElementById("deleteForm").action = "parishes.php?action=delete&id=" + parishId;
        document.getElementById("deleteForm").submit();
    }
}
</script>
';

include 'includes/footer.php';
?>
