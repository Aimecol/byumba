<?php
/**
 * Reports Management
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login and permission
requirePermission('view_reports');

// Page configuration
$page_title = 'Reports & Analytics';
$breadcrumbs = [
    ['title' => 'Reports']
];

// Get date range from request
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Today
$report_type = $_GET['report_type'] ?? 'overview';

// Validate dates
if (!strtotime($start_date) || !strtotime($end_date)) {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-d');
}

// Generate reports based on type
$report_data = [];

// Prepare date variables for binding
$end_date_with_time = $end_date . ' 23:59:59';

try {
    // Overview Statistics
    if ($report_type === 'overview' || $report_type === 'all') {
        // User statistics
        $user_stats_query = "SELECT
            COUNT(*) as total_users,
            COUNT(CASE WHEN email_verified = 1 THEN 1 END) as verified_users,
            COUNT(CASE WHEN created_at >= :start_date AND created_at <= :end_date THEN 1 END) as new_users_period
            FROM users";
        $stmt = $db->prepare($user_stats_query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date_with_time);
        $stmt->execute();
        $report_data['user_stats'] = $stmt->fetch();
        
        // Application statistics
        $app_stats_query = "SELECT
            COUNT(*) as total_applications,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_applications,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_applications,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_applications,
            COUNT(CASE WHEN submitted_date >= :start_date AND submitted_date <= :end_date THEN 1 END) as applications_period
            FROM applications";
        $stmt = $db->prepare($app_stats_query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date_with_time);
        $stmt->execute();
        $report_data['application_stats'] = $stmt->fetch();
        
        // Meeting statistics
        $meeting_stats_query = "SELECT 
            COUNT(*) as total_meetings,
            COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_meetings,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_meetings,
            COUNT(CASE WHEN meeting_date >= :start_date AND meeting_date <= :end_date THEN 1 END) as meetings_period
            FROM meetings";
        $stmt = $db->prepare($meeting_stats_query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        $report_data['meeting_stats'] = $stmt->fetch();
    }
    
    // Application Reports
    if ($report_type === 'applications' || $report_type === 'all') {
        // Applications by certificate type
        $cert_type_query = "SELECT 
            ctt.name as certificate_type,
            COUNT(a.id) as application_count,
            COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed_count
            FROM applications a
            JOIN certificate_types ct ON a.certificate_type_id = ct.id
            JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id
            WHERE ctt.language_code = 'en' 
            AND a.submitted_date >= :start_date AND a.submitted_date <= :end_date
            GROUP BY ct.id, ctt.name
            ORDER BY application_count DESC";
        $stmt = $db->prepare($cert_type_query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date_with_time);
        $stmt->execute();
        $report_data['applications_by_type'] = $stmt->fetchAll();
        
        // Monthly application trends
        $monthly_apps_query = "SELECT 
            DATE_FORMAT(submitted_date, '%Y-%m') as month,
            COUNT(*) as count,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed
            FROM applications 
            WHERE submitted_date >= DATE_SUB(:end_date, INTERVAL 12 MONTH)
            AND submitted_date <= :end_date
            GROUP BY DATE_FORMAT(submitted_date, '%Y-%m')
            ORDER BY month";
        $stmt = $db->prepare($monthly_apps_query);
        $stmt->bindParam(':end_date', $end_date_with_time);
        $stmt->execute();
        $report_data['monthly_applications'] = $stmt->fetchAll();
    }
    
    // User Reports
    if ($report_type === 'users' || $report_type === 'all') {
        // User registration trends
        $user_trends_query = "SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as registrations,
            COUNT(CASE WHEN email_verified = 1 THEN 1 END) as verified
            FROM users 
            WHERE created_at >= DATE_SUB(:end_date, INTERVAL 12 MONTH)
            AND created_at <= :end_date
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month";
        $stmt = $db->prepare($user_trends_query);
        $stmt->bindParam(':end_date', $end_date_with_time);
        $stmt->execute();
        $report_data['user_trends'] = $stmt->fetchAll();
        
        // Users by parish
        $users_by_parish_query = "SELECT
            COALESCE(p.name_en, p.name) as parish_name,
            COUNT(upm.user_id) as member_count
            FROM parishes p
            LEFT JOIN user_parish_membership upm ON p.id = upm.parish_id AND upm.is_active = 1
            WHERE p.is_active = 1
            GROUP BY p.id, COALESCE(p.name_en, p.name)
            ORDER BY member_count DESC";
        $stmt = $db->prepare($users_by_parish_query);
        $stmt->execute();
        $report_data['users_by_parish'] = $stmt->fetchAll();
    }
    
    // Financial Reports (if payment data exists)
    if ($report_type === 'financial' || $report_type === 'all') {
        $payment_stats_query = "SELECT 
            COUNT(*) as total_payments,
            COUNT(CASE WHEN payment_status = 'paid' THEN 1 END) as completed_payments,
            COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as pending_payments
            FROM applications 
            WHERE submitted_date >= :start_date AND submitted_date <= :end_date";
        $stmt = $db->prepare($payment_stats_query);
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date_with_time);
        $stmt->execute();
        $report_data['payment_stats'] = $stmt->fetch();
    }
    
} catch(PDOException $e) {
    $error = 'Error generating reports: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="h3 mb-0">Reports & Analytics</h1>
        <p class="text-muted">Comprehensive reports and data analysis</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Print Report
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>Export
            </button>
        </div>
    </div>
</div>

<!-- Report Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="report_type" class="form-label">Report Type</label>
                <select class="form-select" id="report_type" name="report_type">
                    <option value="overview" <?php echo $report_type === 'overview' ? 'selected' : ''; ?>>Overview</option>
                    <option value="applications" <?php echo $report_type === 'applications' ? 'selected' : ''; ?>>Applications</option>
                    <option value="users" <?php echo $report_type === 'users' ? 'selected' : ''; ?>>Users</option>
                    <option value="financial" <?php echo $report_type === 'financial' ? 'selected' : ''; ?>>Financial</option>
                    <option value="all" <?php echo $report_type === 'all' ? 'selected' : ''; ?>>All Reports</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar me-2"></i>Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Report Period Info -->
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Report Period:</strong> <?php echo formatDisplayDate($start_date); ?> to <?php echo formatDisplayDate($end_date); ?>
    <span class="ms-3"><strong>Generated:</strong> <?php echo date('M j, Y g:i A'); ?></span>
</div>

<?php if ($report_type === 'overview' || $report_type === 'all'): ?>
<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <h4><i class="fas fa-chart-pie me-2"></i>Overview Statistics</h4>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-users text-primary mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1"><?php echo number_format($report_data['user_stats']['total_users'] ?? 0); ?></h3>
                <p class="text-muted mb-1">Total Users</p>
                <small class="text-success">+<?php echo number_format($report_data['user_stats']['new_users_period'] ?? 0); ?> this period</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-file-alt text-success mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1"><?php echo number_format($report_data['application_stats']['total_applications'] ?? 0); ?></h3>
                <p class="text-muted mb-1">Total Applications</p>
                <small class="text-success">+<?php echo number_format($report_data['application_stats']['applications_period'] ?? 0); ?> this period</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-calendar text-warning mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1"><?php echo number_format($report_data['meeting_stats']['total_meetings'] ?? 0); ?></h3>
                <p class="text-muted mb-1">Total Meetings</p>
                <small class="text-success">+<?php echo number_format($report_data['meeting_stats']['meetings_period'] ?? 0); ?> this period</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fas fa-check-circle text-info mb-2" style="font-size: 2rem;"></i>
                <h3 class="mb-1"><?php echo number_format($report_data['application_stats']['completed_applications'] ?? 0); ?></h3>
                <p class="text-muted mb-1">Completed Applications</p>
                <small class="text-muted">
                    <?php 
                    $total_apps = $report_data['application_stats']['total_applications'] ?? 0;
                    $completed_apps = $report_data['application_stats']['completed_applications'] ?? 0;
                    $completion_rate = $total_apps > 0 ? round(($completed_apps / $total_apps) * 100, 1) : 0;
                    echo $completion_rate . '% completion rate';
                    ?>
                </small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($report_type === 'applications' || $report_type === 'all'): ?>
<!-- Application Reports -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-certificate me-2"></i>Applications by Certificate Type</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($report_data['applications_by_type'])): ?>
                    <canvas id="certificateTypeChart" height="300"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Certificate Type</th>
                                    <th>Applications</th>
                                    <th>Completed</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data['applications_by_type'] as $type): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($type['certificate_type']); ?></td>
                                        <td><?php echo number_format($type['application_count']); ?></td>
                                        <td><?php echo number_format($type['completed_count']); ?></td>
                                        <td>
                                            <?php 
                                            $rate = $type['application_count'] > 0 ? round(($type['completed_count'] / $type['application_count']) * 100, 1) : 0;
                                            echo $rate . '%';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No application data for the selected period.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Application Trends</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($report_data['monthly_applications'])): ?>
                    <canvas id="monthlyApplicationsChart" height="300"></canvas>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No monthly trend data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($report_type === 'users' || $report_type === 'all'): ?>
<!-- User Reports -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-user-plus me-2"></i>User Registration Trends</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($report_data['user_trends'])): ?>
                    <canvas id="userTrendsChart" height="300"></canvas>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No user trend data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-church me-2"></i>Users by Parish</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($report_data['users_by_parish'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Parish</th>
                                    <th>Members</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_members = array_sum(array_column($report_data['users_by_parish'], 'member_count'));
                                foreach ($report_data['users_by_parish'] as $parish): 
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($parish['parish_name']); ?></td>
                                        <td><?php echo number_format($parish['member_count']); ?></td>
                                        <td>
                                            <?php 
                                            $percentage = $total_members > 0 ? round(($parish['member_count'] / $total_members) * 100, 1) : 0;
                                            echo $percentage . '%';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No parish membership data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($report_type === 'financial' || $report_type === 'all'): ?>
<!-- Financial Reports -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <h4 class="text-primary"><?php echo number_format($report_data['payment_stats']['total_payments'] ?? 0); ?></h4>
                        <p class="text-muted">Total Payment Records</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h4 class="text-success"><?php echo number_format($report_data['payment_stats']['completed_payments'] ?? 0); ?></h4>
                        <p class="text-muted">Completed Payments</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <h4 class="text-warning"><?php echo number_format($report_data['payment_stats']['pending_payments'] ?? 0); ?></h4>
                        <p class="text-muted">Pending Payments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$additional_js = '
<script>
// Certificate Type Chart
' . (isset($report_data['applications_by_type']) && !empty($report_data['applications_by_type']) ? '
const certTypeCtx = document.getElementById("certificateTypeChart").getContext("2d");
new Chart(certTypeCtx, {
    type: "doughnut",
    data: {
        labels: [' . implode(',', array_map(function($type) { return '"' . addslashes($type['certificate_type']) . '"'; }, $report_data['applications_by_type'])) . '],
        datasets: [{
            data: [' . implode(',', array_column($report_data['applications_by_type'], 'application_count')) . '],
            backgroundColor: ["#3498db", "#27ae60", "#f39c12", "#e74c3c", "#9b59b6", "#1abc9c", "#34495e"]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "bottom"
            }
        }
    }
});
' : '') . '

// Monthly Applications Chart
' . (isset($report_data['monthly_applications']) && !empty($report_data['monthly_applications']) ? '
const monthlyAppsCtx = document.getElementById("monthlyApplicationsChart").getContext("2d");
new Chart(monthlyAppsCtx, {
    type: "line",
    data: {
        labels: [' . implode(',', array_map(function($month) { return '"' . date('M Y', strtotime($month['month'] . '-01')) . '"'; }, $report_data['monthly_applications'])) . '],
        datasets: [{
            label: "Applications",
            data: [' . implode(',', array_column($report_data['monthly_applications'], 'count')) . '],
            borderColor: "#3498db",
            backgroundColor: "rgba(52, 152, 219, 0.1)",
            tension: 0.4,
            fill: true
        }, {
            label: "Completed",
            data: [' . implode(',', array_column($report_data['monthly_applications'], 'completed')) . '],
            borderColor: "#27ae60",
            backgroundColor: "rgba(39, 174, 96, 0.1)",
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
' : '') . '

// User Trends Chart
' . (isset($report_data['user_trends']) && !empty($report_data['user_trends']) ? '
const userTrendsCtx = document.getElementById("userTrendsChart").getContext("2d");
new Chart(userTrendsCtx, {
    type: "bar",
    data: {
        labels: [' . implode(',', array_map(function($trend) { return '"' . date('M Y', strtotime($trend['month'] . '-01')) . '"'; }, $report_data['user_trends'])) . '],
        datasets: [{
            label: "Registrations",
            data: [' . implode(',', array_column($report_data['user_trends'], 'registrations')) . '],
            backgroundColor: "#3498db"
        }, {
            label: "Verified",
            data: [' . implode(',', array_column($report_data['user_trends'], 'verified')) . '],
            backgroundColor: "#27ae60"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
' : '') . '

function exportReport() {
    // Simple CSV export functionality
    const reportData = {
        period: "' . $start_date . ' to ' . $end_date . '",
        type: "' . $report_type . '",
        generated: "' . date('Y-m-d H:i:s') . '"
    };
    
    alert("Export functionality would be implemented here. Report data prepared for: " + reportData.type);
}
</script>
';

include 'includes/footer.php';
?>
