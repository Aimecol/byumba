<?php
/**
 * User View Page
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('view_users');

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    header('Location: users.php');
    exit;
}

// Page configuration
$page_title = 'User Details';
$breadcrumbs = [
    ['title' => 'Users', 'url' => 'users.php'],
    ['title' => 'User Details']
];

$message = '';
$error = '';

// Get user details
try {
    $user_query = "SELECT u.*, 
                          COUNT(DISTINCT a.id) as application_count,
                          COUNT(DISTINCT m.id) as meeting_count,
                          p.name as parish_name
                   FROM users u
                   LEFT JOIN applications a ON u.id = a.user_id
                   LEFT JOIN meetings m ON u.id = m.user_id
                   LEFT JOIN user_parish_membership upm ON u.id = upm.user_id AND upm.is_active = 1
                   LEFT JOIN parishes p ON upm.parish_id = p.id
                   WHERE u.id = :id
                   GROUP BY u.id";
    
    $user_stmt = $db->prepare($user_query);
    $user_stmt->bindParam(':id', $user_id);
    $user_stmt->execute();
    $user = $user_stmt->fetch();
    
    if (!$user) {
        $error = 'User not found.';
    }
} catch(PDOException $e) {
    $error = 'Error loading user: ' . $e->getMessage();
}

// Get user's applications
$applications = [];
if ($user) {
    try {
        $app_query = "SELECT a.*, ctt.name as certificate_type_name
                      FROM applications a
                      JOIN certificate_types ct ON a.certificate_type_id = ct.id
                      JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id
                      WHERE a.user_id = :user_id AND ctt.language_code = 'en'
                      ORDER BY a.created_at DESC
                      LIMIT 10";
        
        $app_stmt = $db->prepare($app_query);
        $app_stmt->bindParam(':user_id', $user_id);
        $app_stmt->execute();
        $applications = $app_stmt->fetchAll();
    } catch(PDOException $e) {
        // Handle error silently
    }
}

// Get user's meetings
$meetings = [];
if ($user) {
    try {
        $meeting_query = "SELECT m.*, mtt.name as meeting_type_name
                         FROM meetings m
                         JOIN meeting_types mt ON m.meeting_type_id = mt.id
                         JOIN meeting_type_translations mtt ON mt.id = mtt.meeting_type_id
                         WHERE m.user_id = :user_id AND mtt.language_code = 'en'
                         ORDER BY m.meeting_date DESC, m.meeting_time DESC
                         LIMIT 10";
        
        $meeting_stmt = $db->prepare($meeting_query);
        $meeting_stmt->bindParam(':user_id', $user_id);
        $meeting_stmt->execute();
        $meetings = $meeting_stmt->fetchAll();
    } catch(PDOException $e) {
        // Handle error silently
    }
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'verify_email' && hasPermission('manage_users')) {
        try {
            $query = "UPDATE users SET email_verified = 1, email_verified_at = NOW() WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            $user['email_verified'] = 1;
            logAdminActivity('verify_user_email', "Verified email for user ID: $user_id");
            $message = 'User email verified successfully.';
        } catch(PDOException $e) {
            $error = 'Error verifying email: ' . $e->getMessage();
        }
    }
    
    if ($action === 'verify_phone' && hasPermission('manage_users')) {
        try {
            $query = "UPDATE users SET phone_verified = 1, phone_verified_at = NOW() WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            
            $user['phone_verified'] = 1;
            logAdminActivity('verify_user_phone', "Verified phone for user ID: $user_id");
            $message = 'User phone verified successfully.';
        } catch(PDOException $e) {
            $error = 'Error verifying phone: ' . $e->getMessage();
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
        <a href="users.php" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>
<?php else: ?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">User Details</h1>
        <p class="text-muted">Detailed information about the user</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <a href="user_form.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit User
            </a>
            <a href="users.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Users
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
    <!-- User Information -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body text-center">
                <!-- User Avatar -->
                <div class="user-avatar mb-3">
                    <div class="avatar-circle mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 600;">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                </div>
                
                <h4 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($user['email']); ?></p>
                
                <!-- Verification Status -->
                <div class="mb-3">
                    <?php if ($user['email_verified'] && $user['phone_verified']): ?>
                        <span class="badge bg-success">Fully Verified</span>
                    <?php elseif ($user['email_verified']): ?>
                        <span class="badge bg-warning">Email Verified</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Unverified</span>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Stats -->
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="mb-0 text-primary"><?php echo $user['application_count']; ?></h5>
                        <small class="text-muted">Applications</small>
                    </div>
                    <div class="col-6">
                        <h5 class="mb-0 text-success"><?php echo $user['meeting_count']; ?></h5>
                        <small class="text-muted">Meetings</small>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <?php if (hasPermission('manage_users')): ?>
                    <hr>
                    <div class="d-grid gap-2">
                        <?php if (!$user['email_verified']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="verify_email">
                                <button type="submit" class="btn btn-sm btn-success w-100">
                                    <i class="fas fa-check me-2"></i>Verify Email
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <?php if (!$user['phone_verified'] && $user['phone']): ?>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="verify_phone">
                                <button type="submit" class="btn btn-sm btn-success w-100">
                                    <i class="fas fa-check me-2"></i>Verify Phone
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Email</label>
                    <div class="d-flex align-items-center">
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                        <?php if ($user['email_verified']): ?>
                            <i class="fas fa-check-circle text-success ms-2" title="Verified"></i>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($user['phone']): ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Phone</label>
                        <div class="d-flex align-items-center">
                            <span><?php echo htmlspecialchars($user['phone']); ?></span>
                            <?php if ($user['phone_verified']): ?>
                                <i class="fas fa-check-circle text-success ms-2" title="Verified"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($user['address']): ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Address</label>
                        <p class="mb-0"><?php echo htmlspecialchars($user['address']); ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if ($user['parish_name']): ?>
                    <div class="mb-3">
                        <label class="form-label text-muted">Parish</label>
                        <p class="mb-0"><?php echo htmlspecialchars($user['parish_name']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- User Details & Activity -->
    <div class="col-lg-8">
        <!-- Personal Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">First Name</label>
                        <p class="mb-0"><?php echo htmlspecialchars($user['first_name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Last Name</label>
                        <p class="mb-0"><?php echo htmlspecialchars($user['last_name']); ?></p>
                    </div>
                    
                    <?php if ($user['national_id']): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">National ID</label>
                            <p class="mb-0"><?php echo htmlspecialchars($user['national_id']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($user['date_of_birth']): ?>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Date of Birth</label>
                            <p class="mb-0"><?php echo formatDisplayDate($user['date_of_birth']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Preferred Language</label>
                        <p class="mb-0"><?php echo ucfirst($user['preferred_language'] ?? 'English'); ?></p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Member Since</label>
                        <p class="mb-0"><?php echo formatDisplayDate($user['created_at']); ?></p>
                    </div>
                </div>
                
                <?php if ($user['bio']): ?>
                    <div class="mt-3">
                        <label class="form-label text-muted">Bio</label>
                        <p class="mb-0"><?php echo htmlspecialchars($user['bio']); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Applications -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Applications</h5>
                <a href="applications.php?search=<?php echo urlencode($user['email']); ?>" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($applications)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Application #</th>
                                    <th>Certificate Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td>
                                            <a href="application_view.php?id=<?php echo $app['id']; ?>">
                                                <?php echo htmlspecialchars($app['application_number']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['certificate_type_name']); ?></td>
                                        <td><?php echo getStatusBadge($app['status']); ?></td>
                                        <td><?php echo formatDisplayDate($app['submitted_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No applications found</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Meetings -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Meetings</h5>
                <a href="meetings.php?search=<?php echo urlencode($user['email']); ?>" class="btn btn-sm btn-outline-primary">
                    View All
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($meetings)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Meeting #</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($meetings as $meeting): ?>
                                    <tr>
                                        <td>
                                            <a href="meeting_view.php?id=<?php echo $meeting['id']; ?>">
                                                <?php echo htmlspecialchars($meeting['meeting_number']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($meeting['title']); ?></td>
                                        <td><?php echo htmlspecialchars($meeting['meeting_type_name']); ?></td>
                                        <td><?php echo formatDisplayDate($meeting['meeting_date']); ?></td>
                                        <td><?php echo getStatusBadge($meeting['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No meetings found</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
