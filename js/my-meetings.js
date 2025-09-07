// My Meetings Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize meetings page
    initializeMeetingsPage();
});

function initializeMeetingsPage() {
    // Initialize user menu
    initializeUserMenu();
    
    // Load meetings data
    loadMeetingsData();
    
    // Initialize filters
    initializeFilters();
    
    // Initialize view toggle
    initializeViewToggle();
    
    // Initialize calendar
    initializeCalendar();
    
    // Initialize pagination
    initializePagination();
    
    // Initialize modal
    initializeModal();
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

// Sample meetings data
const meetingsData = [
    {
        id: 'MTG001',
        title: 'Marriage Counseling Session',
        type: 'counseling',
        status: 'confirmed',
        date: '2024-01-20',
        time: '10:00 AM',
        duration: '60 minutes',
        location: 'Bishop\'s Office',
        description: 'Pre-marriage counseling session for John Doe and Jane Smith',
        notes: 'Please bring required documents and arrive 15 minutes early',
        createdDate: '2024-01-15'
    },
    {
        id: 'MTG002',
        title: 'Baptism Preparation',
        type: 'preparation',
        status: 'pending',
        date: '2024-01-22',
        time: '2:00 PM',
        duration: '45 minutes',
        location: 'Parish Hall',
        description: 'Baptism preparation session for infant baptism',
        notes: 'Bring baptism application form and godparents information',
        createdDate: '2024-01-16'
    },
    {
        id: 'MTG003',
        title: 'General Consultation',
        type: 'consultation',
        status: 'confirmed',
        date: '2024-01-25',
        time: '11:00 AM',
        duration: '30 minutes',
        location: 'Bishop\'s Office',
        description: 'General spiritual consultation and guidance',
        notes: 'Personal consultation regarding spiritual matters',
        createdDate: '2024-01-18'
    },
    {
        id: 'MTG004',
        title: 'Confession Session',
        type: 'confession',
        status: 'completed',
        date: '2024-01-10',
        time: '4:00 PM',
        duration: '30 minutes',
        location: 'Confessional',
        description: 'Private confession session',
        notes: 'Completed successfully',
        createdDate: '2024-01-08',
        completedDate: '2024-01-10'
    },
    {
        id: 'MTG005',
        title: 'Spiritual Guidance',
        type: 'spiritual',
        status: 'completed',
        date: '2024-01-05',
        time: '9:00 AM',
        duration: '45 minutes',
        location: 'Bishop\'s Office',
        description: 'Spiritual guidance and prayer session',
        notes: 'Discussed spiritual growth and prayer life',
        createdDate: '2024-01-02',
        completedDate: '2024-01-05'
    },
    {
        id: 'MTG006',
        title: 'Marriage Counseling Follow-up',
        type: 'counseling',
        status: 'cancelled',
        date: '2024-01-12',
        time: '3:00 PM',
        duration: '60 minutes',
        location: 'Bishop\'s Office',
        description: 'Follow-up marriage counseling session',
        notes: 'Cancelled due to scheduling conflict',
        createdDate: '2024-01-08',
        cancelledDate: '2024-01-11'
    }
];

let currentPage = 1;
let totalPages = 1;
const itemsPerPage = 5;
let filteredMeetings = [...meetingsData];
let currentView = 'list';
let currentMonth = new Date();

// Current filters state
let currentFilters = {
    status: 'all',
    type: 'all',
    date: 'all'
};

// Load Meetings Data from API
async function loadMeetingsData() {
    try {
        const queryParams = new URLSearchParams({
            status: currentFilters.status,
            type: currentFilters.type,
            date: currentFilters.date,
            page: currentPage,
            limit: itemsPerPage
        });

        const response = await fetch(`api/index.php?endpoint=meetings&${queryParams}`);
        const data = await response.json();

        if (data.success) {
            // Update global data
            meetingsData.length = 0;
            meetingsData.push(...data.data.meetings);
            totalPages = data.data.pagination.total_pages;

            // Update summary if available
            if (data.data.summary) {
                updateMeetingsSummary(data.data.summary);
            }

            // Render meetings
            filteredMeetings = [...meetingsData];
            renderMeetings();
            updatePagination();
            renderCalendar();
        } else {
            console.error('Failed to load meetings:', data.message);
            showNotification('Failed to load meetings', 'error');
            // Fallback to existing static data
            loadStaticMeetingsData();
        }
    } catch (error) {
        console.error('Error loading meetings:', error);
        showNotification('Error loading meetings', 'error');
        // Fallback to existing static data
        loadStaticMeetingsData();
    }
}

// Load Static Meetings Data (fallback)
function loadStaticMeetingsData() {
    applyFilters();
    renderMeetings();
    updatePagination();
    renderCalendar();
}

// Update Meetings Summary
function updateMeetingsSummary(summary) {
    // Update summary cards if they exist
    const summaryCards = document.querySelectorAll('.summary-card');

    if (summaryCards.length >= 4) {
        summaryCards[0].querySelector('h3').textContent = summary.pending || 0;
        summaryCards[1].querySelector('h3').textContent = summary.confirmed || 0;
        summaryCards[2].querySelector('h3').textContent = summary.completed || 0;
        summaryCards[3].querySelector('h3').textContent = summary.cancelled || 0;
    }
}

// Initialize Filters
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const applyFiltersBtn = document.getElementById('applyFilters');
    
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (typeFilter) typeFilter.addEventListener('change', applyFilters);
    if (dateFilter) dateFilter.addEventListener('change', applyFilters);
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            statusFilter.value = 'all';
            typeFilter.value = 'all';
            dateFilter.value = 'all';
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
    const dateFilter = document.getElementById('dateFilter').value;
    
    filteredMeetings = meetingsData.filter(meeting => {
        // Status filter
        if (statusFilter !== 'all' && meeting.status !== statusFilter) {
            return false;
        }
        
        // Type filter
        if (typeFilter !== 'all' && meeting.type !== typeFilter) {
            return false;
        }
        
        // Date filter
        if (dateFilter !== 'all') {
            const meetingDate = new Date(meeting.date);
            const now = new Date();
            
            switch(dateFilter) {
                case 'upcoming':
                    if (meetingDate <= now) return false;
                    break;
                case 'week':
                    const weekFromNow = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
                    if (meetingDate < now || meetingDate > weekFromNow) return false;
                    break;
                case 'month':
                    const monthFromNow = new Date(now.getFullYear(), now.getMonth() + 1, now.getDate());
                    if (meetingDate < now || meetingDate > monthFromNow) return false;
                    break;
                case 'past':
                    if (meetingDate >= now) return false;
                    break;
            }
        }
        
        return true;
    });
    
    currentPage = 1;
    renderMeetings();
    updatePagination();
    updateSummary();
}

// Initialize View Toggle
function initializeViewToggle() {
    const viewBtns = document.querySelectorAll('.view-btn');
    
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentView = this.getAttribute('data-view');
            toggleView();
        });
    });
}

// Toggle View
function toggleView() {
    const listView = document.getElementById('meetingsListView');
    const timelineView = document.getElementById('meetingsTimelineView');
    
    if (currentView === 'list') {
        listView.style.display = 'block';
        timelineView.style.display = 'none';
    } else {
        listView.style.display = 'none';
        timelineView.style.display = 'block';
    }
    
    renderMeetings();
}

// Render Meetings
function renderMeetings() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageMeetings = filteredMeetings.slice(startIndex, endIndex);
    
    if (currentView === 'list') {
        renderListView(pageMeetings);
    } else {
        renderTimelineView(pageMeetings);
    }
}

// Render List View
function renderListView(meetings) {
    const container = document.getElementById('meetingsListView');
    
    if (meetings.length === 0) {
        container.innerHTML = `
            <div class="no-meetings">
                <i class="fas fa-calendar-alt"></i>
                <h3>No Meetings Found</h3>
                <p>No meetings match your current filters.</p>
                <a href="bishop-meeting.html" class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    Schedule New Meeting
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = meetings.map(meeting => `
        <div class="meeting-item-detailed">
            <div class="meeting-header">
                <div class="meeting-title-section">
                    <h3 class="meeting-title">${meeting.title}</h3>
                    <span class="meeting-id">ID: ${meeting.id}</span>
                </div>
                <span class="meeting-status status-${meeting.status}">${getStatusText(meeting.status)}</span>
            </div>
            
            <div class="meeting-body">
                <div class="meeting-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>${formatDate(meeting.date)}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>${meeting.time}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-hourglass-half"></i>
                        <span>${meeting.duration}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${meeting.location}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <span>${getMeetingTypeText(meeting.type)}</span>
                    </div>
                </div>
                
                <div class="meeting-description">
                    <p>${meeting.description}</p>
                    ${meeting.notes ? `<p class="meeting-notes"><strong>Notes:</strong> ${meeting.notes}</p>` : ''}
                </div>
            </div>
            
            <div class="meeting-actions">
                <button class="action-btn secondary view-details" data-id="${meeting.id}">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                ${meeting.status === 'pending' || meeting.status === 'confirmed' ? `
                    <button class="action-btn primary edit-meeting" data-id="${meeting.id}">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    <button class="action-btn danger cancel-meeting" data-id="${meeting.id}">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    // Add event listeners
    addMeetingEventListeners();
}

// Render Timeline View
function renderTimelineView(meetings) {
    const container = document.getElementById('meetingsTimelineView');
    
    if (meetings.length === 0) {
        container.innerHTML = `
            <div class="no-meetings">
                <i class="fas fa-calendar-alt"></i>
                <h3>No Meetings Found</h3>
                <p>No meetings match your current filters.</p>
                <a href="bishop-meeting.html" class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    Schedule New Meeting
                </a>
            </div>
        `;
        return;
    }
    
    // Sort meetings by date for timeline
    const sortedMeetings = [...meetings].sort((a, b) => new Date(a.date) - new Date(b.date));
    
    container.innerHTML = `
        <div class="meetings-timeline">
            ${sortedMeetings.map(meeting => `
                <div class="timeline-item" data-status="${meeting.status}">
                    <div class="timeline-marker ${meeting.status}">
                        <i class="fas ${getMeetingIcon(meeting.type)}"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h4 class="timeline-title">${meeting.title}</h4>
                            <span class="timeline-date">${formatDate(meeting.date)} at ${meeting.time}</span>
                        </div>
                        <div class="timeline-body">
                            <p class="timeline-description">${meeting.description}</p>
                            <div class="timeline-details">
                                <span><i class="fas fa-map-marker-alt"></i> ${meeting.location}</span>
                                <span><i class="fas fa-hourglass-half"></i> ${meeting.duration}</span>
                                <span class="meeting-status status-${meeting.status}">${getStatusText(meeting.status)}</span>
                            </div>
                        </div>
                        <div class="timeline-actions">
                            <button class="action-btn secondary view-details" data-id="${meeting.id}">
                                <i class="fas fa-eye"></i>
                                Details
                            </button>
                            ${meeting.status === 'pending' || meeting.status === 'confirmed' ? `
                                <button class="action-btn primary edit-meeting" data-id="${meeting.id}">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    
    // Add event listeners
    addMeetingEventListeners();
}

// Add Event Listeners to Meeting Items
function addMeetingEventListeners() {
    // View details buttons
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const meetingId = this.getAttribute('data-id');
            showMeetingDetails(meetingId);
        });
    });
    
    // Edit meeting buttons
    document.querySelectorAll('.edit-meeting').forEach(btn => {
        btn.addEventListener('click', function() {
            const meetingId = this.getAttribute('data-id');
            editMeeting(meetingId);
        });
    });
    
    // Cancel meeting buttons
    document.querySelectorAll('.cancel-meeting').forEach(btn => {
        btn.addEventListener('click', function() {
            const meetingId = this.getAttribute('data-id');
            cancelMeeting(meetingId);
        });
    });
}

// Initialize Calendar
function initializeCalendar() {
    const prevBtn = document.getElementById('prevMonth');
    const nextBtn = document.getElementById('nextMonth');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentMonth.setMonth(currentMonth.getMonth() - 1);
            renderCalendar();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentMonth.setMonth(currentMonth.getMonth() + 1);
            renderCalendar();
        });
    }
    
    renderCalendar();
}

// Render Calendar
function renderCalendar() {
    const monthElement = document.getElementById('currentMonth');
    const calendarGrid = document.getElementById('calendarGrid');
    
    if (!monthElement || !calendarGrid) return;
    
    // Update month display
    monthElement.textContent = currentMonth.toLocaleDateString('en-US', { 
        month: 'long', 
        year: 'numeric' 
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth(), 1);
    const lastDay = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    // Generate calendar HTML
    let calendarHTML = `
        <div class="calendar-weekdays">
            <div class="weekday">Sun</div>
            <div class="weekday">Mon</div>
            <div class="weekday">Tue</div>
            <div class="weekday">Wed</div>
            <div class="weekday">Thu</div>
            <div class="weekday">Fri</div>
            <div class="weekday">Sat</div>
        </div>
        <div class="calendar-days">
    `;
    
    // Generate 6 weeks of calendar
    for (let week = 0; week < 6; week++) {
        for (let day = 0; day < 7; day++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + (week * 7) + day);
            
            const isCurrentMonth = currentDate.getMonth() === currentMonth.getMonth();
            const isToday = currentDate.toDateString() === new Date().toDateString();
            
            // Check if there are meetings on this date
            const dayMeetings = meetingsData.filter(meeting => {
                const meetingDate = new Date(meeting.date);
                return meetingDate.toDateString() === currentDate.toDateString();
            });
            
            calendarHTML += `
                <div class="calendar-day ${isCurrentMonth ? 'current-month' : 'other-month'} ${isToday ? 'today' : ''}" 
                     data-date="${currentDate.toISOString().split('T')[0]}">
                    <span class="day-number">${currentDate.getDate()}</span>
                    ${dayMeetings.length > 0 ? `
                        <div class="day-meetings">
                            ${dayMeetings.slice(0, 2).map(meeting => `
                                <div class="meeting-dot ${meeting.status}" title="${meeting.title} - ${meeting.time}"></div>
                            `).join('')}
                            ${dayMeetings.length > 2 ? `<div class="meeting-more">+${dayMeetings.length - 2}</div>` : ''}
                        </div>
                    ` : ''}
                </div>
            `;
        }
    }
    
    calendarHTML += '</div>';
    calendarGrid.innerHTML = calendarHTML;
    
    // Add click listeners to calendar days
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.addEventListener('click', function() {
            const date = this.getAttribute('data-date');
            filterMeetingsByDate(date);
        });
    });
}

// Initialize Pagination
function initializePagination() {
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderMeetings();
                updatePagination();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredMeetings.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderMeetings();
                updatePagination();
            }
        });
    }
}

// Update Pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredMeetings.length / itemsPerPage);
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
    const summary = {
        pending: filteredMeetings.filter(meeting => meeting.status === 'pending').length,
        confirmed: filteredMeetings.filter(meeting => meeting.status === 'confirmed').length,
        upcoming: filteredMeetings.filter(meeting => {
            const meetingDate = new Date(meeting.date);
            return meetingDate > new Date() && (meeting.status === 'confirmed' || meeting.status === 'pending');
        }).length,
        completed: filteredMeetings.filter(meeting => meeting.status === 'completed').length
    };
    
    document.querySelector('.summary-card:nth-child(1) h3').textContent = summary.pending;
    document.querySelector('.summary-card:nth-child(2) h3').textContent = summary.confirmed;
    document.querySelector('.summary-card:nth-child(3) h3').textContent = summary.upcoming;
    document.querySelector('.summary-card:nth-child(4) h3').textContent = summary.completed;
}

// Initialize Modal
function initializeModal() {
    const modal = document.getElementById('meetingModal');
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

// Utility Functions
function getStatusText(status) {
    const statusMap = {
        'pending': 'Pending',
        'confirmed': 'Confirmed',
        'completed': 'Completed',
        'cancelled': 'Cancelled'
    };
    return statusMap[status] || status;
}

function getMeetingTypeText(type) {
    const typeMap = {
        'counseling': 'Marriage Counseling',
        'preparation': 'Baptism Preparation',
        'consultation': 'General Consultation',
        'confession': 'Confession',
        'spiritual': 'Spiritual Guidance',
        'other': 'Other'
    };
    return typeMap[type] || type;
}

function getMeetingIcon(type) {
    const iconMap = {
        'counseling': 'fa-heart',
        'preparation': 'fa-baby',
        'consultation': 'fa-comments',
        'confession': 'fa-praying-hands',
        'spiritual': 'fa-cross',
        'other': 'fa-calendar'
    };
    return iconMap[type] || 'fa-calendar';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Action Functions
function showMeetingDetails(meetingId) {
    const meeting = meetingsData.find(m => m.id === meetingId);
    if (!meeting) return;
    
    const modal = document.getElementById('meetingModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const editBtn = document.getElementById('editMeetingBtn');
    const cancelBtn = document.getElementById('cancelMeetingBtn');
    
    modalTitle.textContent = `${meeting.title} - ${meeting.id}`;
    
    modalBody.innerHTML = `
        <div class="meeting-details">
            <div class="detail-section">
                <h4>Meeting Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Meeting ID:</label>
                        <span>${meeting.id}</span>
                    </div>
                    <div class="detail-item">
                        <label>Title:</label>
                        <span>${meeting.title}</span>
                    </div>
                    <div class="detail-item">
                        <label>Type:</label>
                        <span>${getMeetingTypeText(meeting.type)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status:</label>
                        <span class="status-badge status-${meeting.status}">${getStatusText(meeting.status)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Date:</label>
                        <span>${formatDate(meeting.date)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Time:</label>
                        <span>${meeting.time}</span>
                    </div>
                    <div class="detail-item">
                        <label>Duration:</label>
                        <span>${meeting.duration}</span>
                    </div>
                    <div class="detail-item">
                        <label>Location:</label>
                        <span>${meeting.location}</span>
                    </div>
                    <div class="detail-item">
                        <label>Created:</label>
                        <span>${formatDate(meeting.createdDate)}</span>
                    </div>
                    ${meeting.completedDate ? `
                        <div class="detail-item">
                            <label>Completed:</label>
                            <span>${formatDate(meeting.completedDate)}</span>
                        </div>
                    ` : ''}
                    ${meeting.cancelledDate ? `
                        <div class="detail-item">
                            <label>Cancelled:</label>
                            <span>${formatDate(meeting.cancelledDate)}</span>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            <div class="detail-section">
                <h4>Description</h4>
                <p>${meeting.description}</p>
            </div>
            
            ${meeting.notes ? `
                <div class="detail-section">
                    <h4>Notes</h4>
                    <p>${meeting.notes}</p>
                </div>
            ` : ''}
        </div>
    `;
    
    // Show/hide action buttons
    const canEdit = meeting.status === 'pending' || meeting.status === 'confirmed';
    editBtn.style.display = canEdit ? 'inline-flex' : 'none';
    cancelBtn.style.display = canEdit ? 'inline-flex' : 'none';
    
    // Add event listeners to action buttons
    editBtn.onclick = () => editMeeting(meetingId);
    cancelBtn.onclick = () => cancelMeeting(meetingId);
    
    modal.style.display = 'flex';
}

function editMeeting(meetingId) {
    showNotification(`Redirecting to edit meeting ${meetingId}`, 'info');
    closeModal();
    // In real app, this would redirect to edit meeting page
}

function cancelMeeting(meetingId) {
    if (confirm('Are you sure you want to cancel this meeting?')) {
        showNotification(`Meeting ${meetingId} cancelled successfully`, 'success');
        closeModal();
        // In real app, this would make API call to cancel meeting
        setTimeout(() => {
            loadMeetingsData(); // Refresh the list
        }, 1000);
    }
}

function filterMeetingsByDate(date) {
    const dayMeetings = meetingsData.filter(meeting => meeting.date === date);
    if (dayMeetings.length > 0) {
        showNotification(`Found ${dayMeetings.length} meeting(s) on ${formatDate(date)}`, 'info');
        // Could implement a day view modal here
    } else {
        showNotification(`No meetings scheduled for ${formatDate(date)}`, 'info');
    }
}

function closeModal() {
    const modal = document.getElementById('meetingModal');
    modal.style.display = 'none';
}
