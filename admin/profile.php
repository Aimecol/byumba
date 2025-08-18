<?php
/**
 * Admin Profile Page
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login
requireAdminLogin();

// Page configuration
$page_title = 'My Profile';
$breadcrumbs = [
    ['title' => 'Profile']
];

$current_admin = getCurrentAdmin();
$message = '';
$error = '';

// Get admin user details
try {
    $query = "SELECT * FROM users WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $current_admin['id']);
    $stmt->execute();
    $admin_user = $stmt->fetch();
    
    if (!$admin_user) {
        $error = 'Admin user not found.';
    }
} catch(PDOException $e) {
    $error = 'Error loading profile: ' . $e->getMessage();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        try {
            $first_name = sanitizeInput($_POST['first_name']);
            $last_name = sanitizeInput($_POST['last_name']);
            $email = sanitizeInput($_POST['email']);
            $phone = sanitizeInput($_POST['phone'] ?? '');
            $national_id = sanitizeInput($_POST['national_id'] ?? '');
            $date_of_birth = $_POST['date_of_birth'] ?? null;
            $address = sanitizeInput($_POST['address'] ?? '');
            
            // Validation
            if (empty($first_name) || empty($last_name) || empty($email)) {
                throw new Exception('First name, last name, and email are required.');
            }
            
            if (!validateEmail($email)) {
                throw new Exception('Please enter a valid email address.');
            }
            
            // Check if email is already taken by another user
            $email_check_query = "SELECT id FROM users WHERE email = :email AND id != :current_id";
            $email_check_stmt = $db->prepare($email_check_query);
            $email_check_stmt->bindParam(':email', $email);
            $email_check_stmt->bindParam(':current_id', $current_admin['id']);
            $email_check_stmt->execute();
            
            if ($email_check_stmt->fetch()) {
                throw new Exception('This email address is already in use by another user.');
            }
            
            // Update profile
            $update_query = "UPDATE users SET
                            first_name = :first_name,
                            last_name = :last_name,
                            email = :email,
                            phone = :phone,
                            national_id = :national_id,
                            date_of_birth = :date_of_birth,
                            address = :address,
                            updated_at = NOW()
                            WHERE id = :id";

            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':first_name', $first_name);
            $update_stmt->bindParam(':last_name', $last_name);
            $update_stmt->bindParam(':email', $email);
            $update_stmt->bindParam(':phone', $phone);
            $update_stmt->bindParam(':national_id', $national_id);
            $update_stmt->bindParam(':date_of_birth', $date_of_birth);
            $update_stmt->bindParam(':address', $address);
            $update_stmt->bindParam(':id', $current_admin['id']);
            $update_stmt->execute();
            
            // Update session data
            $_SESSION['admin_name'] = $first_name . ' ' . $last_name;
            $_SESSION['admin_email'] = $email;
            
            // Refresh admin user data
            $admin_user['first_name'] = $first_name;
            $admin_user['last_name'] = $last_name;
            $admin_user['email'] = $email;
            $admin_user['phone'] = $phone;
            $admin_user['national_id'] = $national_id;
            $admin_user['date_of_birth'] = $date_of_birth;
            $admin_user['address'] = $address;
            
            logAdminActivity('update_profile', 'Updated profile information');
            $message = 'Profile updated successfully.';
            
        } catch(Exception $e) {
            $error = $e->getMessage();
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
    
    if ($action === 'change_password') {
        try {
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Validation
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                throw new Exception('All password fields are required.');
            }
            
            if (!password_verify($current_password, $admin_user['password_hash'])) {
                throw new Exception('Current password is incorrect.');
            }
            
            if ($new_password !== $confirm_password) {
                throw new Exception('New passwords do not match.');
            }
            
            if (strlen($new_password) < 8) {
                throw new Exception('New password must be at least 8 characters long.');
            }
            
            // Update password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $password_query = "UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id";
            $password_stmt = $db->prepare($password_query);
            $password_stmt->bindParam(':password_hash', $new_password_hash);
            $password_stmt->bindParam(':id', $current_admin['id']);
            $password_stmt->execute();
            
            logAdminActivity('change_password', 'Changed account password');
            $message = 'Password changed successfully.';
            
        } catch(Exception $e) {
            $error = $e->getMessage();
        } catch(PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Get admin activity log
try {
    $activity_query = "SELECT action, details, ip_address, created_at 
                      FROM admin_activity_log 
                      WHERE admin_id = :admin_id 
                      ORDER BY created_at DESC 
                      LIMIT 10";
    $activity_stmt = $db->prepare($activity_query);
    $activity_stmt->bindParam(':admin_id', $current_admin['id']);
    $activity_stmt->execute();
    $recent_activities = $activity_stmt->fetchAll();
} catch(PDOException $e) {
    $recent_activities = [];
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">My Profile</h1>
        <p class="text-muted">Manage your account information and settings</p>
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

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Profile Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($admin_user['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($admin_user['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($admin_user['email'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($admin_user['phone'] ?? ''); ?>" 
                                   placeholder="+250 788 123 456">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="national_id" class="form-label">National ID</label>
                            <input type="text" class="form-control" id="national_id" name="national_id" 
                                   value="<?php echo htmlspecialchars($admin_user['national_id'] ?? ''); ?>" 
                                   placeholder="1234567890123456">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                   value="<?php echo $admin_user['date_of_birth'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"
                                  placeholder="Enter your address"><?php echo htmlspecialchars($admin_user['address'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Change Password -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock me-2"></i>Change Password
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   minlength="8" required>
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   minlength="8" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Profile Summary & Activity -->
    <div class="col-lg-4">
        <!-- Profile Summary -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="profile-avatar mb-3">
                    <div class="avatar-circle mx-auto" style="width: 100px; height: 100px; background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2.5rem; font-weight: 600;">
                        <?php echo strtoupper(substr($admin_user['first_name'] ?? 'A', 0, 1) . substr($admin_user['last_name'] ?? 'U', 0, 1)); ?>
                    </div>
                </div>
                <h4 class="mb-1"><?php echo htmlspecialchars(($admin_user['first_name'] ?? '') . ' ' . ($admin_user['last_name'] ?? '')); ?></h4>
                <p class="text-muted mb-2"><?php echo htmlspecialchars($admin_user['email'] ?? ''); ?></p>
                <span class="badge bg-primary"><?php echo ucfirst($current_admin['role']); ?></span>
                
                <hr class="my-3">
                
                <div class="row text-center">
                    <div class="col-6">
                        <h6 class="mb-0">Member Since</h6>
                        <small class="text-muted">
                            <?php echo $admin_user['created_at'] ? formatDisplayDate($admin_user['created_at']) : 'Unknown'; ?>
                        </small>
                    </div>
                    <div class="col-6">
                        <h6 class="mb-0">Last Updated</h6>
                        <small class="text-muted">
                            <?php echo $admin_user['updated_at'] ? formatDisplayDate($admin_user['updated_at']) : 'Never'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Account Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Account Status</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Email Verified</span>
                    <?php if ($admin_user['email_verified']): ?>
                        <span class="badge bg-success">Verified</span>
                    <?php else: ?>
                        <span class="badge bg-warning">Unverified</span>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Phone Verified</span>
                    <?php if ($admin_user['phone_verified']): ?>
                        <span class="badge bg-success">Verified</span>
                    <?php else: ?>
                        <span class="badge bg-warning">Unverified</span>
                    <?php endif; ?>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Account Status</span>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Recent Activity</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($recent_activities)): ?>
                    <div class="activity-list">
                        <?php foreach (array_slice($recent_activities, 0, 5) as $activity): ?>
                            <div class="activity-item d-flex align-items-start mb-3">
                                <div class="activity-icon me-2">
                                    <i class="fas fa-circle text-primary" style="font-size: 0.5rem;"></i>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <p class="mb-1 small">
                                        <strong><?php echo ucwords(str_replace('_', ' ', $activity['action'])); ?></strong>
                                        <?php if ($activity['details']): ?>
                                            <br><span class="text-muted"><?php echo htmlspecialchars($activity['details']); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <small class="text-muted">
                                        <?php echo timeAgo($activity['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (count($recent_activities) > 5): ?>
                        <div class="text-center">
                            <small class="text-muted">And <?php echo count($recent_activities) - 5; ?> more activities...</small>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No recent activity</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = '
<script>
// Password confirmation validation
document.getElementById("confirm_password").addEventListener("input", function() {
    const newPassword = document.getElementById("new_password").value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity("Passwords do not match");
    } else {
        this.setCustomValidity("");
    }
});

// Form validation feedback
document.querySelectorAll("form").forEach(function(form) {
    form.addEventListener("submit", function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add("was-validated");
    });
});
</script>
';

include 'includes/footer.php';
?>
