// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Wait for auth guard to complete before initializing dashboard
    if (window.authGuard) {
        // Dashboard will be initialized after authentication is verified
        // This is handled by the auth guard
        setTimeout(initializeDashboard, 100);
    } else {
        // Fallback if auth guard is not available
        initializeDashboard();
    }
});

function initializeDashboard() {
    // Initialize user menu
    initializeUserMenu();
    
    // Load dashboard data
    loadDashboardData();
    
    // Initialize quick actions
    initializeQuickActions();
    
    // Initialize filters
    initializeFilters();
    
    // Initialize notifications
    initializeNotifications();
    
    // Auto-refresh data every 5 minutes
    setInterval(refreshDashboardData, 300000);
}

// User Menu Functionality
function initializeUserMenu() {
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userDropdown = document.getElementById('userDropdown');
    
    if (userMenuToggle && userDropdown) {
        userMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            userDropdown.classList.remove('active');
        });
        
        // Prevent dropdown from closing when clicking inside
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Load Dashboard Data from API
async function loadDashboardData() {
    try {
        // Get current user from auth guard
        const currentUser = window.authGuard ? window.authGuard.getCurrentUser() : null;

        const response = await fetch('api/index.php?endpoint=dashboard');
        const data = await response.json();

        if (data.success) {
            updateDashboardContent(data.data);
        } else {
            console.error('Failed to load dashboard data:', data.message);
            showDashboardError('Failed to load dashboard data: ' + data.message);
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        showDashboardError('Unable to connect to the server. Please check your internet connection and try again.');
    }
}

// Global function for language manager to update content
window.updateDashboardContent = function(data) {
    updateStatistics(data.stats);

    // Update sections with data or show empty states
    if (data.recent_applications && data.recent_applications.length > 0) {
        updateRecentApplications(data.recent_applications);
    } else {
        showEmptyApplications();
    }

    if (data.upcoming_meetings && data.upcoming_meetings.length > 0) {
        updateUpcomingMeetings(data.upcoming_meetings);
    } else {
        showEmptyMeetings();
    }

    if (data.recent_notifications && data.recent_notifications.length > 0) {
        updateRecentNotifications(data.recent_notifications);
    } else {
        showEmptyNotifications();
    }

    if (data.activity_timeline && data.activity_timeline.length > 0) {
        updateActivityTimeline(data.activity_timeline);
    } else {
        showEmptyActivityTimeline();
    }

    updateWelcomeMessage();
};

// Update welcome message with user's name
async function updateWelcomeMessage() {
    try {
        const response = await fetch('api/index.php?endpoint=auth&action=user');
        const data = await response.json();

        if (data.success && data.data) {
            const welcomeTitle = document.getElementById('welcomeTitle');
            if (welcomeTitle) {
                welcomeTitle.textContent = `Welcome back, ${data.data.first_name}!`;
            }
        }
    } catch (error) {
        console.error('Error loading user data:', error);
        // Keep default welcome message
    }
}

// Show dashboard error message
function showDashboardError(message) {
    // Show error in each dashboard section
    showSectionError('recentApplications', 'Unable to load recent applications');
    showSectionError('upcomingMeetings', 'Unable to load upcoming meetings');
    showSectionError('recentNotifications', 'Unable to load recent notifications');
    showSectionError('activityTimeline', 'Unable to load activity timeline');

    // Show main error message
    const errorContainer = document.createElement('div');
    errorContainer.className = 'dashboard-error';
    errorContainer.innerHTML = `
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>Unable to Load Dashboard Data</h3>
            <p>${message}</p>
            <button class="retry-btn" onclick="loadDashboardData()">
                <i class="fas fa-refresh"></i>
                Try Again
            </button>
        </div>
    `;

    // Insert error message at the top of dashboard content
    const dashboardContent = document.querySelector('.dashboard-grid');
    if (dashboardContent) {
        dashboardContent.insertBefore(errorContainer, dashboardContent.firstChild);
    }
}

// Show section-specific error
function showSectionError(containerId, message) {
    const container = document.getElementById(containerId);
    if (container) {
        container.innerHTML = `
            <div class="section-error">
                <i class="fas fa-exclamation-circle"></i>
                <p>${message}</p>
            </div>
        `;
    }
}

// Show dashboard notification
function showDashboardNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `dashboard-notification ${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Update Statistics
function updateStatistics(stats) {
    if (!stats) return;

    const statsElements = {
        applications: document.querySelector('.stat-card:nth-child(1) .stat-number'),
        jobs: document.querySelector('.stat-card:nth-child(2) .stat-number'),
        meetings: document.querySelector('.stat-card:nth-child(3) .stat-number'),
        notifications: document.querySelector('.stat-card:nth-child(4) .stat-number')
    };

    if (statsElements.applications) statsElements.applications.textContent = stats.applications || 0;
    if (statsElements.jobs) statsElements.jobs.textContent = stats.job_applications || 0;
    if (statsElements.meetings) statsElements.meetings.textContent = stats.meetings || 0;
    if (statsElements.notifications) statsElements.notifications.textContent = stats.notifications || 0;

    // Update notification badge in navigation
    const notificationBadge = document.querySelector('.notification-badge');
    if (notificationBadge && stats.notifications) {
        notificationBadge.textContent = stats.notifications;
        notificationBadge.style.display = stats.notifications > 0 ? 'inline' : 'none';
    }
}

// Update Recent Applications with API data
function updateRecentApplications(applications) {
    const container = document.getElementById('recentApplications');
    if (!container || !applications) return;

    renderApplications(applications);
}

// Update Upcoming Meetings with API data
function updateUpcomingMeetings(meetings) {
    const container = document.getElementById('upcomingMeetings');
    if (!container || !meetings) return;

    renderMeetings(meetings);
}

// Update Recent Notifications with API data
function updateRecentNotifications(notifications) {
    const container = document.getElementById('recentNotifications');
    if (!container || !notifications) return;

    renderNotifications(notifications);
}

// Update Activity Timeline with API data
function updateActivityTimeline(activities) {
    const container = document.getElementById('activityTimeline');
    if (!container || !activities) return;

    renderActivityTimeline(activities);
}

// Show empty state for applications
function showEmptyApplications() {
    const container = document.getElementById('recentApplications');
    if (!container) return;

    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-file-alt"></i>
            <h4>No Applications Yet</h4>
            <p>You haven't submitted any certificate applications.</p>
            <a href="application.html" class="empty-action-btn">
                <i class="fas fa-plus"></i>
                Submit Your First Application
            </a>
        </div>
    `;
}

// Render Applications
function renderApplications(applications) {
    const container = document.getElementById('recentApplications');
    if (!container) return;
    
    container.innerHTML = applications.map(app => `
        <div class="application-item">
            <div class="application-info">
                <div class="application-header">
                    <h4 class="application-title">${app.type}</h4>
                    <span class="application-status status-${app.status}">${getStatusText(app.status)}</span>
                </div>
                <div class="application-details">
                    <span class="application-id">ID: ${app.id}</span>
                    <span class="application-date">${formatDate(app.date)}</span>
                    <span class="application-fee">${app.fee}</span>
                </div>
            </div>
            <div class="application-actions">
                <button class="action-btn view-btn" data-id="${app.id}">
                    <i class="fas fa-eye"></i>
                </button>
                ${app.status === 'approved' ? `
                    <button class="action-btn pay-btn" data-id="${app.id}">
                        <i class="fas fa-credit-card"></i>
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    // Add event listeners for action buttons
    container.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.getAttribute('data-id');
            if (this.classList.contains('view-btn')) {
                viewApplication(appId);
            } else if (this.classList.contains('pay-btn')) {
                payApplication(appId);
            }
        });
    });
}

// Show empty state for meetings
function showEmptyMeetings() {
    const container = document.getElementById('upcomingMeetings');
    if (!container) return;

    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-calendar-check"></i>
            <h4>No Upcoming Meetings</h4>
            <p>You don't have any scheduled meetings with the Bishop.</p>
            <a href="bishop-meeting.html" class="empty-action-btn">
                <i class="fas fa-plus"></i>
                Schedule a Meeting
            </a>
        </div>
    `;
}

// Render Meetings
function renderMeetings(meetings) {
    const container = document.getElementById('upcomingMeetings');
    if (!container) return;

    container.innerHTML = meetings.map(meeting => `
        <div class="meeting-item">
            <div class="meeting-info">
                <h4 class="meeting-title">${meeting.title}</h4>
                <div class="meeting-details">
                    <span class="meeting-date">
                        <i class="fas fa-calendar"></i>
                        ${formatDate(meeting.date)}
                    </span>
                    <span class="meeting-time">
                        <i class="fas fa-clock"></i>
                        ${meeting.time}
                    </span>
                    ${meeting.location ? `
                        <span class="meeting-location">
                            <i class="fas fa-map-marker-alt"></i>
                            ${meeting.location}
                        </span>
                    ` : ''}
                </div>
                <span class="meeting-status status-${meeting.status}">${getStatusText(meeting.status)}</span>
            </div>
            <div class="meeting-actions">
                <button class="action-btn edit-btn" data-id="${meeting.id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="action-btn cancel-btn" data-id="${meeting.id}">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `).join('');

    // Add event listeners
    container.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const meetingId = this.getAttribute('data-id');
            if (this.classList.contains('edit-btn')) {
                editMeeting(meetingId);
            } else if (this.classList.contains('cancel-btn')) {
                cancelMeeting(meetingId);
            }
        });
    });
}

// Show empty state for notifications
function showEmptyNotifications() {
    const container = document.getElementById('recentNotifications');
    if (!container) return;

    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-bell"></i>
            <h4>No Recent Notifications</h4>
            <p>You're all caught up! No new notifications at this time.</p>
            <a href="notifications.html" class="empty-action-btn">
                <i class="fas fa-eye"></i>
                View All Notifications
            </a>
        </div>
    `;
}

// Render Notifications
function renderNotifications(notifications) {
    const container = document.getElementById('recentNotifications');
    if (!container) return;

    container.innerHTML = notifications.map(notification => `
        <div class="notification-item ${notification.read ? 'read' : 'unread'}" data-id="${notification.id}">
            <div class="notification-icon ${notification.type}">
                <i class="fas ${getNotificationIcon(notification.type)}"></i>
            </div>
            <div class="notification-content">
                <h4 class="notification-title">${notification.title}</h4>
                <p class="notification-message">${notification.message}</p>
                <span class="notification-time">${formatTimeAgo(notification.date)}</span>
                ${notification.action_required && notification.action_text ? `
                    <div class="notification-action">
                        <a href="${notification.action_url}" class="action-link">
                            <i class="fas fa-arrow-right"></i>
                            ${notification.action_text}
                        </a>
                    </div>
                ` : ''}
            </div>
            <div class="notification-actions">
                ${!notification.read ? `
                    <button class="action-btn mark-read-btn" data-id="${notification.id}">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
                <button class="action-btn delete-btn" data-id="${notification.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');

    // Add event listeners
    container.querySelectorAll('.action-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.getAttribute('data-id');
            if (this.classList.contains('mark-read-btn')) {
                markNotificationRead(notificationId);
            } else if (this.classList.contains('delete-btn')) {
                deleteNotification(notificationId);
            }
        });
    });

    // Mark as read when clicked
    container.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            if (this.classList.contains('unread')) {
                const notificationId = this.getAttribute('data-id');
                markNotificationRead(notificationId);
            }
        });
    });
}

// Show empty state for activity timeline
function showEmptyActivityTimeline() {
    const container = document.getElementById('activityTimeline');
    if (!container) return;

    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h4>No Recent Activity</h4>
            <p>Your activity timeline will appear here as you interact with the system.</p>
            <div class="empty-actions">
                <a href="application.html" class="empty-action-btn">
                    <i class="fas fa-file-alt"></i>
                    Submit Application
                </a>
                <a href="bishop-meeting.html" class="empty-action-btn">
                    <i class="fas fa-calendar-plus"></i>
                    Schedule Meeting
                </a>
            </div>
        </div>
    `;
}

// Render Activity Timeline
function renderActivityTimeline(activities) {
    const container = document.getElementById('activityTimeline');
    if (!container) return;

    container.innerHTML = `
        <div class="timeline">
            ${activities.map(activity => `
                <div class="timeline-item" data-type="${activity.type}">
                    <div class="timeline-marker ${activity.color}">
                        <i class="${activity.icon}"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h4 class="timeline-title">${activity.title}</h4>
                            <span class="timeline-time">${formatTimeAgo(activity.date)}</span>
                        </div>
                        <p class="timeline-description">${activity.description}</p>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Quick Actions
function initializeQuickActions() {
    const quickActionBtns = document.querySelectorAll('.quick-action-btn');
    
    quickActionBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            
            switch(action) {
                case 'new-application':
                    window.location.href = 'application.html';
                    break;
                case 'schedule-meeting':
                    window.location.href = 'bishop-meeting.html';
                    break;
            }
        });
    });
}

// Initialize Filters
function initializeFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Filter timeline items
            const filter = this.getAttribute('data-filter');
            filterTimeline(filter);
        });
    });
}

// Filter Timeline
function filterTimeline(filter) {
    const timelineItems = document.querySelectorAll('.timeline-item');
    
    timelineItems.forEach(item => {
        if (filter === 'all' || item.getAttribute('data-type') === filter) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Initialize Notifications
function initializeNotifications() {
    const markAllReadBtn = document.getElementById('markAllRead');
    
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            markAllNotificationsRead();
        });
    }
}

// Utility Functions
function getStatusText(status) {
    const statusMap = {
        'pending': 'Pending',
        'processing': 'Processing',
        'approved': 'Approved',
        'completed': 'Completed',
        'confirmed': 'Confirmed',
        'cancelled': 'Cancelled'
    };
    return statusMap[status] || status;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    
    return formatDate(dateString);
}

function getNotificationIcon(type) {
    const iconMap = {
        'success': 'fa-check-circle',
        'info': 'fa-info-circle',
        'warning': 'fa-exclamation-triangle',
        'error': 'fa-times-circle'
    };
    return iconMap[type] || 'fa-bell';
}

// Action Functions
function viewApplication(appId) {
    showNotification(`Viewing application ${appId}`, 'info');
    // In real app, this would open application details modal or page
}

function payApplication(appId) {
    showNotification(`Redirecting to payment for application ${appId}`, 'info');
    // In real app, this would redirect to payment page
}

function editMeeting(meetingId) {
    showNotification(`Editing meeting ${meetingId}`, 'info');
    // In real app, this would open meeting edit modal
}

function cancelMeeting(meetingId) {
    if (confirm('Are you sure you want to cancel this meeting?')) {
        showDashboardNotification(`Meeting ${meetingId} cancelled`, 'success');
        // In real app, this would make API call to cancel meeting
        setTimeout(() => {
            loadDashboardData(); // Refresh the dashboard data
        }, 1000);
    }
}

function markNotificationRead(notificationId) {
    const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
    if (notificationItem) {
        notificationItem.classList.remove('unread');
        notificationItem.classList.add('read');
        
        // Remove mark as read button
        const markReadBtn = notificationItem.querySelector('.mark-read-btn');
        if (markReadBtn) {
            markReadBtn.remove();
        }
    }
    
    // Update notification count in stats
    updateNotificationCount();
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const notificationItem = document.querySelector(`[data-id="${notificationId}"]`);
        if (notificationItem) {
            notificationItem.remove();
            showDashboardNotification('Notification deleted', 'success');
        }
        
        // Update notification count in stats
        updateNotificationCount();
    }
}

function markAllNotificationsRead() {
    const unreadNotifications = document.querySelectorAll('.notification-item.unread');
    
    unreadNotifications.forEach(notification => {
        notification.classList.remove('unread');
        notification.classList.add('read');
        
        // Remove mark as read button
        const markReadBtn = notification.querySelector('.mark-read-btn');
        if (markReadBtn) {
            markReadBtn.remove();
        }
    });
    
    showNotification('All notifications marked as read', 'success');
    updateNotificationCount();
}

function updateNotificationCount() {
    const unreadCount = document.querySelectorAll('.notification-item.unread').length;
    const statNumber = document.querySelector('.stat-card:last-child .stat-number');
    const statChange = document.querySelector('.stat-card:last-child .stat-change');
    
    if (statNumber) {
        statNumber.textContent = unreadCount + document.querySelectorAll('.notification-item.read').length;
    }
    
    if (statChange) {
        statChange.textContent = unreadCount > 0 ? `${unreadCount} unread` : 'All read';
        statChange.className = unreadCount > 0 ? 'stat-change attention' : 'stat-change positive';
    }
}

function refreshDashboardData() {
    // In real app, this would fetch fresh data from API
    console.log('Refreshing dashboard data...');
    loadDashboardData();
}
