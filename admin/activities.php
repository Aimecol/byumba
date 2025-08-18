<?php
/**
 * Admin Activities Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('view_dashboard');

// Page configuration
$page_title = 'Admin Activities';
$breadcrumbs = [
    ['title' => 'Activities']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$activity_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'clear_old' && hasPermission('manage_users')) {
        try {
            $days = (int)($_POST['days'] ?? 30);
            $query = "DELETE FROM admin_activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL :days DAY)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':days', $days, PDO::PARAM_INT);
            $stmt->execute();
            
            $deleted_count = $stmt->rowCount();
            logAdminActivity('clear_old_activities', "Cleared $deleted_count activities older than $days days");
            $message = "Successfully cleared $deleted_count old activity records.";
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error clearing old activities: ' . $e->getMessage();
        }
    }
}

// Pagination and filtering
$page = (int)($_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

// Filters
$admin_filter = $_GET['admin'] ?? '';
$action_filter = $_GET['action_type'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search = $_GET['search'] ?? '';

// Build query conditions
$where_conditions = [];
$params = [];

if (!empty($admin_filter)) {
    $where_conditions[] = 'aal.admin_email = :admin_email';
    $params[':admin_email'] = $admin_filter;
}

if (!empty($action_filter)) {
    $where_conditions[] = 'aal.action = :action';
    $params[':action'] = $action_filter;
}

if (!empty($date_from)) {
    $where_conditions[] = 'DATE(aal.created_at) >= :date_from';
    $params[':date_from'] = $date_from;
}

if (!empty($date_to)) {
    $where_conditions[] = 'DATE(aal.created_at) <= :date_to';
    $params[':date_to'] = $date_to;
}

if (!empty($search)) {
    $where_conditions[] = '(aal.action LIKE :search OR aal.details LIKE :search OR aal.admin_email LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_query = "SELECT COUNT(*) as total FROM admin_activity_log aal $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_records = $count_stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// Get activities
$query = "SELECT aal.*, u.first_name, u.last_name 
          FROM admin_activity_log aal
          LEFT JOIN users u ON aal.admin_id = u.id
          $where_clause
          ORDER BY aal.created_at DESC 
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$activities = $stmt->fetchAll();

// Get unique admins for filter
$admins_query = "SELECT DISTINCT admin_email, admin_id FROM admin_activity_log ORDER BY admin_email";
$admins_stmt = $db->prepare($admins_query);
$admins_stmt->execute();
$admins = $admins_stmt->fetchAll();

// Get unique actions for filter
$actions_query = "SELECT DISTINCT action FROM admin_activity_log ORDER BY action";
$actions_stmt = $db->prepare($actions_query);
$actions_stmt->execute();
$action_types = $actions_stmt->fetchAll();

// Get activity statistics
$stats_query = "SELECT 
                    COUNT(*) as total_activities,
                    COUNT(DISTINCT admin_id) as unique_admins,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as today_activities,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as week_activities
                FROM admin_activity_log";
$stats_stmt = $db->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Admin Activities</h1>
        <p class="text-muted">Monitor and track administrative actions</p>
    </div>
    <div class="col-md-6 text-end">
        <?php if (hasPermission('manage_users')): ?>
            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearActivitiesModal">
                <i class="fas fa-trash me-2"></i>Clear Old Activities
            </button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-primary" onclick="exportActivities()">
            <i class="fas fa-download me-2"></i>Export
        </button>
    </div>
</div>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #3498db, #5dade2);">
            <div class="stats-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['total_activities']); ?></div>
            <div class="stats-label">Total Activities</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #27ae60, #58d68d);">
            <div class="stats-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['unique_admins']); ?></div>
            <div class="stats-label">Active Admins</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f39c12, #f7dc6f);">
            <div class="stats-icon">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['today_activities']); ?></div>
            <div class="stats-label">Today's Activities</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #9b59b6, #bb8fce);">
            <div class="stats-icon">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['week_activities']); ?></div>
            <div class="stats-label">This Week</div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-filter me-2"></i>Filters
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="action" value="list">
            
            <div class="col-md-3">
                <label for="admin" class="form-label">Admin</label>
                <select class="form-select" id="admin" name="admin">
                    <option value="">All Admins</option>
                    <?php foreach ($admins as $admin): ?>
                        <option value="<?php echo htmlspecialchars($admin['admin_email']); ?>" 
                                <?php echo $admin_filter === $admin['admin_email'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($admin['admin_email']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="action_type" class="form-label">Action Type</label>
                <select class="form-select" id="action_type" name="action_type">
                    <option value="">All Actions</option>
                    <?php foreach ($action_types as $action_type): ?>
                        <option value="<?php echo htmlspecialchars($action_type['action']); ?>" 
                                <?php echo $action_filter === $action_type['action'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $action_type['action']))); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="date_from" class="form-label">From Date</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
            </div>
            
            <div class="col-md-2">
                <label for="date_to" class="form-label">To Date</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
            </div>
            
            <div class="col-md-2">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search activities..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Apply Filters
                </button>
                <a href="activities.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Activities List -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>Activity Log
        </h5>
        <span class="badge bg-primary"><?php echo number_format($total_records); ?> total records</span>
    </div>
    <div class="card-body">
        <?php if (empty($activities)): ?>
            <div class="text-center py-5">
                <i class="fas fa-history fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No Activities Found</h4>
                <p class="text-muted">No activities match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="activitiesTable">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Admin</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td>
                                    <div class="fw-bold"><?php echo date('M j, Y', strtotime($activity['created_at'])); ?></div>
                                    <small class="text-muted"><?php echo date('g:i A', strtotime($activity['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="width: 35px; height: 35px; background: #3498db; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            <?php
                                            $name = ($activity['first_name'] && $activity['last_name'])
                                                ? $activity['first_name'] . ' ' . $activity['last_name']
                                                : $activity['admin_email'];
                                            echo strtoupper(substr($name, 0, 1));
                                            ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold">
                                                <?php echo htmlspecialchars($name); ?>
                                            </div>
                                            <small class="text-muted"><?php echo htmlspecialchars($activity['admin_email']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo getActionBadgeColor($activity['action']); ?>">
                                        <i class="fas <?php echo getActionIcon($activity['action']); ?> me-1"></i>
                                        <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $activity['action']))); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="activity-details">
                                        <?php echo htmlspecialchars($activity['details'] ?: 'No details provided'); ?>
                                    </div>
                                </td>
                                <td>
                                    <code class="small"><?php echo htmlspecialchars($activity['ip_address']); ?></code>
                                </td>
                                <td>
                                    <div class="user-agent-info" title="<?php echo htmlspecialchars($activity['user_agent']); ?>">
                                        <?php echo getBrowserInfo($activity['user_agent']); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing <?php echo number_format($offset + 1); ?> to <?php echo number_format(min($offset + $per_page, $total_records)); ?>
                        of <?php echo number_format($total_records); ?> entries
                    </div>
                    <?php
                    $base_url = 'activities.php?action=list';
                    if ($admin_filter) $base_url .= '&admin=' . urlencode($admin_filter);
                    if ($action_filter) $base_url .= '&action_type=' . urlencode($action_filter);
                    if ($date_from) $base_url .= '&date_from=' . urlencode($date_from);
                    if ($date_to) $base_url .= '&date_to=' . urlencode($date_to);
                    if ($search) $base_url .= '&search=' . urlencode($search);

                    echo generatePagination($page, $total_pages, $base_url);
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Clear Activities Modal -->
<?php if (hasPermission('manage_users')): ?>
<div class="modal fade" id="clearActivitiesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old Activities</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="clear_old">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This action will permanently delete old activity records and cannot be undone.
                    </div>
                    <div class="mb-3">
                        <label for="days" class="form-label">Delete activities older than:</label>
                        <select class="form-select" id="days" name="days" required>
                            <option value="30">30 days</option>
                            <option value="60">60 days</option>
                            <option value="90" selected>90 days</option>
                            <option value="180">6 months</option>
                            <option value="365">1 year</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clear Activities</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
/**
 * Helper function to get action badge color
 */
function getActionBadgeColor($action) {
    $colors = [
        'login' => 'success',
        'logout' => 'secondary',
        'create' => 'primary',
        'update' => 'info',
        'delete' => 'danger',
        'approve' => 'success',
        'reject' => 'danger',
        'toggle' => 'warning',
        'send' => 'info',
        'clear' => 'warning'
    ];

    foreach ($colors as $key => $color) {
        if (strpos($action, $key) !== false) {
            return $color;
        }
    }

    return 'secondary';
}

/**
 * Helper function to get action icon
 */
function getActionIcon($action) {
    $icons = [
        'login' => 'fa-sign-in-alt',
        'logout' => 'fa-sign-out-alt',
        'create' => 'fa-plus',
        'update' => 'fa-edit',
        'delete' => 'fa-trash',
        'approve' => 'fa-check',
        'reject' => 'fa-times',
        'toggle' => 'fa-toggle-on',
        'send' => 'fa-paper-plane',
        'clear' => 'fa-broom',
        'view' => 'fa-eye',
        'download' => 'fa-download'
    ];

    foreach ($icons as $key => $icon) {
        if (strpos($action, $key) !== false) {
            return $icon;
        }
    }

    return 'fa-cog';
}

/**
 * Helper function to get browser info from user agent
 */
function getBrowserInfo($userAgent) {
    if (empty($userAgent)) return 'Unknown';

    $browsers = [
        'Chrome' => 'fa-chrome',
        'Firefox' => 'fa-firefox',
        'Safari' => 'fa-safari',
        'Edge' => 'fa-edge',
        'Opera' => 'fa-opera'
    ];

    foreach ($browsers as $browser => $icon) {
        if (strpos($userAgent, $browser) !== false) {
            return '<i class="fab ' . $icon . ' me-1"></i>' . $browser;
        }
    }

    return '<i class="fas fa-globe me-1"></i>Unknown';
}

$additional_js = '
<script>
// Export activities function
function exportActivities() {
    const params = new URLSearchParams(window.location.search);
    params.set("export", "csv");
    window.location.href = "activities_export.php?" + params.toString();
}

// Auto-refresh activities every 30 seconds
let autoRefresh = setInterval(function() {
    if (document.visibilityState === "visible") {
        // Only refresh if no filters are applied to avoid losing user input
        const urlParams = new URLSearchParams(window.location.search);
        if (!urlParams.has("admin") && !urlParams.has("action_type") &&
            !urlParams.has("date_from") && !urlParams.has("date_to") &&
            !urlParams.has("search")) {
            location.reload();
        }
    }
}, 30000);

// Stop auto-refresh when user is interacting with filters
document.querySelectorAll("input, select").forEach(element => {
    element.addEventListener("focus", function() {
        clearInterval(autoRefresh);
    });
});

// Initialize DataTables for better sorting and searching
$(document).ready(function() {
    if ($("#activitiesTable tbody tr").length > 0) {
        $("#activitiesTable").DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [5] }
            ],
            "language": {
                "search": "Search activities:",
                "lengthMenu": "Show _MENU_ activities per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ activities",
                "infoEmpty": "No activities found",
                "infoFiltered": "(filtered from _MAX_ total activities)"
            }
        });
    }
});

// Tooltip initialization
$(document).ready(function() {
    $("[title]").tooltip();
});
</script>
';

include 'includes/footer.php';
?>
