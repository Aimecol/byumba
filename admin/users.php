<?php
/**
 * Users Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_users');

// Page configuration
$page_title = 'Users Management';
$breadcrumbs = [
    ['title' => 'Users']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$user_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'delete' && $user_id) {
        try {
            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            logAdminActivity('delete_user', "Deleted user ID: $user_id");
            $message = 'User deleted successfully.';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error deleting user: ' . $e->getMessage();
        }
    }
}

// Get users data
$search = $_GET['search'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_clause = '';
$params = [];

if ($search) {
    $where_clause = "WHERE (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR phone LIKE :search)";
    $params[':search'] = "%$search%";
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM users $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_users = $count_stmt->fetch()['total'];
$total_pages = ceil($total_users / $per_page);

// Get users
$query = "SELECT u.*, 
                 COUNT(a.id) as application_count,
                 COUNT(m.id) as meeting_count
          FROM users u
          LEFT JOIN applications a ON u.id = a.user_id
          LEFT JOIN meetings m ON u.id = m.user_id
          $where_clause
          GROUP BY u.id
          ORDER BY u.created_at DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Users Management</h1>
        <p class="text-muted">Manage diocese users and their information</p>
    </div>
    <div class="col-md-6 text-end">
        <a href="user_form.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New User
        </a>
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
                <label for="search" class="form-label">Search Users</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by name, email, or phone" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <a href="users.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Users List 
            <span class="badge bg-primary"><?php echo number_format($total_users); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('usersTable', 'users')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($users)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Users Found</h4>
                <p class="text-muted">No users match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applications</th>
                            <th>Meetings</th>
                            <th>Verified</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-2" style="width: 35px; height: 35px; background: #3498db; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                            <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                            <?php if ($user['national_id']): ?>
                                                <br><small class="text-muted">ID: <?php echo htmlspecialchars($user['national_id']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                    <?php if ($user['email_verified']): ?>
                                        <i class="fas fa-check-circle text-success ms-1" title="Email verified"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?>
                                    <?php if ($user['phone_verified']): ?>
                                        <i class="fas fa-check-circle text-success ms-1" title="Phone verified"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $user['application_count']; ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning"><?php echo $user['meeting_count']; ?></span>
                                </td>
                                <td>
                                    <?php if ($user['email_verified'] && $user['phone_verified']): ?>
                                        <span class="badge bg-success">Fully Verified</span>
                                    <?php elseif ($user['email_verified']): ?>
                                        <span class="badge bg-warning">Email Only</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Unverified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo formatDisplayDate($user['created_at']); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="user_view.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="user_form.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-outline-secondary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if (hasPermission('manage_users')): ?>
                                            <button type="button" class="btn btn-outline-danger btn-delete" 
                                                    onclick="deleteUser(<?php echo $user['id']; ?>)" title="Delete">
                                                <i class="fas fa-trash"></i>
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
            <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_users); ?> 
                        of <?php echo number_format($total_users); ?> users
                    </div>
                    <?php echo generatePagination($page, $total_pages, 'users.php?search=' . urlencode($search)); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this user? This action cannot be undone.</p>
                <p class="text-danger"><strong>Warning:</strong> This will also delete all associated applications, meetings, and other data.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="user_id" id="deleteUserId">
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = '
<script>
function deleteUser(userId) {
    document.getElementById("deleteUserId").value = userId;
    var deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
    deleteModal.show();
}
</script>
';

include 'includes/footer.php';
?>
