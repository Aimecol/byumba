<?php
/**
 * Notifications Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_notifications');

// Page configuration
$page_title = 'Notifications Management';
$breadcrumbs = [
    ['title' => 'Notifications']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$notification_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'send_bulk') {
        try {
            $title = sanitizeInput($_POST['title']);
            $message_text = sanitizeInput($_POST['message']);
            $notification_type_id = $_POST['notification_type_id'];
            $user_filter = $_POST['user_filter'] ?? 'all';
            $action_required = isset($_POST['action_required']) ? 1 : 0;
            $action_text = sanitizeInput($_POST['action_text'] ?? '');
            $action_url = sanitizeInput($_POST['action_url'] ?? '');
            
            // Get users based on filter
            $user_query = "SELECT id FROM users WHERE 1=1";
            $user_params = [];
            
            if ($user_filter === 'verified') {
                $user_query .= " AND email_verified = 1";
            } elseif ($user_filter === 'unverified') {
                $user_query .= " AND email_verified = 0";
            } elseif ($user_filter === 'recent') {
                $user_query .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            }
            
            $user_stmt = $db->prepare($user_query);
            foreach ($user_params as $key => $value) {
                $user_stmt->bindValue($key, $value);
            }
            $user_stmt->execute();
            $users = $user_stmt->fetchAll();
            
            // Insert notifications for each user using batch insert for better performance
            if (!empty($users)) {
                $batch_size = 100; // Process in batches to avoid memory issues
                $total_users = count($users);
                $count = 0;

                for ($i = 0; $i < $total_users; $i += $batch_size) {
                    $batch_users = array_slice($users, $i, $batch_size);

                    // Build batch insert query
                    $values = [];
                    $params = [];

                    foreach ($batch_users as $index => $user) {
                        $values[] = "(:user_id_$index, :type_id_$index, :title_$index, :message_$index, :action_required_$index, :action_text_$index, :action_url_$index, NOW())";
                        $params[":user_id_$index"] = $user['id'];
                        $params[":type_id_$index"] = $notification_type_id;
                        $params[":title_$index"] = $title;
                        $params[":message_$index"] = $message_text;
                        $params[":action_required_$index"] = $action_required;
                        $params[":action_text_$index"] = $action_text;
                        $params[":action_url_$index"] = $action_url;
                    }

                    $insert_query = "INSERT INTO notifications (user_id, notification_type_id, title, message, action_required, action_text, action_url, created_at)
                                    VALUES " . implode(', ', $values);

                    $insert_stmt = $db->prepare($insert_query);

                    // Bind all parameters
                    foreach ($params as $key => $value) {
                        $insert_stmt->bindValue($key, $value);
                    }

                    $insert_stmt->execute();
                    $count += count($batch_users);
                }
            }
            
            logAdminActivity('send_bulk_notification', "Sent notification to $count users");
            $message = "Notification sent to $count users successfully.";
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error sending notifications: ' . $e->getMessage();
        }
    }
    
    if ($action === 'mark_read' && $notification_id) {
        try {
            $query = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            $stmt->execute();
            
            $message = 'Notification marked as read.';
        } catch(PDOException $e) {
            $error = 'Error updating notification: ' . $e->getMessage();
        }
    }
    
    if ($action === 'delete' && $notification_id) {
        try {
            $query = "DELETE FROM notifications WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            $stmt->execute();
            
            logAdminActivity('delete_notification', "Deleted notification ID: $notification_id");
            $message = 'Notification deleted successfully.';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error deleting notification: ' . $e->getMessage();
        }
    }
}

// Get notifications data
$search = $_GET['search'] ?? '';
$type_filter = $_GET['type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(n.title LIKE :search OR n.message LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($type_filter) {
    $where_conditions[] = "n.notification_type_id = :type";
    $params[':type'] = $type_filter;
}

if ($status_filter) {
    if ($status_filter === 'read') {
        $where_conditions[] = "n.is_read = 1";
    } elseif ($status_filter === 'unread') {
        $where_conditions[] = "n.is_read = 0";
    } elseif ($status_filter === 'action_required') {
        $where_conditions[] = "n.action_required = 1";
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count with connection recovery
try {
    $count_query = "SELECT COUNT(*) as total
                    FROM notifications n
                    JOIN users u ON n.user_id = u.id
                    $where_clause";
    $count_stmt = $db->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_notifications = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_notifications / $per_page);
} catch(PDOException $e) {
    // Handle connection timeout by reconnecting
    if ($e->getCode() == 2006 || strpos($e->getMessage(), 'server has gone away') !== false) {
        // Reconnect to database
        require_once '../config/database.php';

        // Retry the query
        try {
            $count_stmt = $db->prepare($count_query);
            foreach ($params as $key => $value) {
                $count_stmt->bindValue($key, $value);
            }
            $count_stmt->execute();
            $total_notifications = $count_stmt->fetch()['total'];
            $total_pages = ceil($total_notifications / $per_page);
        } catch(PDOException $retry_e) {
            error_log("Database connection failed after retry: " . $retry_e->getMessage());
            $total_notifications = 0;
            $total_pages = 0;
            $error = 'Database connection error. Please try again.';
        }
    } else {
        error_log("Database query error: " . $e->getMessage());
        $total_notifications = 0;
        $total_pages = 0;
        $error = 'Error loading notifications. Please try again.';
    }
}

// Get notifications with error handling
try {
    $query = "SELECT n.*,
                     u.first_name, u.last_name, u.email,
                     ntt.category as type_name
              FROM notifications n
              JOIN users u ON n.user_id = u.id
              JOIN notification_types nt ON n.notification_type_id = nt.id
              JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id
              $where_clause AND ntt.language_code = 'en'
              ORDER BY n.created_at DESC
              LIMIT :limit OFFSET :offset";

    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll();
} catch(PDOException $e) {
    // Handle connection timeout by reconnecting
    if ($e->getCode() == 2006 || strpos($e->getMessage(), 'server has gone away') !== false) {
        // Reconnect to database
        require_once '../config/database.php';

        // Retry the query with corrected column name
        try {
            $retry_query = "SELECT n.*,
                                   u.first_name, u.last_name, u.email,
                                   ntt.category as type_name
                            FROM notifications n
                            JOIN users u ON n.user_id = u.id
                            JOIN notification_types nt ON n.notification_type_id = nt.id
                            JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id
                            $where_clause AND ntt.language_code = 'en'
                            ORDER BY n.created_at DESC
                            LIMIT :limit OFFSET :offset";

            $stmt = $db->prepare($retry_query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $notifications = $stmt->fetchAll();
        } catch(PDOException $retry_e) {
            error_log("Database connection failed after retry: " . $retry_e->getMessage());
            $notifications = [];
            $error = 'Database connection error. Please try again.';
        }
    } else {
        error_log("Database query error: " . $e->getMessage());
        $notifications = [];
        $error = 'Error loading notifications. Please try again.';
    }
}

// Get notification types for filters and forms
$type_query = "SELECT nt.id, ntt.category as name
               FROM notification_types nt
               JOIN notification_type_translations ntt ON nt.id = ntt.notification_type_id
               WHERE ntt.language_code = 'en' AND nt.is_active = 1
               ORDER BY ntt.category";
$type_stmt = $db->prepare($type_query);
$type_stmt->execute();
$notification_types = $type_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Notifications Management</h1>
        <p class="text-muted">Manage system notifications and user communications</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkNotificationModal">
                <i class="fas fa-bullhorn me-2"></i>Send Bulk Notification
            </button>
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
                <label for="search" class="form-label">Search Notifications</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by title, message, or user" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type">
                    <option value="">All Types</option>
                    <?php foreach ($notification_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>" 
                                <?php echo $type_filter == $type['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($type['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Notifications</option>
                    <option value="unread" <?php echo $status_filter === 'unread' ? 'selected' : ''; ?>>Unread</option>
                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="action_required" <?php echo $status_filter === 'action_required' ? 'selected' : ''; ?>>Action Required</option>
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

<!-- Notification Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-bell text-primary mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count($notifications); ?></h4>
                <p class="text-muted mb-0">Total Notifications</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-envelope text-warning mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($notifications, fn($n) => !$n['is_read'])); ?></h4>
                <p class="text-muted mb-0">Unread</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-exclamation-circle text-danger mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($notifications, fn($n) => $n['action_required'])); ?></h4>
                <p class="text-muted mb-0">Action Required</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo count(array_filter($notifications, fn($n) => $n['is_read'])); ?></h4>
                <p class="text-muted mb-0">Read</p>
            </div>
        </div>
    </div>
</div>

<!-- Notifications Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Notifications List 
            <span class="badge bg-primary"><?php echo number_format($total_notifications); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('notificationsTable', 'notifications')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($notifications)): ?>
            <div class="text-center py-5">
                <i class="fas fa-bell text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Notifications Found</h4>
                <p class="text-muted">No notifications match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="notificationsTable">
                    <thead>
                        <tr>
                            <th>Notification</th>
                            <th>Recipient</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notifications as $notification): ?>
                            <tr class="<?php echo !$notification['is_read'] ? 'table-light' : ''; ?>">
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($notification['title']); ?></strong>
                                        <?php if ($notification['action_required']): ?>
                                            <span class="badge bg-danger ms-2">Action Required</span>
                                        <?php endif; ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($notification['message'], 0, 100)) . '...'; ?></small>
                                        <?php if ($notification['action_text'] && $notification['action_url']): ?>
                                            <br><small class="text-primary">
                                                <i class="fas fa-link me-1"></i><?php echo htmlspecialchars($notification['action_text']); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($notification['first_name'] . ' ' . $notification['last_name']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($notification['email']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($notification['type_name']); ?></span>
                                </td>
                                <td>
                                    <?php if ($notification['is_read']): ?>
                                        <span class="badge bg-success">Read</span>
                                        <?php if ($notification['read_at']): ?>
                                            <br><small class="text-muted"><?php echo formatDisplayDateTime($notification['read_at']); ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Unread</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?php echo formatDisplayDateTime($notification['created_at']); ?></small>
                                    <br><small class="text-muted"><?php echo timeAgo($notification['created_at']); ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                                onclick="viewNotification(<?php echo $notification['id']; ?>)" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (!$notification['is_read']): ?>
                                            <button type="button" class="btn btn-outline-success" 
                                                    onclick="markAsRead(<?php echo $notification['id']; ?>)" title="Mark as Read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-outline-danger" 
                                                onclick="deleteNotification(<?php echo $notification['id']; ?>)" title="Delete">
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
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_notifications); ?> 
                        of <?php echo number_format($total_notifications); ?> notifications
                    </div>
                    <?php 
                    $base_url = 'notifications.php?search=' . urlencode($search) . '&type=' . urlencode($type_filter) . '&status=' . urlencode($status_filter);
                    echo generatePagination($page, $total_pages, $base_url); 
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Notification Modal -->
<div class="modal fade" id="bulkNotificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="notifications.php?action=send_bulk">
                <div class="modal-header">
                    <h5 class="modal-title">Send Bulk Notification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="notification_type_id" class="form-label">Notification Type</label>
                            <select class="form-select" id="notification_type_id" name="notification_type_id" required>
                                <?php foreach ($notification_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="user_filter" class="form-label">Send To</label>
                            <select class="form-select" id="user_filter" name="user_filter">
                                <option value="all">All Users</option>
                                <option value="verified">Verified Users Only</option>
                                <option value="unverified">Unverified Users Only</option>
                                <option value="recent">Recent Users (Last 30 days)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="action_required" name="action_required">
                            <label class="form-check-label" for="action_required">
                                Requires Action
                            </label>
                        </div>
                    </div>
                    
                    <div class="row" id="actionFields" style="display: none;">
                        <div class="col-md-6 mb-3">
                            <label for="action_text" class="form-label">Action Text</label>
                            <input type="text" class="form-control" id="action_text" name="action_text" placeholder="e.g., View Details">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="action_url" class="form-label">Action URL</label>
                            <input type="url" class="form-control" id="action_url" name="action_url" placeholder="e.g., /my-applications.html">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Notification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Action Forms -->
<form id="markReadForm" method="POST" style="display: none;"></form>
<form id="deleteForm" method="POST" style="display: none;"></form>

<?php
$additional_js = '
<script>
// Toggle action fields
document.getElementById("action_required").addEventListener("change", function() {
    const actionFields = document.getElementById("actionFields");
    if (this.checked) {
        actionFields.style.display = "block";
    } else {
        actionFields.style.display = "none";
    }
});

function viewNotification(notificationId) {
    // You can implement a modal or redirect to view details
    alert("View notification details for ID: " + notificationId);
}

function markAsRead(notificationId) {
    if (confirm("Mark this notification as read?")) {
        document.getElementById("markReadForm").action = "notifications.php?action=mark_read&id=" + notificationId;
        document.getElementById("markReadForm").submit();
    }
}

function deleteNotification(notificationId) {
    if (confirm("Are you sure you want to delete this notification? This action cannot be undone.")) {
        document.getElementById("deleteForm").action = "notifications.php?action=delete&id=" + notificationId;
        document.getElementById("deleteForm").submit();
    }
}
</script>
';

include 'includes/footer.php';
?>
