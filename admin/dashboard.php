<?php
/**
 * Admin Dashboard
 * Diocese of Byumba Admin Panel
 */

define('ADMIN_PAGE', true);
require_once 'functions.php';

// Require admin login
requireAdminLogin();

// Page configuration
$page_title = 'Dashboard';
$breadcrumbs = [
    ['title' => 'Dashboard']
];

// Get dashboard data
$stats = getDashboardStats();
$recent_activities = getRecentAdminActivities(8);
$application_status_counts = getApplicationStatusCounts();
$monthly_stats = getMonthlyApplicationStats(6);

include 'includes/header.php';
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-0">Dashboard Overview</h1>
        <p class="text-muted">Welcome back, <?php echo htmlspecialchars(getCurrentAdmin()['name']); ?>!</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #3498db, #5dade2);">
            <div class="stats-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['total_users'] ?? 0); ?></div>
            <div class="stats-label">Total Users</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #27ae60, #58d68d);">
            <div class="stats-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['total_applications'] ?? 0); ?></div>
            <div class="stats-label">Total Applications</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #f39c12, #f7dc6f);">
            <div class="stats-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['pending_applications'] ?? 0); ?></div>
            <div class="stats-label">Pending Applications</div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stats-card" style="background: linear-gradient(135deg, #e74c3c, #ec7063);">
            <div class="stats-icon">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stats-number"><?php echo number_format($stats['todays_meetings'] ?? 0); ?></div>
            <div class="stats-label">Today's Meetings</div>
        </div>
    </div>
</div>

<!-- Secondary Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-briefcase text-primary mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo number_format($stats['active_jobs'] ?? 0); ?></h4>
                <p class="text-muted mb-0">Active Jobs</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-blog text-success mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo number_format($stats['published_posts'] ?? 0); ?></h4>
                <p class="text-muted mb-0">Published Posts</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-church text-info mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo number_format($stats['total_parishes'] ?? 0); ?></h4>
                <p class="text-muted mb-0">Parishes</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-bell text-warning mb-2" style="font-size: 2rem;"></i>
                <h4 class="mb-1"><?php echo number_format($stats['unread_notifications'] ?? 0); ?></h4>
                <p class="text-muted mb-0">Unread Notifications</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                    <i class="fas fa-history me-2"></i>Recent Admin Activities
                </h5>
                <a href="activities.php" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View All
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_activities)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-history text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-2">No Recent Admin Activities</h6>
                        <p class="text-muted small">Admin actions will appear here once they start performing activities</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item d-flex align-items-start mb-3 pb-3 border-bottom">
                                <div class="activity-icon me-3" style="width: 40px; height: 40px; background: rgba(52, 152, 219, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="<?php echo $activity['icon']; ?> text-primary"></i>
                                </div>
                                <div class="activity-content flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($activity['title']); ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo timeAgo($activity['time']); ?>
                                        </small>
                                    </div>
                                    <p class="text-muted mb-1"><?php echo htmlspecialchars($activity['description']); ?></p>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-2">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($activity['admin_email']); ?>
                                        </span>
                                        <?php if (!empty($activity['ip_address'])): ?>
                                            <span class="badge bg-light text-dark">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($activity['ip_address']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Application Status Chart -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Application Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Applications Chart -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Monthly Applications</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="applications.php?status=pending" class="btn btn-outline-warning w-100">
                            <i class="fas fa-clock mb-2"></i><br>
                            Review Pending Applications
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="meetings.php?date=today" class="btn btn-outline-info w-100">
                            <i class="fas fa-calendar-day mb-2"></i><br>
                            Today's Meetings
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="blog.php?action=new" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus mb-2"></i><br>
                            Create New Post
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="activities.php" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-history mb-2"></i><br>
                            View Admin Activities
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_css = '
<style>
.activity-item {
    transition: all 0.3s ease;
    border-radius: 8px;
    padding: 12px;
    margin: -12px;
}

.activity-item:hover {
    background-color: rgba(52, 152, 219, 0.05);
    transform: translateX(5px);
}

.activity-icon {
    flex-shrink: 0;
}

.activity-content .badge {
    font-size: 0.7rem;
    font-weight: 500;
}

.activity-list .activity-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0;
}

.stats-card {
    border-radius: 15px;
    padding: 1.5rem;
    color: white;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-label {
    font-size: 0.9rem;
    opacity: 0.9;
    font-weight: 500;
}
</style>
';

$additional_js = '
<script>
// Application Status Chart
const statusCtx = document.getElementById("statusChart").getContext("2d");
const statusChart = new Chart(statusCtx, {
    type: "doughnut",
    data: {
        labels: ["Pending", "Processing", "Approved", "Completed", "Rejected"],
        datasets: [{
            data: [
                ' . ($application_status_counts['pending'] ?? 0) . ',
                ' . ($application_status_counts['processing'] ?? 0) . ',
                ' . ($application_status_counts['approved'] ?? 0) . ',
                ' . ($application_status_counts['completed'] ?? 0) . ',
                ' . ($application_status_counts['rejected'] ?? 0) . '
            ],
            backgroundColor: [
                "#f39c12",
                "#3498db", 
                "#27ae60",
                "#9b59b6",
                "#e74c3c"
            ]
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

// Monthly Applications Chart
const monthlyCtx = document.getElementById("monthlyChart").getContext("2d");
const monthlyChart = new Chart(monthlyCtx, {
    type: "line",
    data: {
        labels: [' . implode(',', array_map(function($stat) { return '"' . date('M Y', strtotime($stat['month'] . '-01')) . '"'; }, $monthly_stats)) . '],
        datasets: [{
            label: "Applications",
            data: [' . implode(',', array_column($monthly_stats, 'count')) . '],
            borderColor: "#3498db",
            backgroundColor: "rgba(52, 152, 219, 0.1)",
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
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
';

include 'includes/footer.php';
?>
