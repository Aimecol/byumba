// Notifications Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize notifications page
    initializeNotificationsPage();
});

function initializeNotificationsPage() {
    // Initialize user menu
    initializeUserMenu();
    
    // Load notifications data
    loadNotificationsData();
    
    // Initialize filters
    initializeFilters();
    
    // Initialize bulk actions
    initializeBulkActions();
    
    // Initialize pagination
    initializePagination();
    
    // Initialize modal
    initializeModal();
    
    // Initialize page actions
    initializePageActions();
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
        
        document.addEventListener('click', function() {
            userDropdown.classList.remove('active');
        });
        
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Sample notifications data
const notificationsData = [
    {
        id: 'NOT001',
        title: 'Application Approved',
        message: 'Your Baptism Certificate application has been approved. Payment code: BC2024001. Please proceed with payment to complete the process.',
        type: 'application',
        priority: 'high',
        date: '2024-01-19T14:30:00',
        read: false,
        category: 'Certificate Application',
        actionRequired: true,
        actionText: 'Make Payment',
        actionUrl: '#payment'
    },
    {
        id: 'NOT002',
        title: 'Meeting Reminder',
        message: 'Your marriage counseling session is scheduled for tomorrow (January 20, 2024) at 10:00 AM in the Bishop\'s Office. Please arrive 15 minutes early.',
        type: 'meeting',
        priority: 'high',
        date: '2024-01-19T09:00:00',
        read: false,
        category: 'Meeting Reminder',
        actionRequired: true,
        actionText: 'View Meeting',
        actionUrl: 'my-meetings.html'
    },
    {
        id: 'NOT003',
        title: 'Document Required',
        message: 'Additional documentation is needed for your Marriage Certificate application (APP002). Please upload the missing birth certificate within 7 days.',
        type: 'application',
        priority: 'medium',
        date: '2024-01-18T16:45:00',
        read: false,
        category: 'Document Request',
        actionRequired: true,
        actionText: 'Upload Document',
        actionUrl: 'my-applications.html'
    },
    {
        id: 'NOT004',
        title: 'Payment Received',
        message: 'Payment confirmed for Membership Certificate (APP004). Your certificate will be ready for pickup in 2-3 business days. You will receive another notification when ready.',
        type: 'payment',
        priority: 'medium',
        date: '2024-01-17T11:20:00',
        read: true,
        category: 'Payment Confirmation',
        actionRequired: false
    },
    {
        id: 'NOT005',
        title: 'New Job Posting',
        message: 'A new position matching your profile has been posted: Parish Coordinator at St. Mary\'s Parish. Application deadline: January 30, 2024.',
        type: 'general',
        priority: 'low',
        date: '2024-01-16T08:15:00',
        read: true,
        category: 'Job Opportunity',
        actionRequired: true,
        actionText: 'View Job',
        actionUrl: 'jobs.html'
    },
    {
        id: 'NOT006',
        title: 'System Maintenance',
        message: 'Scheduled system maintenance will occur on January 21, 2024, from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.',
        type: 'system',
        priority: 'medium',
        date: '2024-01-15T10:00:00',
        read: true,
        category: 'System Notice',
        actionRequired: false
    },
    {
        id: 'NOT007',
        title: 'Certificate Ready',
        message: 'Your Good Standing Certificate is ready for pickup. Please visit the Diocese office during business hours (8:00 AM - 5:00 PM) with your ID.',
        type: 'application',
        priority: 'high',
        date: '2024-01-14T13:45:00',
        read: true,
        category: 'Certificate Ready',
        actionRequired: true,
        actionText: 'Download Certificate',
        actionUrl: 'my-applications.html'
    },
    {
        id: 'NOT008',
        title: 'Meeting Cancelled',
        message: 'Your scheduled meeting on January 12, 2024, has been cancelled due to an emergency. Please reschedule at your convenience.',
        type: 'meeting',
        priority: 'medium',
        date: '2024-01-11T15:30:00',
        read: true,
        category: 'Meeting Update',
        actionRequired: true,
        actionText: 'Reschedule',
        actionUrl: 'bishop-meeting.html'
    },
    {
        id: 'NOT009',
        title: 'Welcome to Diocese Portal',
        message: 'Welcome to the Diocese of Byumba online portal! You can now apply for certificates, schedule meetings, and stay updated with announcements.',
        type: 'general',
        priority: 'low',
        date: '2024-01-08T12:00:00',
        read: true,
        category: 'Welcome Message',
        actionRequired: false
    },
    {
        id: 'NOT010',
        title: 'Profile Update Required',
        message: 'Please update your profile information to ensure you receive important notifications. Some fields are missing or outdated.',
        type: 'system',
        priority: 'medium',
        date: '2024-01-07T09:30:00',
        read: true,
        category: 'Profile Update',
        actionRequired: true,
        actionText: 'Update Profile',
        actionUrl: 'profile.html'
    }
];

let currentPage = 1;
const itemsPerPage = 8;
let filteredNotifications = [...notificationsData];
let selectedNotifications = new Set();

// Load Notifications Data
function loadNotificationsData() {
    applyFilters();
    renderNotifications();
    updatePagination();
    updateSummary();
}

// Initialize Filters
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const clearFilters = document.getElementById('clearFilters');
    const applyFiltersBtn = document.getElementById('applyFilters');
    
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (typeFilter) typeFilter.addEventListener('change', applyFilters);
    if (priorityFilter) priorityFilter.addEventListener('change', applyFilters);
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            statusFilter.value = 'all';
            typeFilter.value = 'all';
            priorityFilter.value = 'all';
            applyFilters();
        });
    }
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', applyFilters);
    }
}

// Apply Filters
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const priorityFilter = document.getElementById('priorityFilter').value;
    
    filteredNotifications = notificationsData.filter(notification => {
        // Status filter
        if (statusFilter === 'unread' && notification.read) return false;
        if (statusFilter === 'read' && !notification.read) return false;
        
        // Type filter
        if (typeFilter !== 'all' && notification.type !== typeFilter) return false;
        
        // Priority filter
        if (priorityFilter !== 'all' && notification.priority !== priorityFilter) return false;
        
        return true;
    });
    
    currentPage = 1;
    selectedNotifications.clear();
    renderNotifications();
    updatePagination();
    updateSummary();
    updateBulkActions();
}

// Initialize Bulk Actions
function initializeBulkActions() {
    const selectAllBtn = document.getElementById('selectAll');
    const deleteSelectedBtn = document.getElementById('deleteSelected');
    const markSelectedReadBtn = document.getElementById('markSelectedRead');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.notification-checkbox');
            const allSelected = selectedNotifications.size === checkboxes.length;
            
            if (allSelected) {
                // Deselect all
                selectedNotifications.clear();
                checkboxes.forEach(cb => cb.checked = false);
                this.innerHTML = '<i class="fas fa-square"></i> Select All';
            } else {
                // Select all
                checkboxes.forEach(cb => {
                    cb.checked = true;
                    selectedNotifications.add(cb.value);
                });
                this.innerHTML = '<i class="fas fa-check-square"></i> Deselect All';
            }
            
            updateBulkActions();
        });
    }
    
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            if (selectedNotifications.size > 0) {
                deleteSelectedNotifications();
            }
        });
    }
    
    if (markSelectedReadBtn) {
        markSelectedReadBtn.addEventListener('click', function() {
            if (selectedNotifications.size > 0) {
                markSelectedAsRead();
            }
        });
    }
}

// Initialize Page Actions
function initializePageActions() {
    const markAllReadBtn = document.getElementById('markAllRead');
    const notificationSettingsBtn = document.getElementById('notificationSettings');
    
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            markAllNotificationsAsRead();
        });
    }
    
    if (notificationSettingsBtn) {
        notificationSettingsBtn.addEventListener('click', function() {
            window.location.href = 'profile.html#notifications';
        });
    }
}

// Render Notifications
function renderNotifications() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageNotifications = filteredNotifications.slice(startIndex, endIndex);
    const container = document.getElementById('notificationsList');
    
    if (pageNotifications.length === 0) {
        container.innerHTML = `
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications Found</h3>
                <p>No notifications match your current filters.</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = pageNotifications.map(notification => `
        <div class="notification-item-detailed ${notification.read ? 'read' : 'unread'}" data-id="${notification.id}">
            <div class="notification-checkbox-container">
                <input type="checkbox" class="notification-checkbox" value="${notification.id}" 
                       ${selectedNotifications.has(notification.id) ? 'checked' : ''}>
            </div>
            
            <div class="notification-icon ${notification.type} ${notification.priority}">
                <i class="fas ${getNotificationIcon(notification.type)}"></i>
            </div>
            
            <div class="notification-content">
                <div class="notification-header">
                    <h3 class="notification-title">${notification.title}</h3>
                    <div class="notification-meta">
                        <span class="notification-category">${notification.category}</span>
                        <span class="notification-priority priority-${notification.priority}">${notification.priority.toUpperCase()}</span>
                        <span class="notification-time">${formatTimeAgo(notification.date)}</span>
                    </div>
                </div>
                
                <p class="notification-message">${notification.message}</p>
                
                ${notification.actionRequired ? `
                    <div class="notification-action">
                        <a href="${notification.actionUrl}" class="action-link">
                            <i class="fas fa-arrow-right"></i>
                            ${notification.actionText}
                        </a>
                    </div>
                ` : ''}
            </div>
            
            <div class="notification-actions">
                ${!notification.read ? `
                    <button class="action-btn secondary mark-read" data-id="${notification.id}">
                        <i class="fas fa-check"></i>
                    </button>
                ` : ''}
                <button class="action-btn secondary view-details" data-id="${notification.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn danger delete-notification" data-id="${notification.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    // Add event listeners
    addNotificationEventListeners();
}

// Add Event Listeners
function addNotificationEventListeners() {
    // Checkbox listeners
    document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                selectedNotifications.add(this.value);
            } else {
                selectedNotifications.delete(this.value);
            }
            updateBulkActions();
        });
    });
    
    // Mark as read buttons
    document.querySelectorAll('.mark-read').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.getAttribute('data-id');
            markNotificationAsRead(notificationId);
        });
    });
    
    // View details buttons
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.getAttribute('data-id');
            showNotificationDetails(notificationId);
        });
    });
    
    // Delete buttons
    document.querySelectorAll('.delete-notification').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.getAttribute('data-id');
            deleteNotification(notificationId);
        });
    });
    
    // Click to mark as read
    document.querySelectorAll('.notification-item-detailed.unread').forEach(item => {
        item.addEventListener('click', function(e) {
            if (!e.target.closest('.notification-checkbox-container') && 
                !e.target.closest('.notification-actions') &&
                !e.target.closest('.action-link')) {
                const notificationId = this.getAttribute('data-id');
                markNotificationAsRead(notificationId);
            }
        });
    });
}

// Update Bulk Actions
function updateBulkActions() {
    const deleteBtn = document.getElementById('deleteSelected');
    const markReadBtn = document.getElementById('markSelectedRead');
    const selectAllBtn = document.getElementById('selectAll');
    const hasSelected = selectedNotifications.size > 0;
    
    if (deleteBtn) deleteBtn.disabled = !hasSelected;
    if (markReadBtn) markReadBtn.disabled = !hasSelected;
    
    if (selectAllBtn) {
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        const allSelected = selectedNotifications.size === checkboxes.length && checkboxes.length > 0;
        selectAllBtn.innerHTML = allSelected ? 
            '<i class="fas fa-check-square"></i> Deselect All' : 
            '<i class="fas fa-square"></i> Select All';
    }
}

// Initialize Pagination
function initializePagination() {
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderNotifications();
                updatePagination();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderNotifications();
                updatePagination();
            }
        });
    }
}

// Update Pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredNotifications.length / itemsPerPage);
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    const currentPageSpan = document.getElementById('currentPage');
    const totalPagesSpan = document.getElementById('totalPages');
    
    if (prevBtn) prevBtn.disabled = currentPage === 1;
    if (nextBtn) nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    if (currentPageSpan) currentPageSpan.textContent = currentPage;
    if (totalPagesSpan) totalPagesSpan.textContent = Math.max(totalPages, 1);
}

// Update Summary
function updateSummary() {
    const unreadCount = filteredNotifications.filter(n => !n.read).length;
    const todayCount = filteredNotifications.filter(n => {
        const notificationDate = new Date(n.date);
        const today = new Date();
        return notificationDate.toDateString() === today.toDateString();
    }).length;
    const highPriorityCount = filteredNotifications.filter(n => n.priority === 'high').length;
    const totalCount = filteredNotifications.length;
    
    document.querySelector('.summary-card:nth-child(1) h3').textContent = unreadCount;
    document.querySelector('.summary-card:nth-child(2) h3').textContent = todayCount;
    document.querySelector('.summary-card:nth-child(3) h3').textContent = highPriorityCount;
    document.querySelector('.summary-card:nth-child(4) h3').textContent = totalCount;
}

// Initialize Modal
function initializeModal() {
    const modal = document.getElementById('notificationModal');
    const closeBtn = document.getElementById('closeModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }
}

// Action Functions
function markNotificationAsRead(notificationId) {
    const notification = notificationsData.find(n => n.id === notificationId);
    if (notification && !notification.read) {
        notification.read = true;
        renderNotifications();
        updateSummary();
        showNotification('Notification marked as read', 'success');
    }
}

function deleteNotification(notificationId) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const index = notificationsData.findIndex(n => n.id === notificationId);
        if (index !== -1) {
            notificationsData.splice(index, 1);
            selectedNotifications.delete(notificationId);
            applyFilters();
            showNotification('Notification deleted', 'success');
        }
    }
}

function markAllNotificationsAsRead() {
    const unreadNotifications = notificationsData.filter(n => !n.read);
    if (unreadNotifications.length === 0) {
        showNotification('All notifications are already read', 'info');
        return;
    }
    
    unreadNotifications.forEach(notification => {
        notification.read = true;
    });
    
    renderNotifications();
    updateSummary();
    showNotification(`${unreadNotifications.length} notifications marked as read`, 'success');
}

function deleteSelectedNotifications() {
    if (confirm(`Are you sure you want to delete ${selectedNotifications.size} selected notifications?`)) {
        selectedNotifications.forEach(id => {
            const index = notificationsData.findIndex(n => n.id === id);
            if (index !== -1) {
                notificationsData.splice(index, 1);
            }
        });
        
        selectedNotifications.clear();
        applyFilters();
        showNotification('Selected notifications deleted', 'success');
    }
}

function markSelectedAsRead() {
    let markedCount = 0;
    selectedNotifications.forEach(id => {
        const notification = notificationsData.find(n => n.id === id);
        if (notification && !notification.read) {
            notification.read = true;
            markedCount++;
        }
    });
    
    selectedNotifications.clear();
    renderNotifications();
    updateSummary();
    updateBulkActions();
    
    if (markedCount > 0) {
        showNotification(`${markedCount} notifications marked as read`, 'success');
    } else {
        showNotification('Selected notifications are already read', 'info');
    }
}

function showNotificationDetails(notificationId) {
    const notification = notificationsData.find(n => n.id === notificationId);
    if (!notification) return;
    
    const modal = document.getElementById('notificationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const markReadBtn = document.getElementById('markReadBtn');
    const deleteBtn = document.getElementById('deleteNotificationBtn');
    
    modalTitle.textContent = notification.title;
    
    modalBody.innerHTML = `
        <div class="notification-details">
            <div class="detail-header">
                <div class="detail-meta">
                    <span class="detail-category">${notification.category}</span>
                    <span class="detail-priority priority-${notification.priority}">${notification.priority.toUpperCase()}</span>
                    <span class="detail-status ${notification.read ? 'read' : 'unread'}">${notification.read ? 'Read' : 'Unread'}</span>
                </div>
                <div class="detail-date">${formatDateTime(notification.date)}</div>
            </div>
            
            <div class="detail-content">
                <p>${notification.message}</p>
            </div>
            
            ${notification.actionRequired ? `
                <div class="detail-action">
                    <a href="${notification.actionUrl}" class="action-btn primary">
                        <i class="fas fa-arrow-right"></i>
                        ${notification.actionText}
                    </a>
                </div>
            ` : ''}
        </div>
    `;
    
    // Show/hide mark as read button
    markReadBtn.style.display = notification.read ? 'none' : 'inline-flex';
    
    // Add event listeners
    markReadBtn.onclick = () => {
        markNotificationAsRead(notificationId);
        closeModal();
    };
    
    deleteBtn.onclick = () => {
        deleteNotification(notificationId);
        closeModal();
    };
    
    modal.style.display = 'flex';
    
    // Mark as read when viewing details
    if (!notification.read) {
        setTimeout(() => {
            markNotificationAsRead(notificationId);
        }, 1000);
    }
}

function closeModal() {
    const modal = document.getElementById('notificationModal');
    modal.style.display = 'none';
}

// Utility Functions
function getNotificationIcon(type) {
    const iconMap = {
        'application': 'fa-file-alt',
        'meeting': 'fa-calendar',
        'payment': 'fa-credit-card',
        'general': 'fa-info-circle',
        'system': 'fa-cog'
    };
    return iconMap[type] || 'fa-bell';
}

function formatTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) return 'Just now';
    if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)} days ago`;
    
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
