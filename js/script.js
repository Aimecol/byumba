// Diocese of Byumba Platform JavaScript

// Global variables
let certificatesData = [];
// Note: currentLanguage is declared in page-specific JS files (jobs.js, etc.)

// Helper function to get current language safely
function getCurrentLanguage() {
    // Try to get from global currentLanguage variable
    if (typeof currentLanguage !== 'undefined') {
        return currentLanguage;
    }
    // Try to get from language manager
    if (window.languageManager && window.languageManager.currentLanguage) {
        return window.languageManager.currentLanguage;
    }
    // Default to English
    return 'en';
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initializeCertificatesPage();
    initializeSearch();
    initializeApplyButtons();
    initializeMobileMenu();
    initializeHeaderScroll();
    initializeResponsiveNavigation();

    // Load certificates data
    loadCertificates();
});

// Initialize certificates page functionality
function initializeCertificatesPage() {
    // Set up retry button
    const retryBtn = document.getElementById('retryBtn');
    if (retryBtn) {
        retryBtn.addEventListener('click', loadCertificates);
    }

    // Listen for language changes
    document.addEventListener('languageChanged', function(event) {
        // Update currentLanguage if it exists globally
        if (typeof currentLanguage !== 'undefined') {
            currentLanguage = event.detail.language;
        }
        loadCertificates();
    });
}

// Load certificates from API
async function loadCertificates() {
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');
    const certificatesGrid = document.getElementById('certificatesGrid');

    // Show loading state
    if (loadingState) loadingState.style.display = 'block';
    if (errorState) errorState.style.display = 'none';

    try {
        const response = await fetch('api/index.php?endpoint=certificates');
        const data = await response.json();

        if (data.success && data.data.certificates) {
            certificatesData = data.data.certificates;
            renderCertificates(certificatesData);
            populateCategoryFilter();

            // Hide loading state
            if (loadingState) loadingState.style.display = 'none';
        } else {
            throw new Error(data.message || 'Failed to load certificates');
        }
    } catch (error) {
        console.error('Error loading certificates:', error);
        showErrorState();
    }
}

// Show error state
function showErrorState() {
    const loadingState = document.getElementById('loadingState');
    const errorState = document.getElementById('errorState');

    if (loadingState) loadingState.style.display = 'none';
    if (errorState) errorState.style.display = 'block';
}

// Render certificates in the grid
function renderCertificates(certificates) {
    const certificatesGrid = document.getElementById('certificatesGrid');
    if (!certificatesGrid) return;

    // Clear existing certificates (keep loading and error states)
    const existingCards = certificatesGrid.querySelectorAll('.certificate-card');
    existingCards.forEach(card => card.remove());

    certificates.forEach(certificate => {
        const certificateCard = createCertificateCard(certificate);
        certificatesGrid.appendChild(certificateCard);
    });

    // Reinitialize apply buttons for new cards
    initializeApplyButtons();
}

// Create certificate card element
function createCertificateCard(certificate) {
    const card = document.createElement('div');
    card.className = 'certificate-card';
    card.setAttribute('data-category', certificate.type_key);
    card.setAttribute('data-certificate-id', certificate.id);

    card.innerHTML = `
        <div class="card-icon">
            <i class="${certificate.icon}"></i>
        </div>
        <div class="card-content">
            <h3 class="card-title">${certificate.name}</h3>
            <p class="card-description">${certificate.description}</p>
            <div class="card-details">
                <span class="processing-time">
                    <i class="fas fa-clock"></i>
                    ${certificate.processing_days} ${getProcessingDaysText(certificate.processing_days)}
                </span>
                <span class="fee">
                    <i class="fas fa-money-bill"></i>
                    ${certificate.fee}
                </span>
            </div>
            <button class="apply-btn" data-certificate-id="${certificate.id}">
                <i class="fas fa-file-alt"></i>
                <span data-translate="apply_now">Apply Now</span>
            </button>
        </div>
    `;

    return card;
}

// Get processing days text based on language
function getProcessingDaysText(days) {
    const texts = {
        'en': days === 1 ? 'business day' : 'business days',
        'rw': days === 1 ? 'umunsi w\'akazi' : 'iminsi y\'akazi',
        'fr': days === 1 ? 'jour ouvrable' : 'jours ouvrables'
    };
    const lang = getCurrentLanguage();
    return texts[lang] || texts['en'];
}

// Populate category filter dropdown
function populateCategoryFilter() {
    const categoryFilter = document.getElementById('categoryFilter');
    if (!categoryFilter) return;

    // Get unique categories
    const categories = [...new Set(certificatesData.map(cert => cert.type_key))];

    // Clear existing options except "All Categories"
    const allCategoriesOption = categoryFilter.querySelector('option[value=""]');
    categoryFilter.innerHTML = '';
    if (allCategoriesOption) {
        categoryFilter.appendChild(allCategoriesOption);
    }

    // Add category options
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category;
        option.textContent = getCategoryDisplayName(category);
        categoryFilter.appendChild(option);
    });
}

// Get category display name based on current language
function getCategoryDisplayName(categoryKey) {
    const categoryNames = {
        'baptism': {
            'en': 'Baptism',
            'rw': 'Ubwiyunge',
            'fr': 'Baptême'
        },
        'confirmation': {
            'en': 'Confirmation',
            'rw': 'Iyemeza',
            'fr': 'Confirmation'
        },
        'marriage': {
            'en': 'Marriage',
            'rw': 'Ubukwe',
            'fr': 'Mariage'
        },
        'ordination': {
            'en': 'Ordination',
            'rw': 'Ubwiyunge bw\'Abapadiri',
            'fr': 'Ordination'
        },
        'membership': {
            'en': 'Membership',
            'rw': 'Ubwiyunge mu Paruwasi',
            'fr': 'Adhésion'
        },
        'good_standing': {
            'en': 'Good Standing',
            'rw': 'Imyitwarire Myiza',
            'fr': 'Bonne Conduite'
        }
    };

    const lang = getCurrentLanguage();
    return categoryNames[categoryKey]?.[lang] || categoryNames[categoryKey]?.['en'] || categoryKey;
}

// Search and Filter Functionality
function initializeSearch() {
    const searchInput = document.getElementById('certificateSearch');
    const categoryFilter = document.getElementById('categoryFilter');

    if (searchInput) {
        searchInput.addEventListener('input', filterCertificates);
    }

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterCertificates);
    }
}

function filterCertificates() {
    const searchInput = document.getElementById('certificateSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const certificateCards = document.querySelectorAll('.certificate-card');

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const selectedCategory = categoryFilter ? categoryFilter.value : '';

    certificateCards.forEach(card => {
        const title = card.querySelector('.card-title').textContent.toLowerCase();
        const description = card.querySelector('.card-description').textContent.toLowerCase();
        const category = card.getAttribute('data-category');

        const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
        const matchesCategory = !selectedCategory || category === selectedCategory;

        if (matchesSearch && matchesCategory) {
            card.style.display = 'block';
            card.style.animation = 'fadeIn 0.3s ease';
        } else {
            card.style.display = 'none';
        }
    });
}

// Integration with language manager
function updateCertificatesContent(language) {
    // Update currentLanguage if it exists globally
    if (typeof currentLanguage !== 'undefined') {
        currentLanguage = language;
    }

    // Update processing days text for existing cards
    const processingTimeElements = document.querySelectorAll('.processing-time');
    processingTimeElements.forEach(element => {
        const daysMatch = element.textContent.match(/(\d+)/);
        if (daysMatch) {
            const days = parseInt(daysMatch[1]);
            const icon = element.querySelector('i');
            element.innerHTML = `${icon.outerHTML} ${days} ${getProcessingDaysText(days)}`;
        }
    });

    // Update category filter options
    populateCategoryFilter();
}

// Make functions available globally for language manager
window.updateCertificatesContent = updateCertificatesContent;
window.loadCertificates = loadCertificates;



// Apply Button Functionality
function initializeApplyButtons() {
    const applyButtons = document.querySelectorAll('.apply-btn');
    
    applyButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const card = this.closest('.certificate-card');
            const certificateTitle = card.querySelector('.card-title').textContent;

            // Redirect to application page with certificate type
            window.location.href = `application.html?type=${encodeURIComponent(certificateTitle)}`;
        });
    });
}

function showApplicationModal(certificateType) {
    // Get certificate-specific information
    const certificateInfo = getCertificateInfo(certificateType);

    // Create modal overlay
    const modalOverlay = document.createElement('div');
    modalOverlay.className = 'certificate-modal-overlay';
    modalOverlay.innerHTML = `
        <div class="certificate-modal-content">
            <div class="certificate-modal-header">
                <div class="modal-header-content">
                    <div class="certificate-icon">
                        <i class="${certificateInfo.icon}"></i>
                    </div>
                    <div class="header-text">
                        <h3>Apply for ${certificateType}</h3>
                        <p>Complete the form below to submit your application</p>
                    </div>
                </div>
                <button class="certificate-modal-close" aria-label="Close modal">&times;</button>
            </div>

            <div class="certificate-modal-body">
                <!-- Application Progress -->
                <div class="application-progress">
                    <div class="progress-step active" data-step="1">
                        <div class="step-number">1</div>
                        <span>Application Details</span>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-number">2</div>
                        <span>Review & Submit</span>
                    </div>
                </div>

                <!-- Certificate Information -->
                <div class="certificate-info-panel">
                    <div class="info-grid">
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Processing Time</strong>
                                <span>${certificateInfo.processingTime}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-money-bill-wave"></i>
                            <div>
                                <strong>Application Fee</strong>
                                <span>${certificateInfo.fee} (Pay after approval)</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-file-alt"></i>
                            <div>
                                <strong>Required Documents</strong>
                                <span>${certificateInfo.documents.length} items</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Application Form -->
                <form class="certificate-application-form" id="certificateApplicationForm">
                    <!-- Step 1: Application Details -->
                    <div class="form-step active" data-step="1">
                        <h4 class="step-title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="firstName">First Name *</label>
                                <input type="text" id="firstName" name="firstName" required>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Last Name *</label>
                                <input type="text" id="lastName" name="lastName" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="dateOfBirth">Date of Birth *</label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth" required>
                            </div>
                            <div class="form-group">
                                <label for="placeOfBirth">Place of Birth *</label>
                                <input type="text" id="placeOfBirth" name="placeOfBirth" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nationalId">National ID Number *</label>
                                <input type="text" id="nationalId" name="nationalId" required placeholder="1234567890123456">
                            </div>
                            <div class="form-group">
                                <label for="gender">Gender *</label>
                                <select id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required placeholder="+250 788 123 456">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Current Address *</label>
                            <textarea id="address" name="address" rows="3" required placeholder="Province, District, Sector, Cell, Village"></textarea>
                        </div>

                        <!-- Communication Preferences -->
                        <div class="communication-preferences">
                            <h5><i class="fas fa-bell"></i> Payment Code Notification Preferences</h5>
                            <p class="preference-description">Choose how you would like to receive your payment code after application approval:</p>
                            <div class="preference-options">
                                <label class="preference-option">
                                    <input type="checkbox" name="notificationMethod" value="email" checked>
                                    <span class="checkmark"></span>
                                    <div class="option-content">
                                        <i class="fas fa-envelope"></i>
                                        <span>Email Notification</span>
                                        <small>Receive payment code via email</small>
                                    </div>
                                </label>
                                <label class="preference-option">
                                    <input type="checkbox" name="notificationMethod" value="sms">
                                    <span class="checkmark"></span>
                                    <div class="option-content">
                                        <i class="fas fa-sms"></i>
                                        <span>SMS Notification</span>
                                        <small>Receive payment code via SMS</small>
                                    </div>
                                </label>
                                <label class="preference-option">
                                    <input type="checkbox" name="notificationMethod" value="phone">
                                    <span class="checkmark"></span>
                                    <div class="option-content">
                                        <i class="fas fa-phone"></i>
                                        <span>Phone Call</span>
                                        <small>Receive payment code via phone call</small>
                                    </div>
                                </label>
                            </div>
                            <div class="preference-note">
                                <i class="fas fa-info-circle"></i>
                                <span>You can select multiple notification methods. At least one method must be selected.</span>
                            </div>
                        </div>

                        <!-- Certificate-specific fields -->
                        ${getCertificateSpecificFields(certificateType)}

                        <!-- Documents Section -->
                        <div class="documents-section">
                            <h4 class="section-title">
                                <i class="fas fa-file-upload"></i>
                                Required Documents
                            </h4>

                            <div class="documents-info">
                                <p>Please upload the following documents for your ${certificateType} application:</p>
                                <ul class="required-documents-list">
                                    ${certificateInfo.documents.map(doc => `<li><i class="fas fa-check"></i> ${doc}</li>`).join('')}
                                </ul>
                                <div class="upload-progress-summary">
                                    <div class="progress-bar">
                                        <div class="progress-fill" id="uploadProgressFill" style="width: 0%"></div>
                                    </div>
                                    <span class="progress-text" id="uploadProgressText">0 of ${certificateInfo.documents.length} documents uploaded</span>
                                </div>
                            </div>

                            <div class="document-uploads">
                                ${certificateInfo.documents.map((doc, index) => `
                                    <div class="upload-group">
                                        <label for="document${index}">${doc} *</label>
                                        <div class="file-upload-area" data-upload="${index}">
                                            <input type="file" id="document${index}" name="document${index}" accept=".pdf,.jpg,.jpeg,.png" required>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <span>Click to upload or drag and drop</span>
                                                <small>PDF, JPG, PNG (Max 5MB)</small>
                                            </div>
                                            <div class="upload-preview" style="display: none;">
                                                <i class="fas fa-file"></i>
                                                <span class="file-name"></span>
                                                <button type="button" class="remove-file">×</button>
                                            </div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Review -->
                    <div class="form-step" data-step="2">
                        <h4 class="step-title">
                            <i class="fas fa-check-circle"></i>
                            Review & Submit
                        </h4>

                        <div class="application-review">
                            <div class="review-section">
                                <h5>Personal Information</h5>
                                <div class="review-content" id="reviewPersonalInfo">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>

                            <div class="review-section">
                                <h5>Documents</h5>
                                <div class="review-content" id="reviewDocuments">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>

                            <div class="review-section">
                                <h5>Notification Preferences</h5>
                                <div class="review-content" id="reviewNotifications">
                                    <!-- Will be populated dynamically -->
                                </div>
                            </div>

                            <div class="payment-info-section">
                                <div class="payment-info-card">
                                    <div class="payment-info-header">
                                        <i class="fas fa-info-circle"></i>
                                        <h5>Payment Information</h5>
                                    </div>
                                    <div class="payment-info-content">
                                        <p><strong>Application Fee:</strong> ${certificateInfo.fee}</p>
                                        <p><strong>Payment Process:</strong></p>
                                        <ol>
                                            <li>Submit your application with required documents</li>
                                            <li>Wait for application review and approval</li>
                                            <li>Receive payment code via your selected notification method(s)</li>
                                            <li>Make payment using the provided code</li>
                                            <li>Submit payment confirmation to complete the process</li>
                                        </ol>
                                        <div class="payment-note">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>Payment is only required after your application is approved and you receive the payment code.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="terms-agreement">
                            <label class="checkbox-label">
                                <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
                                <span class="checkmark"></span>
                                I agree to the <a href="#terms" target="_blank">Terms and Conditions</a> and confirm that all information provided is accurate *
                            </label>
                        </div>
                    </div>

                    <!-- Form Navigation -->
                    <div class="form-navigation">
                        <button type="button" class="nav-btn prev-btn" id="prevBtn" style="display: none;">
                            <i class="fas fa-arrow-left"></i>
                            Previous
                        </button>
                        <button type="button" class="nav-btn next-btn" id="nextBtn">
                            Next
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button type="submit" class="nav-btn submit-btn" id="submitBtn" style="display: none;">
                            <i class="fas fa-paper-plane"></i>
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(modalOverlay);

    // Initialize the application form
    initializeCertificateApplicationForm(modalOverlay, certificateType);
}

// Certificate-specific form fields
function getCertificateSpecificFields(certificateType) {
    const specificFields = {
        'Baptism Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="baptismDate">Baptism Date (if known)</label>
                    <input type="date" id="baptismDate" name="baptismDate">
                </div>
                <div class="form-group">
                    <label for="baptismParish">Parish of Baptism</label>
                    <input type="text" id="baptismParish" name="baptismParish" placeholder="Enter parish name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="fatherName">Father's Full Name *</label>
                    <input type="text" id="fatherName" name="fatherName" required>
                </div>
                <div class="form-group">
                    <label for="motherName">Mother's Full Name *</label>
                    <input type="text" id="motherName" name="motherName" required>
                </div>
            </div>
        `,
        'Confirmation Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="confirmationDate">Confirmation Date (if known)</label>
                    <input type="date" id="confirmationDate" name="confirmationDate">
                </div>
                <div class="form-group">
                    <label for="confirmationParish">Parish of Confirmation</label>
                    <input type="text" id="confirmationParish" name="confirmationParish" placeholder="Enter parish name">
                </div>
            </div>
            <div class="form-group">
                <label for="sponsorName">Sponsor's Full Name</label>
                <input type="text" id="sponsorName" name="sponsorName" placeholder="Enter sponsor's name">
            </div>
        `,
        'Marriage Certificate': `
            <div class="spouse-section">
                <h5><i class="fas fa-heart"></i> Spouse Information</h5>
                <div class="form-row">
                    <div class="form-group">
                        <label for="spouseFirstName">Spouse's First Name *</label>
                        <input type="text" id="spouseFirstName" name="spouseFirstName" required>
                    </div>
                    <div class="form-group">
                        <label for="spouseLastName">Spouse's Last Name *</label>
                        <input type="text" id="spouseLastName" name="spouseLastName" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="marriageDate">Marriage Date *</label>
                        <input type="date" id="marriageDate" name="marriageDate" required>
                    </div>
                    <div class="form-group">
                        <label for="marriageParish">Parish of Marriage *</label>
                        <input type="text" id="marriageParish" name="marriageParish" required>
                    </div>
                </div>
            </div>
        `,
        'Ordination Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="ordinationDate">Ordination Date *</label>
                    <input type="date" id="ordinationDate" name="ordinationDate" required>
                </div>
                <div class="form-group">
                    <label for="ordinationLevel">Ordination Level *</label>
                    <select id="ordinationLevel" name="ordinationLevel" required>
                        <option value="">Select Level</option>
                        <option value="deacon">Deacon</option>
                        <option value="priest">Priest</option>
                        <option value="bishop">Bishop</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="seminary">Seminary Attended *</label>
                <input type="text" id="seminary" name="seminary" required placeholder="Enter seminary name">
            </div>
        `,
        'Membership Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Current Parish *</label>
                    <select id="currentParish" name="currentParish" required>
                        <option value="">Select your parish</option>
                        <option value="st-mary">St. Mary's Parish</option>
                        <option value="st-joseph">St. Joseph's Parish</option>
                        <option value="st-peter">St. Peter's Parish</option>
                        <option value="holy-family">Holy Family Parish</option>
                        <option value="st-paul">St. Paul's Parish</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="membershipDate">Membership Start Date</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
            </div>
        `,
        'Good Standing Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Current Parish *</label>
                    <select id="currentParish" name="currentParish" required>
                        <option value="">Select your parish</option>
                        <option value="st-mary">St. Mary's Parish</option>
                        <option value="st-joseph">St. Joseph's Parish</option>
                        <option value="st-peter">St. Peter's Parish</option>
                        <option value="holy-family">Holy Family Parish</option>
                        <option value="st-paul">St. Paul's Parish</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="purposeOfCertificate">Purpose of Certificate *</label>
                    <select id="purposeOfCertificate" name="purposeOfCertificate" required>
                        <option value="">Select purpose</option>
                        <option value="employment">Employment</option>
                        <option value="education">Education</option>
                        <option value="travel">Travel/Visa</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        `
    };

    return specificFields[certificateType] || '';
}

// Calculate total fee
function calculateTotalFee(baseFee) {
    const baseAmount = parseInt(baseFee.replace(/[^\d]/g, ''));
    const processingFee = 500;
    const total = baseAmount + processingFee;
    return `RWF ${total.toLocaleString()}`;
}

// Initialize Certificate Application Form
function initializeCertificateApplicationForm(modalOverlay, certificateType) {
    let currentStep = 1;
    const totalSteps = 2;

    // Get form elements
    const form = modalOverlay.querySelector('#certificateApplicationForm');
    const prevBtn = modalOverlay.querySelector('#prevBtn');
    const nextBtn = modalOverlay.querySelector('#nextBtn');
    const submitBtn = modalOverlay.querySelector('#submitBtn');
    const closeBtn = modalOverlay.querySelector('.certificate-modal-close');

    // Close modal functionality
    closeBtn.addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });

    // Navigation button events
    prevBtn.addEventListener('click', () => navigateStep(-1));
    nextBtn.addEventListener('click', () => navigateStep(1));

    // Form submission
    form.addEventListener('submit', handleFormSubmission);

    // File upload handling
    initializeFileUploads(modalOverlay);

    // Communication preferences handling
    initializeCommunicationPreferences(modalOverlay);

    // Initialize upload progress
    updateUploadProgress(modalOverlay);

    // Step navigation
    function navigateStep(direction) {
        if (direction === 1 && !validateCurrentStep()) {
            return;
        }

        const newStep = currentStep + direction;
        if (newStep >= 1 && newStep <= totalSteps) {
            showStep(newStep);
        }
    }

    function showStep(step) {
        // Hide all steps
        modalOverlay.querySelectorAll('.form-step').forEach(stepEl => {
            stepEl.classList.remove('active');
        });

        // Show current step
        const currentStepEl = modalOverlay.querySelector(`[data-step="${step}"]`);
        if (currentStepEl) {
            currentStepEl.classList.add('active');
        }

        // Update progress indicators
        modalOverlay.querySelectorAll('.progress-step').forEach((progressStep, index) => {
            if (index + 1 <= step) {
                progressStep.classList.add('active');
            } else {
                progressStep.classList.remove('active');
            }
        });

        // Update navigation buttons
        prevBtn.style.display = step === 1 ? 'none' : 'inline-flex';
        nextBtn.style.display = step === totalSteps ? 'none' : 'inline-flex';
        submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';

        // Update review section if on last step
        if (step === totalSteps) {
            updateReviewSection();
        }

        currentStep = step;
    }

    function validateCurrentStep() {
        const currentStepEl = modalOverlay.querySelector(`[data-step="${currentStep}"]`);
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });

        // Validate communication preferences and documents on step 1
        if (currentStep === 1) {
            // Check notification methods
            const notificationMethods = modalOverlay.querySelectorAll('input[name="notificationMethod"]:checked');
            if (notificationMethods.length === 0) {
                showNotification('Please select at least one notification method for receiving your payment code', 'error');
                isValid = false;
            }

            // Check document uploads
            const certificateInfo = getCertificateInfo(certificateType);
            const uploadedFiles = modalOverlay.querySelectorAll('.upload-preview:not([style*="display: none"])');
            const requiredDocuments = certificateInfo.documents.length;

            if (uploadedFiles.length < requiredDocuments) {
                showNotification(`Please upload all ${requiredDocuments} required documents before proceeding`, 'error');
                isValid = false;
            }
        }

        if (!isValid && requiredFields.length > 0) {
            showNotification('Please fill in all required fields', 'error');
        }

        return isValid;
    }

    function updateReviewSection() {
        // Update personal info review
        const personalInfo = modalOverlay.querySelector('#reviewPersonalInfo');
        const formData = new FormData(form);

        personalInfo.innerHTML = `
            <div class="review-item">
                <span>Name:</span>
                <span>${formData.get('firstName')} ${formData.get('lastName')}</span>
            </div>
            <div class="review-item">
                <span>Date of Birth:</span>
                <span>${formData.get('dateOfBirth')}</span>
            </div>
            <div class="review-item">
                <span>Email:</span>
                <span>${formData.get('email')}</span>
            </div>
            <div class="review-item">
                <span>Phone:</span>
                <span>${formData.get('phone')}</span>
            </div>
        `;

        // Update documents review
        const documentsReview = modalOverlay.querySelector('#reviewDocuments');
        const uploadedFiles = modalOverlay.querySelectorAll('.upload-preview:not([style*="display: none"])');
        const certificateInfo = getCertificateInfo(certificateType);

        let documentsHtml = '';

        if (uploadedFiles.length > 0) {
            documentsHtml = `
                <div class="review-item">
                    <span>Total Documents:</span>
                    <span>${uploadedFiles.length} of ${certificateInfo.documents.length} uploaded</span>
                </div>
            `;

            // Show individual uploaded documents
            uploadedFiles.forEach((preview) => {
                const fileName = preview.querySelector('.file-name').textContent;
                const uploadGroup = preview.closest('.upload-group');
                const documentLabel = uploadGroup.querySelector('label').textContent.replace(' *', '');

                documentsHtml += `
                    <div class="review-item">
                        <span>${documentLabel}:</span>
                        <span class="document-file">
                            <i class="fas fa-file"></i>
                            ${fileName}
                        </span>
                    </div>
                `;
            });
        } else {
            documentsHtml = `
                <div class="review-item">
                    <span>Documents:</span>
                    <span class="no-documents">No documents uploaded yet</span>
                </div>
            `;
        }

        documentsReview.innerHTML = documentsHtml;

        // Update notification preferences review
        const notificationsReview = modalOverlay.querySelector('#reviewNotifications');
        const selectedNotifications = modalOverlay.querySelectorAll('input[name="notificationMethod"]:checked');
        const notificationMethods = Array.from(selectedNotifications).map(input => {
            const labels = {
                'email': 'Email',
                'sms': 'SMS',
                'phone': 'Phone Call'
            };
            return labels[input.value] || input.value;
        });

        notificationsReview.innerHTML = `
            <div class="review-item">
                <span>Notification Methods:</span>
                <span>${notificationMethods.length > 0 ? notificationMethods.join(', ') : 'None selected'}</span>
            </div>
            <div class="review-item">
                <span>Application Fee:</span>
                <span>${getCertificateInfo(certificateType).fee} (Pay after approval)</span>
            </div>
        `;
    }

    function handleFormSubmission(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return;
        }

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        submitBtn.disabled = true;

        // Simulate form submission
        setTimeout(() => {
            showNotification('Application submitted successfully! You will receive your payment code via your selected notification method(s) after approval.', 'success');
            closeModal();
        }, 2000);
    }

    function closeModal() {
        document.body.removeChild(modalOverlay);
        document.body.style.overflow = '';
    }

    // Initialize modal
    document.body.style.overflow = 'hidden';
    modalOverlay.style.animation = 'fadeIn 0.3s ease';
}

// File Upload Functionality
function initializeFileUploads(modalOverlay) {
    const uploadAreas = modalOverlay.querySelectorAll('.file-upload-area');

    uploadAreas.forEach(uploadArea => {
        const fileInput = uploadArea.querySelector('input[type="file"]');
        const placeholder = uploadArea.querySelector('.upload-placeholder');
        const preview = uploadArea.querySelector('.upload-preview');
        const fileName = preview.querySelector('.file-name');
        const removeBtn = preview.querySelector('.remove-file');

        // Click to upload
        placeholder.addEventListener('click', () => fileInput.click());

        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileUpload(files[0], uploadArea, placeholder, preview, fileName);
            }
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFileUpload(e.target.files[0], uploadArea, placeholder, preview, fileName);
            }
        });

        // Remove file
        removeBtn.addEventListener('click', () => {
            fileInput.value = '';
            placeholder.style.display = 'block';
            preview.style.display = 'none';
            uploadArea.classList.remove('has-file');
            updateUploadProgress(modalOverlay);
        });
    });

    function handleFileUpload(file, uploadArea, placeholder, preview, fileName) {
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('File size must be less than 5MB', 'error');
            return;
        }

        // Validate file type
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Only PDF, JPG, and PNG files are allowed', 'error');
            return;
        }

        // Show preview
        fileName.textContent = file.name;
        placeholder.style.display = 'none';
        preview.style.display = 'flex';
        uploadArea.classList.add('has-file');

        // Update upload progress
        updateUploadProgress(uploadArea.closest('.certificate-modal-overlay'));
    }
}

// Update Upload Progress Function
function updateUploadProgress(modalOverlay) {
    const uploadedFiles = modalOverlay.querySelectorAll('.upload-preview:not([style*="display: none"])');
    const totalDocuments = modalOverlay.querySelectorAll('.upload-group').length;
    const progressFill = modalOverlay.querySelector('#uploadProgressFill');
    const progressText = modalOverlay.querySelector('#uploadProgressText');

    if (progressFill && progressText) {
        const percentage = (uploadedFiles.length / totalDocuments) * 100;
        progressFill.style.width = `${percentage}%`;
        progressText.textContent = `${uploadedFiles.length} of ${totalDocuments} documents uploaded`;

        // Update progress bar color based on completion
        if (uploadedFiles.length === totalDocuments) {
            progressFill.style.background = '#1e753f';
            progressText.style.color = '#1e753f';
        } else {
            progressFill.style.background = '#f2c97e';
            progressText.style.color = '#666';
        }
    }
}

// Communication Preferences Functionality
function initializeCommunicationPreferences(modalOverlay) {
    const preferenceOptions = modalOverlay.querySelectorAll('input[name="notificationMethod"]');

    preferenceOptions.forEach(option => {
        option.addEventListener('change', function() {
            updatePreferenceSelection();
        });
    });

    function updatePreferenceSelection() {
        const selectedOptions = modalOverlay.querySelectorAll('input[name="notificationMethod"]:checked');
        const preferenceNote = modalOverlay.querySelector('.preference-note');

        if (selectedOptions.length === 0) {
            preferenceNote.style.color = '#d14438';
            preferenceNote.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i>
                <span>Please select at least one notification method to receive your payment code.</span>
            `;
        } else {
            preferenceNote.style.color = '#1e753f';
            preferenceNote.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>You will receive your payment code via ${selectedOptions.length} selected method${selectedOptions.length > 1 ? 's' : ''}.</span>
            `;
        }
    }

    // Initialize the preference selection display
    updatePreferenceSelection();
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    // Auto hide after 5 seconds
    setTimeout(() => {
        hideNotification(notification);
    }, 5000);

    // Close button functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        hideNotification(notification);
    });

    function hideNotification(notificationEl) {
        notificationEl.classList.remove('show');
        setTimeout(() => {
            if (notificationEl.parentNode) {
                notificationEl.parentNode.removeChild(notificationEl);
            }
        }, 300);
    }

    function getNotificationIcon(type) {
        const icons = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        return icons[type] || icons.info;
    }
}

// Mobile Menu Functionality
function initializeMobileMenu() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navigation = document.getElementById('mainNavigation');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const navLinks = document.querySelectorAll('.nav-link');

    if (!mobileMenuToggle || !navigation || !mobileMenuOverlay) return;

    // Toggle mobile menu
    mobileMenuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleMobileMenu();
    });

    // Close menu when overlay is clicked
    mobileMenuOverlay.addEventListener('click', function() {
        closeMobileMenu();
    });

    // Close menu when nav link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                closeMobileMenu();
            }
        });
    });

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && navigation.classList.contains('active')) {
            closeMobileMenu();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });

    function toggleMobileMenu() {
        const isActive = navigation.classList.contains('active');

        if (isActive) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    function openMobileMenu() {
        navigation.classList.add('active');
        mobileMenuOverlay.classList.add('active');
        mobileMenuToggle.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Add staggered animation to nav items
        const navItems = navigation.querySelectorAll('.nav-item');
        navItems.forEach((item, index) => {
            item.style.animation = `fadeIn 0.3s ease ${index * 0.1}s both`;
        });
    }

    function closeMobileMenu() {
        navigation.classList.remove('active');
        mobileMenuOverlay.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
        document.body.style.overflow = '';

        // Remove animations
        const navItems = navigation.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.style.animation = '';
        });
    }

    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Header Scroll Effects
function initializeHeaderScroll() {
    const header = document.querySelector('.header');
    let lastScrollTop = 0;
    let scrollTimeout;

    if (!header) return;

    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Add scrolled class for styling
        if (scrollTop > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        // Hide/show header on scroll (mobile only)
        if (window.innerWidth <= 768) {
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                header.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                header.style.transform = 'translateY(0)';
            }
        } else {
            header.style.transform = 'translateY(0)';
        }

        lastScrollTop = scrollTop;

        // Clear timeout and set new one
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            header.style.transform = 'translateY(0)';
        }, 1000);
    });
}

// Responsive Navigation Enhancements
function initializeResponsiveNavigation() {
    // Sync language toggles between desktop and mobile
    const desktopLangButtons = document.querySelectorAll('.header-actions .lang-btn');
    const mobileLangButtons = document.querySelectorAll('.mobile-lang-buttons .lang-btn');

    // Sync desktop to mobile
    desktopLangButtons.forEach(button => {
        button.addEventListener('click', function() {
            const lang = this.getAttribute('data-lang');
            mobileLangButtons.forEach(mobileBtn => {
                mobileBtn.classList.toggle('active', mobileBtn.getAttribute('data-lang') === lang);
            });
        });
    });

    // Sync mobile to desktop
    mobileLangButtons.forEach(button => {
        button.addEventListener('click', function() {
            const lang = this.getAttribute('data-lang');
            desktopLangButtons.forEach(desktopBtn => {
                desktopBtn.classList.toggle('active', desktopBtn.getAttribute('data-lang') === lang);
            });
        });
    });

    // Handle orientation change
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            const navigation = document.getElementById('mainNavigation');
            if (navigation && navigation.classList.contains('active')) {
                // Recalculate menu position after orientation change
                navigation.style.height = window.innerHeight + 'px';
            }
        }, 100);
    });

    // Add touch gestures for mobile menu
    let touchStartX = 0;
    let touchStartY = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    });

    document.addEventListener('touchmove', function(e) {
        if (!touchStartX || !touchStartY) return;

        const touchEndX = e.touches[0].clientX;
        const touchEndY = e.touches[0].clientY;
        const diffX = touchStartX - touchEndX;
        const diffY = touchStartY - touchEndY;

        // Swipe left to close menu (only if menu is open)
        const navigation = document.getElementById('mainNavigation');
        if (navigation && navigation.classList.contains('active')) {
            if (Math.abs(diffX) > Math.abs(diffY) && diffX > 50) {
                closeMobileMenu();
            }
        }
    });

    function closeMobileMenu() {
        const navigation = document.getElementById('mainNavigation');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');

        if (navigation) navigation.classList.remove('active');
        if (mobileMenuOverlay) mobileMenuOverlay.classList.remove('active');
        if (mobileMenuToggle) mobileMenuToggle.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Utility Functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add notification styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1001;
        animation: slideIn 0.3s ease;
        background: ${type === 'success' ? '#1e753f' : type === 'error' ? '#d14438' : '#f2c97e'};
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations
const animationStyles = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); }
        to { transform: translateX(100%); }
    }
`;

const animationStyleSheet = document.createElement('style');
animationStyleSheet.textContent = animationStyles;
document.head.appendChild(animationStyleSheet);
