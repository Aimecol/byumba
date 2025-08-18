<?php
/**
 * Meetings Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_meetings');

// Page configuration
$page_title = 'Meetings Management';
$breadcrumbs = [
    ['title' => 'Meetings']
];

// Handle actions
$action = $_GET['action'] ?? 'list';
$meeting_id = $_GET['id'] ?? null;
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'update_status' && $meeting_id) {
        try {
            $new_status = $_POST['status'];
            $notes = $_POST['notes'] ?? '';
            
            $query = "UPDATE meetings SET status = :status, notes = :notes WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $new_status);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':id', $meeting_id);
            $stmt->execute();
            
            logAdminActivity('update_meeting_status', "Updated meeting $meeting_id status to $new_status");
            $message = 'Meeting status updated successfully.';
            $action = 'list';
        } catch(PDOException $e) {
            $error = 'Error updating meeting: ' . $e->getMessage();
        }
    }
}

// Get meetings data
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$page = max(1, $_GET['page'] ?? 1);
$per_page = 25;
$offset = ($page - 1) * $per_page;

$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(m.meeting_number LIKE :search OR m.title LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($status_filter) {
    $where_conditions[] = "m.status = :status";
    $params[':status'] = $status_filter;
}

if ($date_filter) {
    if ($date_filter === 'today') {
        $where_conditions[] = "m.meeting_date = CURDATE()";
    } elseif ($date_filter === 'tomorrow') {
        $where_conditions[] = "m.meeting_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($date_filter === 'this_week') {
        $where_conditions[] = "m.meeting_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
    }
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM meetings m
                JOIN users u ON m.user_id = u.id
                $where_clause";
$count_stmt = $db->prepare($count_query);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_meetings = $count_stmt->fetch()['total'];
$total_pages = ceil($total_meetings / $per_page);

// Get meetings
$query = "SELECT m.*, 
                 u.first_name, u.last_name, u.email, u.phone,
                 mtt.name as meeting_type_name
          FROM meetings m
          JOIN users u ON m.user_id = u.id
          JOIN meeting_types mt ON m.meeting_type_id = mt.id
          JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id
          $where_clause AND mtt.language_code = 'en'
          ORDER BY m.meeting_date DESC, m.meeting_time DESC
          LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$meetings = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Meetings Management</h1>
        <p class="text-muted">Manage bishop and priest meetings</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <a href="meeting_form.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Schedule Meeting
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
                <label for="search" class="form-label">Search Meetings</label>
                <input type="text" class="form-control" id="search" name="search" 
                       placeholder="Search by number, title, or name" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">Date Filter</label>
                <select class="form-select" id="date" name="date">
                    <option value="">All Dates</option>
                    <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                    <option value="tomorrow" <?php echo $date_filter === 'tomorrow' ? 'selected' : ''; ?>>Tomorrow</option>
                    <option value="this_week" <?php echo $date_filter === 'this_week' ? 'selected' : ''; ?>>This Week</option>
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

<!-- Meetings Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            Meetings List 
            <span class="badge bg-primary"><?php echo number_format($total_meetings); ?></span>
        </h5>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="exportTable('meetingsTable', 'meetings')">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printPage()">
                <i class="fas fa-print me-1"></i>Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($meetings)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">No Meetings Found</h4>
                <p class="text-muted">No meetings match your search criteria.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover" id="meetingsTable">
                    <thead>
                        <tr>
                            <th>Meeting #</th>
                            <th>Title</th>
                            <th>Participant</th>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($meeting['meeting_number']); ?></strong>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($meeting['title']); ?></strong>
                                    <?php if ($meeting['description']): ?>
                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($meeting['description'], 0, 50)) . '...'; ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($meeting['first_name'] . ' ' . $meeting['last_name']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($meeting['email']); ?></small>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($meeting['meeting_type_name']); ?></td>
                                <td>
                                    <strong><?php echo formatDisplayDate($meeting['meeting_date']); ?></strong>
                                    <br><small class="text-muted"><?php echo date('g:i A', strtotime($meeting['meeting_time'])); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo $meeting['duration_minutes']; ?> min</span>
                                </td>
                                <td><?php echo getStatusBadge($meeting['status']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="meeting_view.php?id=<?php echo $meeting['id']; ?>" 
                                           class="btn btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary" 
                                                onclick="updateStatus(<?php echo $meeting['id']; ?>)" title="Update Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="meeting_form.php?id=<?php echo $meeting['id']; ?>" 
                                           class="btn btn-outline-info" title="Edit">
                                            <i class="fas fa-calendar-edit"></i>
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
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $per_page, $total_meetings); ?> 
                        of <?php echo number_format($total_meetings); ?> meetings
                    </div>
                    <?php 
                    $base_url = 'meetings.php?search=' . urlencode($search) . '&status=' . urlencode($status_filter) . '&date=' . urlencode($date_filter);
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
                    <h5 class="modal-title">Update Meeting Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="meeting_id" id="statusMeetingId">
                    
                    <div class="mb-3">
                        <label for="statusSelect" class="form-label">Status</label>
                        <select class="form-select" id="statusSelect" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
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
function updateStatus(meetingId) {
    document.getElementById("statusMeetingId").value = meetingId;
    document.getElementById("statusForm").action = "meetings.php?action=update_status&id=" + meetingId;
    var statusModal = new bootstrap.Modal(document.getElementById("statusModal"));
    statusModal.show();
}
</script>
';

include 'includes/footer.php';
?>
