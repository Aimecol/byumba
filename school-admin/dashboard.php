<?php
/**
 * School Administration Dashboard
 * Diocese of Byumba - School Management System
 */

session_start();
$pageTitle = 'Dashboard';
require_once 'includes/functions.php';

// Get current user and school info
$currentUser = $schoolAuth->getCurrentUser();

// Get dashboard statistics
function getDashboardStats($db, $schoolId) {
    $stats = [];
    
    try {
        // Total reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':school_id', $schoolId);
        $stmt->execute();
        $stats['total_reports'] = $stmt->fetch()['total'];

        // Pending reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id AND status IN ('draft', 'submitted')";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':school_id', $schoolId);
        $stmt->execute();
        $stats['pending_reports'] = $stmt->fetch()['total'];

        // Approved reports
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id AND status = 'approved'";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':school_id', $schoolId);
        $stmt->execute();
        $stats['approved_reports'] = $stmt->fetch()['total'];

        // Reports this month
        $query = "SELECT COUNT(*) as total FROM school_reports WHERE school_id = :school_id AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':school_id', $schoolId);
        $stmt->execute();
        $stats['monthly_reports'] = $stmt->fetch()['total'];
        
    } catch(PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        $stats = [
            'total_reports' => 0,
            'pending_reports' => 0,
            'approved_reports' => 0,
            'monthly_reports' => 0
        ];
    }
    
    return $stats;
}

// Get recent reports
function getRecentReports($db, $schoolId, $limit = 5) {
    try {
        $query = "SELECT sr.*, rt.type_name, rt.type_code 
                 FROM school_reports sr
                 JOIN report_types rt ON sr.report_type_id = rt.id
                 WHERE sr.school_id = :school_id
                 ORDER BY sr.created_at DESC
                 LIMIT :limit";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':school_id', $schoolId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Recent reports error: " . $e->getMessage());
        return [];
    }
}

$stats = getDashboardStats($db, $currentUser['school_id']);
$recentReports = getRecentReports($db, $currentUser['school_id']);

require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="dashboard-header text-center mb-4">
            <h1 class="mb-2">
                <i class="fas fa-tachometer-alt me-3"></i>
                Welcome to <?php echo htmlspecialchars($currentUser['school_name']); ?>
            </h1>
            <p class="mb-0 opacity-75">
                School Code: <?php echo htmlspecialchars($currentUser['school_code']); ?> | 
                Type: <?php echo ucfirst($currentUser['school_type']); ?> | 
                User: <?php echo htmlspecialchars($currentUser['user_name']); ?> (<?php echo ucfirst($currentUser['user_role']); ?>)
            </p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon text-primary mb-2">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-number"><?php echo $stats['total_reports']; ?></div>
                <div class="stat-label">Total Reports</div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon text-warning mb-2">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $stats['pending_reports']; ?></div>
                <div class="stat-label">Pending Reports</div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon text-success mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $stats['approved_reports']; ?></div>
                <div class="stat-label">Approved Reports</div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card stat-card h-100">
            <div class="card-body text-center">
                <div class="stat-icon text-info mb-2">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-number"><?php echo $stats['monthly_reports']; ?></div>
                <div class="stat-label">This Month</div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="quick-actions text-center">
                    <a href="create-report.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Create New Report
                    </a>
                    <a href="reports.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-list me-2"></i>View All Reports
                    </a>
                    <a href="reports.php?status=draft" class="btn btn-outline-warning btn-lg">
                        <i class="fas fa-edit me-2"></i>Continue Draft
                    </a>
                    <a href="profile.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-user me-2"></i>Update Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reports -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Reports
                </h5>
                <a href="reports.php" class="btn btn-sm btn-outline-primary">
                    View All <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                <?php if (empty($recentReports)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No reports found</h6>
                        <p class="text-muted">You haven't created any reports yet.</p>
                        <a href="create-report.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Your First Report
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Report Number</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentReports as $report): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($report['report_number']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($report['type_name']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($report['title']); ?></td>
                                        <td>
                                            <span class="badge <?php echo getStatusBadgeClass($report['status']); ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $report['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($report['created_at']); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="view-report.php?id=<?php echo $report['id']; ?>" 
                                                   class="btn btn-outline-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($report['status'] == 'draft'): ?>
                                                    <a href="edit-report.php?id=<?php echo $report['id']; ?>" 
                                                       class="btn btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
