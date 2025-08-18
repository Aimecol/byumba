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
            // Fallback to static data
            loadStaticDashboardData();
        }
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        // Fallback to static data
        loadStaticDashboardData();
    }
}

// Global function for language manager to update content
window.updateDashboardContent = function(data) {
    updateStatistics(data.stats);
    updateRecentApplications(data.recent_applications);
    updateUpcomingMeetings(data.upcoming_meetings);
    updateRecentNotifications(data.recent_notifications);
    updateActivityTimeline(data.activity_timeline);
};

// Fallback to static data if API fails
function loadStaticDashboardData() {
    loadRecentApplications();
    loadUpcomingMeetings();
    loadRecentNotifications();
    loadActivityTimeline();
}

// Update Statistics
function updateStatistics(stats) {
    if (!stats) return;

    const statsElements = {
        applications: document.querySelector('.stats-card:nth-child(1) .stats-number'),
        jobs: document.querySelector('.stats-card:nth-child(2) .stats-number'),
        meetings: document.querySelector('.stats-card:nth-child(3) .stats-number'),
        notifications: document.querySelector('.stats-card:nth-child(4) .stats-number')
    };

    if (statsElements.applications) statsElements.applications.textContent = stats.applications || 0;
    if (statsElements.jobs) statsElements.jobs.textContent = stats.job_applications || 0;
    if (statsElements.meetings) statsElements.meetings.textContent = stats.meetings || 0;
    if (statsElements.notifications) statsElements.notifications.textContent = stats.notifications || 0;
}

// Update Recent Applications with API data
function updateRecentApplications(applications) {
    const container = document.getElementById('recentApplications');
    if (!container || !applications) return;

    renderApplications(applications);
}

// Load Recent Applications (fallback)
function loadRecentApplications() {
    const container = document.getElementById('recentApplications');
    if (!container) return;

    // Sample data - fallback when API is not available
    const applications = [
        {
            id: 'APP001',
            type: 'Baptism Certificate',
            status: 'approved',
            date: '2024-01-15',
            fee: 'RWF 2,000'
        },
        {
            id: 'APP002',
            type: 'Marriage Certificate',
            status: 'pending',
            date: '2024-01-12',
            fee: 'RWF 5,000'
        },
        {
            id: 'APP003',
            type: 'Confirmation Certificate',
            status: 'processing',
            date: '2024-01-10',
            fee: 'RWF 2,500'
        },
        {
            id: 'APP004',
            type: 'Membership Certificate',
            status: 'completed',
            date: '2024-01-08',
            fee: 'RWF 1,500'
        }
    ];

    renderApplications(applications);
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

// Load Upcoming Meetings
function loadUpcomingMeetings() {
    const container = document.getElementById('upcomingMeetings');
    if (!container) return;
    
    const meetings = [
        {
            id: 'MTG001',
            title: 'Marriage Counseling Session',
            date: '2024-01-20',
            time: '10:00 AM',
            type: 'counseling',
            status: 'confirmed'
        },
        {
            id: 'MTG002',
            title: 'Baptism Preparation',
            date: '2024-01-22',
            time: '2:00 PM',
            type: 'preparation',
            status: 'pending'
        },
        {
            id: 'MTG003',
            title: 'General Consultation',
            date: '2024-01-25',
            time: '11:00 AM',
            type: 'consultation',
            status: 'confirmed'
        }
    ];
    
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

// Load Recent Notifications
function loadRecentNotifications() {
    const container = document.getElementById('recentNotifications');
    if (!container) return;
    
    const notifications = [
        {
            id: 'NOT001',
            title: 'Application Approved',
            message: 'Your Baptism Certificate application has been approved. Payment code: BC2024001',
            type: 'success',
            date: '2024-01-15T14:30:00',
            read: false
        },
        {
            id: 'NOT002',
            title: 'Meeting Reminder',
            message: 'Your marriage counseling session is scheduled for tomorrow at 10:00 AM',
            type: 'info',
            date: '2024-01-19T09:00:00',
            read: false
        },
        {
            id: 'NOT003',
            title: 'Document Required',
            message: 'Additional documentation needed for your Marriage Certificate application',
            type: 'warning',
            date: '2024-01-18T16:45:00',
            read: true
        },
        {
            id: 'NOT004',
            title: 'Payment Received',
            message: 'Payment confirmed for Membership Certificate. Certificate will be ready in 2-3 days',
            type: 'success',
            date: '2024-01-17T11:20:00',
            read: true
        },
        {
            id: 'NOT005',
            title: 'New Job Posting',
            message: 'A new position matching your profile has been posted: Parish Coordinator',
            type: 'info',
            date: '2024-01-16T08:15:00',
            read: false
        }
    ];
    
    container.innerHTML = notifications.map(notification => `
        <div class="notification-item ${notification.read ? 'read' : 'unread'}" data-id="${notification.id}">
            <div class="notification-icon ${notification.type}">
                <i class="fas ${getNotificationIcon(notification.type)}"></i>
            </div>
            <div class="notification-content">
                <h4 class="notification-title">${notification.title}</h4>
                <p class="notification-message">${notification.message}</p>
                <span class="notification-time">${formatTimeAgo(notification.date)}</span>
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

// Load Activity Timeline
function loadActivityTimeline() {
    const container = document.getElementById('activityTimeline');
    if (!container) return;
    
    const activities = [
        {
            id: 'ACT001',
            type: 'application',
            title: 'Baptism Certificate Application Approved',
            description: 'Your application for Baptism Certificate has been approved. Payment code sent via email.',
            date: '2024-01-15T14:30:00',
            icon: 'fas fa-check-circle',
            color: 'success'
        },
        {
            id: 'ACT002',
            type: 'meeting',
            title: 'Meeting Scheduled',
            description: 'Marriage counseling session scheduled for January 20, 2024 at 10:00 AM',
            date: '2024-01-14T16:20:00',
            icon: 'fas fa-calendar-plus',
            color: 'info'
        },
        {
            id: 'ACT003',
            type: 'application',
            title: 'Marriage Certificate Application Submitted',
            description: 'Application submitted with all required documents. Processing time: 5-7 business days.',
            date: '2024-01-12T11:45:00',
            icon: 'fas fa-file-upload',
            color: 'primary'
        },
        {
            id: 'ACT004',
            type: 'jobs',
            title: 'Job Application Submitted',
            description: 'Applied for Parish Coordinator position at St. Mary\'s Parish',
            date: '2024-01-10T09:30:00',
            icon: 'fas fa-briefcase',
            color: 'warning'
        },
        {
            id: 'ACT005',
            type: 'application',
            title: 'Confirmation Certificate Completed',
            description: 'Certificate ready for pickup. Payment confirmed.',
            date: '2024-01-08T13:15:00',
            icon: 'fas fa-certificate',
            color: 'success'
        }
    ];
    
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
        showNotification(`Meeting ${meetingId} cancelled`, 'success');
        // In real app, this would make API call to cancel meeting
        setTimeout(() => {
            loadUpcomingMeetings(); // Refresh the list
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
            showNotification('Notification deleted', 'success');
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
