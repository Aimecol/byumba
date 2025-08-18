// My Applications Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize applications page
    initializeApplicationsPage();
});

function initializeApplicationsPage() {
    // Initialize user menu
    initializeUserMenu();
    
    // Load applications data
    loadApplicationsData();
    
    // Initialize filters
    initializeFilters();
    
    // Initialize view toggle
    initializeViewToggle();
    
    // Initialize pagination
    initializePagination();
    
    // Initialize modal
    initializeModal();
}

// User Menu Functionality (same as other pages)
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

// Sample applications data
const applicationsData = [
    {
        id: 'APP001',
        type: 'Baptism Certificate',
        status: 'completed',
        date: '2024-01-15',
        fee: 'RWF 2,000',
        paymentCode: 'BC2024001',
        submittedDate: '2024-01-10',
        completedDate: '2024-01-15',
        description: 'Certificate of baptism for John Doe',
        documents: ['National ID Copy', 'Birth Certificate', 'Passport Photo']
    },
    {
        id: 'APP002',
        type: 'Marriage Certificate',
        status: 'approved',
        date: '2024-01-12',
        fee: 'RWF 5,000',
        paymentCode: 'MC2024002',
        submittedDate: '2024-01-08',
        approvedDate: '2024-01-12',
        description: 'Marriage certificate for John Doe and Jane Smith',
        documents: ['National ID Copy (Both)', 'Birth Certificates (Both)', 'Passport Photos (Both)', 'Marriage Banns']
    },
    {
        id: 'APP003',
        type: 'Confirmation Certificate',
        status: 'processing',
        date: '2024-01-10',
        fee: 'RWF 2,500',
        submittedDate: '2024-01-05',
        description: 'Certificate of confirmation for John Doe',
        documents: ['National ID Copy', 'Baptism Certificate', 'Passport Photo']
    },
    {
        id: 'APP004',
        type: 'Membership Certificate',
        status: 'pending',
        date: '2024-01-08',
        fee: 'RWF 1,500',
        submittedDate: '2024-01-08',
        description: 'Parish membership certificate',
        documents: ['National ID Copy', 'Passport Photo']
    },
    {
        id: 'APP005',
        type: 'Good Standing Certificate',
        status: 'completed',
        date: '2024-01-05',
        fee: 'RWF 1,500',
        paymentCode: 'GS2024005',
        submittedDate: '2024-01-01',
        completedDate: '2024-01-05',
        description: 'Certificate of good standing in the parish',
        documents: ['National ID Copy', 'Passport Photo']
    },
    {
        id: 'APP006',
        type: 'Ordination Certificate',
        status: 'pending',
        date: '2024-01-03',
        fee: 'RWF 10,000',
        submittedDate: '2024-01-03',
        description: 'Certificate of ordination',
        documents: ['National ID Copy', 'Seminary Certificate', 'Passport Photo', 'Recommendation Letters']
    },
    {
        id: 'APP007',
        type: 'Baptism Certificate',
        status: 'completed',
        date: '2023-12-20',
        fee: 'RWF 2,000',
        paymentCode: 'BC2023007',
        submittedDate: '2023-12-15',
        completedDate: '2023-12-20',
        description: 'Certificate of baptism for family member',
        documents: ['National ID Copy', 'Birth Certificate', 'Passport Photo']
    }
];

let currentPage = 1;
const itemsPerPage = 5;
let filteredApplications = [...applicationsData];
let currentView = 'list';

// Load Applications Data
function loadApplicationsData() {
    applyFilters();
    renderApplications();
    updatePagination();
}

// Initialize Filters
function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const dateFilter = document.getElementById('dateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const applyFilters = document.getElementById('applyFilters');
    
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
    
    if (applyFilters) {
        applyFilters.addEventListener('click', applyFilters);
    }
}

// Apply Filters
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    filteredApplications = applicationsData.filter(app => {
        // Status filter
        if (statusFilter !== 'all' && app.status !== statusFilter) {
            return false;
        }
        
        // Type filter
        if (typeFilter !== 'all') {
            const typeMap = {
                'baptism': 'Baptism Certificate',
                'confirmation': 'Confirmation Certificate',
                'marriage': 'Marriage Certificate',
                'ordination': 'Ordination Certificate',
                'membership': 'Membership Certificate',
                'good-standing': 'Good Standing Certificate'
            };
            if (app.type !== typeMap[typeFilter]) {
                return false;
            }
        }
        
        // Date filter
        if (dateFilter !== 'all') {
            const appDate = new Date(app.date);
            const now = new Date();
            
            switch(dateFilter) {
                case 'week':
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    if (appDate < weekAgo) return false;
                    break;
                case 'month':
                    const monthAgo = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                    if (appDate < monthAgo) return false;
                    break;
                case 'quarter':
                    const quarterAgo = new Date(now.getFullYear(), now.getMonth() - 3, now.getDate());
                    if (appDate < quarterAgo) return false;
                    break;
                case 'year':
                    const yearAgo = new Date(now.getFullYear() - 1, now.getMonth(), now.getDate());
                    if (appDate < yearAgo) return false;
                    break;
            }
        }
        
        return true;
    });
    
    currentPage = 1;
    renderApplications();
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
    const listView = document.getElementById('applicationsListView');
    const gridView = document.getElementById('applicationsGridView');
    
    if (currentView === 'list') {
        listView.style.display = 'block';
        gridView.style.display = 'none';
    } else {
        listView.style.display = 'none';
        gridView.style.display = 'grid';
    }
    
    renderApplications();
}

// Render Applications
function renderApplications() {
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const pageApplications = filteredApplications.slice(startIndex, endIndex);
    
    if (currentView === 'list') {
        renderListView(pageApplications);
    } else {
        renderGridView(pageApplications);
    }
}

// Render List View
function renderListView(applications) {
    const container = document.getElementById('applicationsListView');
    
    if (applications.length === 0) {
        container.innerHTML = `
            <div class="no-applications">
                <i class="fas fa-file-alt"></i>
                <h3>No Applications Found</h3>
                <p>No applications match your current filters.</p>
                <a href="application.html" class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    Create New Application
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = applications.map(app => `
        <div class="application-item-detailed">
            <div class="application-header">
                <div class="application-title-section">
                    <h3 class="application-title">${app.type}</h3>
                    <span class="application-id">ID: ${app.id}</span>
                </div>
                <span class="application-status status-${app.status}">${getStatusText(app.status)}</span>
            </div>
            
            <div class="application-body">
                <div class="application-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>Submitted: ${formatDate(app.submittedDate)}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Fee: ${app.fee}</span>
                    </div>
                    ${app.paymentCode ? `
                        <div class="info-item">
                            <i class="fas fa-code"></i>
                            <span>Payment Code: ${app.paymentCode}</span>
                        </div>
                    ` : ''}
                    <div class="info-item">
                        <i class="fas fa-file-alt"></i>
                        <span>${app.documents.length} documents uploaded</span>
                    </div>
                </div>
                
                <div class="application-description">
                    <p>${app.description}</p>
                </div>
            </div>
            
            <div class="application-actions">
                <button class="action-btn secondary view-details" data-id="${app.id}">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
                ${app.status === 'approved' ? `
                    <button class="action-btn primary pay-application" data-id="${app.id}">
                        <i class="fas fa-credit-card"></i>
                        Make Payment
                    </button>
                ` : ''}
                ${app.status === 'completed' ? `
                    <button class="action-btn primary download-certificate" data-id="${app.id}">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    // Add event listeners
    addApplicationEventListeners();
}

// Render Grid View
function renderGridView(applications) {
    const container = document.getElementById('applicationsGridView');
    
    if (applications.length === 0) {
        container.innerHTML = `
            <div class="no-applications">
                <i class="fas fa-file-alt"></i>
                <h3>No Applications Found</h3>
                <p>No applications match your current filters.</p>
                <a href="application.html" class="action-btn primary">
                    <i class="fas fa-plus"></i>
                    Create New Application
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = applications.map(app => `
        <div class="application-card">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas ${getApplicationIcon(app.type)}"></i>
                </div>
                <span class="application-status status-${app.status}">${getStatusText(app.status)}</span>
            </div>
            
            <div class="card-body">
                <h3 class="card-title">${app.type}</h3>
                <p class="card-id">ID: ${app.id}</p>
                <p class="card-description">${app.description}</p>
                
                <div class="card-info">
                    <div class="info-row">
                        <span>Submitted:</span>
                        <span>${formatDate(app.submittedDate)}</span>
                    </div>
                    <div class="info-row">
                        <span>Fee:</span>
                        <span>${app.fee}</span>
                    </div>
                </div>
            </div>
            
            <div class="card-actions">
                <button class="action-btn secondary view-details" data-id="${app.id}">
                    <i class="fas fa-eye"></i>
                    Details
                </button>
                ${app.status === 'approved' ? `
                    <button class="action-btn primary pay-application" data-id="${app.id}">
                        <i class="fas fa-credit-card"></i>
                        Pay
                    </button>
                ` : ''}
                ${app.status === 'completed' ? `
                    <button class="action-btn primary download-certificate" data-id="${app.id}">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    // Add event listeners
    addApplicationEventListeners();
}

// Add Event Listeners to Application Items
function addApplicationEventListeners() {
    // View details buttons
    document.querySelectorAll('.view-details').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.getAttribute('data-id');
            showApplicationDetails(appId);
        });
    });
    
    // Pay application buttons
    document.querySelectorAll('.pay-application').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.getAttribute('data-id');
            payApplication(appId);
        });
    });
    
    // Download certificate buttons
    document.querySelectorAll('.download-certificate').forEach(btn => {
        btn.addEventListener('click', function() {
            const appId = this.getAttribute('data-id');
            downloadCertificate(appId);
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
                renderApplications();
                updatePagination();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                renderApplications();
                updatePagination();
            }
        });
    }
}

// Update Pagination
function updatePagination() {
    const totalPages = Math.ceil(filteredApplications.length / itemsPerPage);
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
        pending: filteredApplications.filter(app => app.status === 'pending').length,
        processing: filteredApplications.filter(app => app.status === 'processing').length,
        approved: filteredApplications.filter(app => app.status === 'approved').length,
        completed: filteredApplications.filter(app => app.status === 'completed').length
    };
    
    document.querySelector('.summary-card:nth-child(1) h3').textContent = summary.pending;
    document.querySelector('.summary-card:nth-child(2) h3').textContent = summary.processing;
    document.querySelector('.summary-card:nth-child(3) h3').textContent = summary.approved;
    document.querySelector('.summary-card:nth-child(4) h3').textContent = summary.completed;
}

// Initialize Modal
function initializeModal() {
    const modal = document.getElementById('applicationModal');
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

// Show Application Details
function showApplicationDetails(appId) {
    const application = applicationsData.find(app => app.id === appId);
    if (!application) return;
    
    const modal = document.getElementById('applicationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    const downloadBtn = document.getElementById('downloadBtn');
    const payBtn = document.getElementById('payBtn');
    
    modalTitle.textContent = `${application.type} - ${application.id}`;
    
    modalBody.innerHTML = `
        <div class="application-details">
            <div class="detail-section">
                <h4>Application Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Application ID:</label>
                        <span>${application.id}</span>
                    </div>
                    <div class="detail-item">
                        <label>Type:</label>
                        <span>${application.type}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status:</label>
                        <span class="status-badge status-${application.status}">${getStatusText(application.status)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Fee:</label>
                        <span>${application.fee}</span>
                    </div>
                    <div class="detail-item">
                        <label>Submitted Date:</label>
                        <span>${formatDate(application.submittedDate)}</span>
                    </div>
                    ${application.approvedDate ? `
                        <div class="detail-item">
                            <label>Approved Date:</label>
                            <span>${formatDate(application.approvedDate)}</span>
                        </div>
                    ` : ''}
                    ${application.completedDate ? `
                        <div class="detail-item">
                            <label>Completed Date:</label>
                            <span>${formatDate(application.completedDate)}</span>
                        </div>
                    ` : ''}
                    ${application.paymentCode ? `
                        <div class="detail-item">
                            <label>Payment Code:</label>
                            <span class="payment-code">${application.paymentCode}</span>
                        </div>
                    ` : ''}
                </div>
            </div>
            
            <div class="detail-section">
                <h4>Description</h4>
                <p>${application.description}</p>
            </div>
            
            <div class="detail-section">
                <h4>Uploaded Documents</h4>
                <ul class="documents-list">
                    ${application.documents.map(doc => `
                        <li>
                            <i class="fas fa-file"></i>
                            ${doc}
                        </li>
                    `).join('')}
                </ul>
            </div>
        </div>
    `;
    
    // Show/hide action buttons
    downloadBtn.style.display = application.status === 'completed' ? 'inline-flex' : 'none';
    payBtn.style.display = application.status === 'approved' ? 'inline-flex' : 'none';
    
    // Add event listeners to action buttons
    downloadBtn.onclick = () => downloadCertificate(appId);
    payBtn.onclick = () => payApplication(appId);
    
    modal.style.display = 'flex';
}

// Close Modal
function closeModal() {
    const modal = document.getElementById('applicationModal');
    modal.style.display = 'none';
}

// Action Functions
function payApplication(appId) {
    showNotification(`Redirecting to payment for application ${appId}`, 'info');
    closeModal();
    // In real app, this would redirect to payment page
}

function downloadCertificate(appId) {
    showNotification(`Downloading certificate for application ${appId}`, 'success');
    closeModal();
    // In real app, this would download the certificate
}

// Utility Functions
function getStatusText(status) {
    const statusMap = {
        'pending': 'Pending',
        'processing': 'Processing',
        'approved': 'Approved',
        'completed': 'Completed',
        'rejected': 'Rejected'
    };
    return statusMap[status] || status;
}

function getApplicationIcon(type) {
    const iconMap = {
        'Baptism Certificate': 'fa-cross',
        'Confirmation Certificate': 'fa-hands-praying',
        'Marriage Certificate': 'fa-ring',
        'Ordination Certificate': 'fa-church',
        'Membership Certificate': 'fa-users',
        'Good Standing Certificate': 'fa-certificate'
    };
    return iconMap[type] || 'fa-file-alt';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
