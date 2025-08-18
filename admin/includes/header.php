<?php
/**
 * Admin Header Include
 * Diocese of Byumba Admin Panel
 */

if (!defined('ADMIN_PAGE')) {
    die('Direct access not allowed');
}

$current_admin = getCurrentAdmin();
$page_title = $page_title ?? 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Diocese of Byumba Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Admin CSS -->
    <link href="assets/css/admin.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --sidebar-width: 250px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-bg);
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }
        
        .sidebar-header .logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .sidebar-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .sidebar-header p {
            margin: 0;
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin-bottom: 0.25rem;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent-color);
        }
        
        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.15);
            border-left-color: var(--accent-color);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }
        
        .nav-link .nav-text {
            transition: opacity 0.3s ease;
        }
        
        .sidebar.collapsed .nav-text,
        .sidebar.collapsed .sidebar-header h5,
        .sidebar.collapsed .sidebar-header p {
            opacity: 0;
            pointer-events: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        .sidebar.collapsed + .main-content {
            margin-left: 70px;
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .top-nav .nav-left {
            display: flex;
            align-items: center;
        }
        
        .top-nav .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--primary-color);
            cursor: pointer;
            margin-right: 1rem;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item a {
            color: var(--accent-color);
            text-decoration: none;
        }
        
        /* User Dropdown */
        .user-dropdown .dropdown-toggle {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
        }
        
        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        /* Content Area */
        .content-area {
            padding: 0 1.5rem 1.5rem;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            border-radius: 10px 10px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        
        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, var(--accent-color), #5dade2);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .stats-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .stats-card .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }
        
        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-card .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-primary:hover {
            background: #2980b9;
            border-color: #2980b9;
            transform: translateY(-1px);
        }
        
        /* Tables */
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead th {
            background: var(--light-bg);
            border: none;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .table tbody tr:hover {
            background: rgba(52, 152, 219, 0.05);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar.collapsed + .main-content {
                margin-left: 0;
            }
        }
        
        /* Custom scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
        }
    </style>
    
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <i class="fas fa-church"></i>
            </div>
            <h5>Diocese Admin</h5>
            <p>Management Panel</p>
        </div>
        
        <ul class="sidebar-nav nav flex-column">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="users.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Users</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="applications.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'applications.php') ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">Applications</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="meetings.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'meetings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-calendar"></i>
                    <span class="nav-text">Meetings</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="jobs.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'jobs.php') ? 'active' : ''; ?>">
                    <i class="fas fa-briefcase"></i>
                    <span class="nav-text">Jobs</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="blog.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>">
                    <i class="fas fa-blog"></i>
                    <span class="nav-text">Blog</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="parishes.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'parishes.php') ? 'active' : ''; ?>">
                    <i class="fas fa-church"></i>
                    <span class="nav-text">Parishes</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="notifications.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'notifications.php') ? 'active' : ''; ?>">
                    <i class="fas fa-bell"></i>
                    <span class="nav-text">Notifications</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="reports.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="activities.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'activities.php') ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span class="nav-text">Activities</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="settings.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="nav-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Admin</a></li>
                        <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                            <?php foreach ($breadcrumbs as $breadcrumb): ?>
                                <?php if (isset($breadcrumb['url'])): ?>
                                    <li class="breadcrumb-item"><a href="<?php echo $breadcrumb['url']; ?>"><?php echo $breadcrumb['title']; ?></a></li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active"><?php echo $breadcrumb['title']; ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="breadcrumb-item active"><?php echo $page_title; ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            
            <div class="nav-right">
                <!-- Notifications -->
                <div class="dropdown">
                    <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            3
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header">Notifications</h6></li>
                        <li><a class="dropdown-item" href="#">New application received</a></li>
                        <li><a class="dropdown-item" href="#">Meeting scheduled</a></li>
                        <li><a class="dropdown-item" href="#">Payment confirmed</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="notifications.php">View all</a></li>
                    </ul>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($current_admin['name'], 0, 1)); ?>
                        </div>
                        <span><?php echo htmlspecialchars($current_admin['name']); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><h6 class="dropdown-header"><?php echo htmlspecialchars($current_admin['email']); ?></h6></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
