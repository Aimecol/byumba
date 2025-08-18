<?php
/**
 * Settings Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('manage_settings');

// Page configuration
$page_title = 'Settings';
$breadcrumbs = [
    ['title' => 'Settings']
];

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_general') {
        try {
            // Update general settings
            $settings = [
                'site_name' => $_POST['site_name'] ?? '',
                'site_description' => $_POST['site_description'] ?? '',
                'contact_email' => $_POST['contact_email'] ?? '',
                'contact_phone' => $_POST['contact_phone'] ?? '',
                'address' => $_POST['address'] ?? '',
                'office_hours' => $_POST['office_hours'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                $query = "INSERT INTO system_setting_translations (setting_key, language_code, setting_value) 
                         VALUES (:key, 'en', :value) 
                         ON DUPLICATE KEY UPDATE setting_value = :value";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':key', $key);
                $stmt->bindParam(':value', $value);
                $stmt->execute();
            }
            
            logAdminActivity('update_settings', 'Updated general settings');
            $message = 'General settings updated successfully.';
        } catch(PDOException $e) {
            $error = 'Error updating settings: ' . $e->getMessage();
        }
    }
    
    if ($action === 'update_email') {
        try {
            // Update email settings
            $email_settings = [
                'smtp_host' => $_POST['smtp_host'] ?? '',
                'smtp_port' => $_POST['smtp_port'] ?? '',
                'smtp_username' => $_POST['smtp_username'] ?? '',
                'smtp_password' => $_POST['smtp_password'] ?? '',
                'smtp_encryption' => $_POST['smtp_encryption'] ?? '',
                'from_email' => $_POST['from_email'] ?? '',
                'from_name' => $_POST['from_name'] ?? ''
            ];
            
            foreach ($email_settings as $key => $value) {
                $query = "INSERT INTO system_setting_translations (setting_key, language_code, setting_value) 
                         VALUES (:key, 'en', :value) 
                         ON DUPLICATE KEY UPDATE setting_value = :value";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':key', $key);
                $stmt->bindParam(':value', $value);
                $stmt->execute();
            }
            
            logAdminActivity('update_settings', 'Updated email settings');
            $message = 'Email settings updated successfully.';
        } catch(PDOException $e) {
            $error = 'Error updating email settings: ' . $e->getMessage();
        }
    }
}

// Get current settings
$current_settings = [];
try {
    $query = "SELECT setting_key, setting_value FROM system_setting_translations WHERE language_code = 'en'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    while ($row = $stmt->fetch()) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
} catch(PDOException $e) {
    // Handle error silently
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">System Settings</h1>
        <p class="text-muted">Configure system-wide settings and preferences</p>
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
    <!-- General Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-cog me-2"></i>General Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_general">
                    
                    <div class="mb-3">
                        <label for="site_name" class="form-label">Site Name</label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?php echo htmlspecialchars($current_settings['site_name'] ?? 'Diocese of Byumba'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="site_description" class="form-label">Site Description</label>
                        <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($current_settings['site_description'] ?? 'Official website of the Diocese of Byumba'); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Contact Email</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? 'info@diocesebyumba.rw'); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_phone" class="form-label">Contact Phone</label>
                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" 
                               value="<?php echo htmlspecialchars($current_settings['contact_phone'] ?? '+250 788 123 456'); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($current_settings['address'] ?? 'Byumba, Northern Province, Rwanda'); ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="office_hours" class="form-label">Office Hours</label>
                        <input type="text" class="form-control" id="office_hours" name="office_hours" 
                               value="<?php echo htmlspecialchars($current_settings['office_hours'] ?? 'Monday - Friday: 8:00 AM - 5:00 PM'); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save General Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Email Settings -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope me-2"></i>Email Settings
                </h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_email">
                    
                    <div class="mb-3">
                        <label for="smtp_host" class="form-label">SMTP Host</label>
                        <input type="text" class="form-control" id="smtp_host" name="smtp_host" 
                               value="<?php echo htmlspecialchars($current_settings['smtp_host'] ?? ''); ?>" 
                               placeholder="smtp.gmail.com">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="smtp_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port" 
                                   value="<?php echo htmlspecialchars($current_settings['smtp_port'] ?? '587'); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="smtp_encryption" class="form-label">Encryption</label>
                            <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                <option value="tls" <?php echo ($current_settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($current_settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="" <?php echo ($current_settings['smtp_encryption'] ?? '') === '' ? 'selected' : ''; ?>>None</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="smtp_username" class="form-label">SMTP Username</label>
                        <input type="text" class="form-control" id="smtp_username" name="smtp_username" 
                               value="<?php echo htmlspecialchars($current_settings['smtp_username'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="smtp_password" class="form-label">SMTP Password</label>
                        <input type="password" class="form-control" id="smtp_password" name="smtp_password" 
                               value="<?php echo htmlspecialchars($current_settings['smtp_password'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="from_email" class="form-label">From Email</label>
                        <input type="email" class="form-control" id="from_email" name="from_email" 
                               value="<?php echo htmlspecialchars($current_settings['from_email'] ?? 'noreply@diocesebyumba.rw'); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="from_name" class="form-label">From Name</label>
                        <input type="text" class="form-control" id="from_name" name="from_name" 
                               value="<?php echo htmlspecialchars($current_settings['from_name'] ?? 'Diocese of Byumba'); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Email Settings
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>System Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>PHP Version:</strong></td>
                                <td><?php echo PHP_VERSION; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Server Software:</strong></td>
                                <td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Database:</strong></td>
                                <td>MySQL <?php echo $db->getAttribute(PDO::ATTR_SERVER_VERSION); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Timezone:</strong></td>
                                <td><?php echo date_default_timezone_get(); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Memory Limit:</strong></td>
                                <td><?php echo ini_get('memory_limit'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Upload Max Size:</strong></td>
                                <td><?php echo ini_get('upload_max_filesize'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Post Max Size:</strong></td>
                                <td><?php echo ini_get('post_max_size'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Max Execution Time:</strong></td>
                                <td><?php echo ini_get('max_execution_time'); ?>s</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
