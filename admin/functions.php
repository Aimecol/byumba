<?php
/**
 * Admin Functions
 * Diocese of Byumba Admin Panel
 */

require_once 'auth.php';

/**
 * Get dashboard statistics
 */
function getDashboardStats() {
    global $db;
    
    try {
        $stats = [];
        
        // Total users
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch()['total'];
        
        // Total applications
        $query = "SELECT COUNT(*) as total FROM applications";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['total_applications'] = $stmt->fetch()['total'];
        
        // Pending applications
        $query = "SELECT COUNT(*) as total FROM applications WHERE status = 'pending'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['pending_applications'] = $stmt->fetch()['total'];
        
        // Today's meetings
        $query = "SELECT COUNT(*) as total FROM meetings WHERE meeting_date = CURDATE()";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['todays_meetings'] = $stmt->fetch()['total'];
        
        // Active jobs
        $query = "SELECT COUNT(*) as total FROM jobs WHERE is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['active_jobs'] = $stmt->fetch()['total'];
        
        // Published blog posts
        $query = "SELECT COUNT(*) as total FROM blog_posts WHERE is_published = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['published_posts'] = $stmt->fetch()['total'];
        
        // Total parishes
        $query = "SELECT COUNT(*) as total FROM parishes WHERE is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['total_parishes'] = $stmt->fetch()['total'];
        
        // Unread notifications
        $query = "SELECT COUNT(*) as total FROM notifications WHERE is_read = 0";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats['unread_notifications'] = $stmt->fetch()['total'];
        
        return $stats;
    } catch(PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recent activities
 */
function getRecentActivities($limit = 10) {
    global $db;
    
    try {
        $activities = [];
        
        // Recent applications
        $query = "SELECT a.id, a.application_number, a.status, a.created_at, 
                         u.first_name, u.last_name, ct.name as certificate_type
                  FROM applications a
                  JOIN users u ON a.user_id = u.id
                  JOIN certificate_types ct ON a.certificate_type_id = ct.id
                  JOIN certificate_type_translations ctt ON ct.id = ctt.certificate_type_id 
                  WHERE ctt.language_code = 'en'
                  ORDER BY a.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch()) {
            $activities[] = [
                'type' => 'application',
                'icon' => 'fa-file-alt',
                'title' => 'New Application',
                'description' => $row['first_name'] . ' ' . $row['last_name'] . ' applied for ' . $row['certificate_type'],
                'status' => $row['status'],
                'time' => $row['created_at'],
                'link' => 'applications.php?id=' . $row['id']
            ];
        }
        
        // Recent meetings
        $query = "SELECT m.id, m.title, m.meeting_date, m.meeting_time, m.status,
                         u.first_name, u.last_name
                  FROM meetings m
                  JOIN users u ON m.user_id = u.id
                  ORDER BY m.created_at DESC 
                  LIMIT :limit";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch()) {
            $activities[] = [
                'type' => 'meeting',
                'icon' => 'fa-calendar',
                'title' => 'Meeting Scheduled',
                'description' => $row['title'] . ' with ' . $row['first_name'] . ' ' . $row['last_name'],
                'status' => $row['status'],
                'time' => $row['meeting_date'] . ' ' . $row['meeting_time'],
                'link' => 'meetings.php?id=' . $row['id']
            ];
        }
        
        // Sort activities by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        return array_slice($activities, 0, $limit);
    } catch(PDOException $e) {
        error_log("Recent activities error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recent admin activities
 */
function getRecentAdminActivities($limit = 10) {
    global $db;

    // Return empty array if no database connection
    if ($db === null) {
        return [];
    }

    try {
        $query = "SELECT aal.*, u.first_name, u.last_name
                  FROM admin_activity_log aal
                  LEFT JOIN users u ON aal.admin_id = u.id
                  ORDER BY aal.created_at DESC
                  LIMIT :limit";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $activities = [];
        while ($row = $stmt->fetch()) {
            $admin_name = ($row['first_name'] && $row['last_name'])
                ? $row['first_name'] . ' ' . $row['last_name']
                : $row['admin_email'];

            $activities[] = [
                'type' => 'admin_activity',
                'icon' => getAdminActionIcon($row['action']),
                'title' => getAdminActionTitle($row['action']),
                'description' => $admin_name . ': ' . ($row['details'] ?: 'No details provided'),
                'status' => 'completed',
                'time' => $row['created_at'],
                'link' => 'activities.php',
                'admin_email' => $row['admin_email'],
                'action' => $row['action'],
                'ip_address' => $row['ip_address']
            ];
        }

        return $activities;
    } catch(PDOException $e) {
        error_log("Recent admin activities error: " . $e->getMessage());
        // If table doesn't exist, return empty array gracefully
        return [];
    }
}

/**
 * Get admin action icon
 */
function getAdminActionIcon($action) {
    $icons = [
        'login' => 'fas fa-sign-in-alt',
        'logout' => 'fas fa-sign-out-alt',
        'create' => 'fas fa-plus-circle',
        'update' => 'fas fa-edit',
        'delete' => 'fas fa-trash-alt',
        'approve' => 'fas fa-check-circle',
        'reject' => 'fas fa-times-circle',
        'toggle' => 'fas fa-toggle-on',
        'send' => 'fas fa-paper-plane',
        'export' => 'fas fa-download',
        'clear' => 'fas fa-broom'
    ];

    foreach ($icons as $key => $icon) {
        if (strpos($action, $key) !== false) {
            return $icon;
        }
    }

    return 'fas fa-cog';
}

/**
 * Get admin action title
 */
function getAdminActionTitle($action) {
    $titles = [
        'login' => 'Admin Login',
        'logout' => 'Admin Logout',
        'create_user' => 'User Created',
        'update_user' => 'User Updated',
        'delete_user' => 'User Deleted',
        'create_blog_post' => 'Blog Post Created',
        'update_blog_post' => 'Blog Post Updated',
        'delete_blog_post' => 'Blog Post Deleted',
        'toggle_blog_post' => 'Blog Post Status Changed',
        'approve_application' => 'Application Approved',
        'reject_application' => 'Application Rejected',
        'update_application_status' => 'Application Status Updated',
        'send_notification' => 'Notification Sent',
        'export_users' => 'Users Exported',
        'export_activities' => 'Activities Exported',
        'clear_old_activities' => 'Old Activities Cleared',
        'toggle_parish_status' => 'Parish Status Changed',
        'toggle_job_status' => 'Job Status Changed'
    ];

    return $titles[$action] ?? ucwords(str_replace('_', ' ', $action));
}

/**
 * Get application status counts
 */
function getApplicationStatusCounts() {
    global $db;
    
    try {
        $query = "SELECT status, COUNT(*) as count 
                  FROM applications 
                  GROUP BY status";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $counts = [];
        while ($row = $stmt->fetch()) {
            $counts[$row['status']] = $row['count'];
        }
        
        return $counts;
    } catch(PDOException $e) {
        error_log("Application status counts error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get monthly application statistics
 */
function getMonthlyApplicationStats($months = 6) {
    global $db;
    
    try {
        $query = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count
                  FROM applications 
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL :months MONTH)
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                  ORDER BY month";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':months', $months, PDO::PARAM_INT);
        $stmt->execute();
        
        $stats = [];
        while ($row = $stmt->fetch()) {
            $stats[] = [
                'month' => $row['month'],
                'count' => $row['count']
            ];
        }
        
        return $stats;
    } catch(PDOException $e) {
        error_log("Monthly application stats error: " . $e->getMessage());
        return [];
    }
}

/**
 * Format status badge
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'processing' => '<span class="badge bg-info">Processing</span>',
        'approved' => '<span class="badge bg-success">Approved</span>',
        'completed' => '<span class="badge bg-primary">Completed</span>',
        'rejected' => '<span class="badge bg-danger">Rejected</span>',
        'confirmed' => '<span class="badge bg-success">Confirmed</span>',
        'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
        'paid' => '<span class="badge bg-success">Paid</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
}

/**
 * Format date for display
 */
function formatDisplayDate($date) {
    return date('M j, Y', strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDisplayDateTime($datetime) {
    return date('M j, Y g:i A', strtotime($datetime));
}

/**
 * Get time ago format
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

/**
 * Generate pagination
 */
function generatePagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) return '';
    
    $pagination = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . '&page=' . $prev_page . '">Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    if ($start > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . '&page=1">1</a></li>';
        if ($start > 2) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? ' active' : '';
        $pagination .= '<li class="page-item' . $active . '"><a class="page-link" href="' . $base_url . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $pagination .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . '&page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $base_url . '&page=' . $next_page . '">Next</a></li>';
    }
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

/**
 * Log admin activity
 */
function logAdminActivity($action, $details = '') {
    global $db;

    if (!isAdminLoggedIn() || $db === null) return;

    try {
        $admin = getCurrentAdmin();
        $query = "INSERT INTO admin_activity_log (admin_id, admin_email, action, details, ip_address, user_agent, created_at) 
                  VALUES (:admin_id, :admin_email, :action, :details, :ip_address, :user_agent, NOW())";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':admin_id', $admin['id']);
        $stmt->bindParam(':admin_email', $admin['email']);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':details', $details);
        $stmt->bindParam(':ip_address', $_SERVER['REMOTE_ADDR']);
        $stmt->bindParam(':user_agent', $_SERVER['HTTP_USER_AGENT']);
        $stmt->execute();
    } catch(PDOException $e) {
        // Log error but don't break the application
        error_log("Admin activity log error: " . $e->getMessage());
    }
}

?>
