// Certificate Application Page JavaScript

// Certificate Information Data
function getCertificateInfo(certificateType) {
    const certificateData = {
        'Baptism Certificate': {
            icon: 'fas fa-cross',
            processingTime: '3-5 business days',
            fee: 'RWF 2,000',
            documents: [
                'National ID or Passport copy',
                'Birth Certificate',
                'Parent\'s Marriage Certificate (if applicable)',
                'Baptism Request Letter'
            ]
        },
        'Confirmation Certificate': {
            icon: 'fas fa-hands-praying',
            processingTime: '3-5 business days',
            fee: 'RWF 2,500',
            documents: [
                'National ID or Passport copy',
                'Baptism Certificate',
                'Confirmation Request Letter',
                'Sponsor Information'
            ]
        },
        'Marriage Certificate': {
            icon: 'fas fa-ring',
            processingTime: '5-7 business days',
            fee: 'RWF 5,000',
            documents: [
                'National ID or Passport copy (Both spouses)',
                'Birth Certificate (Both spouses)',
                'Baptism Certificate (Both spouses)',
                'Marriage Request Letter',
                'Witness Information'
            ]
        },
        'Ordination Certificate': {
            icon: 'fas fa-church',
            processingTime: '7-10 business days',
            fee: 'RWF 10,000',
            documents: [
                'National ID or Passport copy',
                'Seminary Graduation Certificate',
                'Baptism Certificate',
                'Confirmation Certificate',
                'Ordination Request Letter'
            ]
        },
        'Membership Certificate': {
            icon: 'fas fa-users',
            processingTime: '2-3 business days',
            fee: 'RWF 1,500',
            documents: [
                'National ID or Passport copy',
                'Baptism Certificate',
                'Parish Registration Form',
                'Membership Request Letter'
            ]
        },
        'Good Standing Certificate': {
            icon: 'fas fa-certificate',
            processingTime: '2-3 business days',
            fee: 'RWF 1,500',
            documents: [
                'National ID or Passport copy',
                'Baptism Certificate',
                'Good Standing Request Letter',
                'Purpose Statement'
            ]
        }
    };

    return certificateData[certificateType] || {
        icon: 'fas fa-certificate',
        processingTime: '3-5 business days',
        fee: 'RWF 2,000',
        documents: [
            'National ID or Passport copy',
            'Supporting Documents',
            'Request Letter'
        ]
    };
}

document.addEventListener('DOMContentLoaded', function() {
    // Get certificate type from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const certificateType = urlParams.get('type');

    if (certificateType) {
        // Load the application form for the specified certificate type
        loadApplicationForm(certificateType);
    } else {
        // Show certificate selection if no type specified
        showCertificateSelection();
    }
});

// Load Application Form
function loadApplicationForm(certificateType) {
    const container = document.getElementById('applicationFormContainer');
    const certificateInfo = getCertificateInfo(certificateType);
    
    // Update page title and description
    updatePageHeader(certificateType);
    
    // Create the application form
    container.innerHTML = `
        <div class="application-form-wrapper">
            <!-- Application Progress -->
            <div class="application-progress">
                <div class="progress-step active" data-step="1">
                    <div class="step-number">1</div>
                    <span>Application Details</span>
                </div>
            </div>
            
            <!-- Certificate Information Panel -->
            <div class="certificate-info-panel">
                <div class="certificate-header">
                    <div class="certificate-icon">
                        <i class="${certificateInfo.icon}"></i>
                    </div>
                    <div class="certificate-details">
                        <h3>${certificateType}</h3>
                        <p>Complete the application form below</p>
                    </div>
                </div>
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
                

                
                <!-- Form Navigation -->
                <div class="form-navigation">
                    <button type="button" class="nav-btn next-btn" id="nextBtn">
                        Next
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    `;
    
    // Initialize the form functionality
    initializeApplicationForm(certificateType);
}

// Update Page Header
function updatePageHeader(certificateType) {
    const pageTitle = document.querySelector('.page-title');
    const pageDescription = document.querySelector('.page-description');
    
    if (pageTitle) {
        pageTitle.textContent = `${certificateType} Application`;
    }
    
    if (pageDescription) {
        pageDescription.textContent = `Complete the form below to apply for your ${certificateType}`;
    }
    
    // Update document title
    document.title = `${certificateType} Application - Diocese of Byumba`;
}

// Show Certificate Selection
function showCertificateSelection() {
    const container = document.getElementById('applicationFormContainer');
    
    container.innerHTML = `
        <div class="certificate-selection">
            <h3>Select Certificate Type</h3>
            <p>Choose the type of certificate you would like to apply for:</p>
            
            <div class="certificate-types-grid">
                <div class="certificate-type-card" data-type="Baptism Certificate">
                    <div class="certificate-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                    <h4>Baptism Certificate</h4>
                    <p>Official record of baptism</p>
                    <span class="fee">RWF 2,000</span>
                </div>
                
                <div class="certificate-type-card" data-type="Confirmation Certificate">
                    <div class="certificate-icon">
                        <i class="fas fa-hands-praying"></i>
                    </div>
                    <h4>Confirmation Certificate</h4>
                    <p>Official record of confirmation</p>
                    <span class="fee">RWF 2,500</span>
                </div>
                
                <div class="certificate-type-card" data-type="Marriage Certificate">
                    <div class="certificate-icon">
                        <i class="fas fa-ring"></i>
                    </div>
                    <h4>Marriage Certificate</h4>
                    <p>Official record of marriage</p>
                    <span class="fee">RWF 5,000</span>
                </div>
                
                <div class="certificate-type-card" data-type="Ordination Certificate">
                    <div class="certificate-icon">
                        <i class="fas fa-church"></i>
                    </div>
                    <h4>Ordination Certificate</h4>
                    <p>Official record of ordination</p>
                    <span class="fee">RWF 1,000</span>
                </div>
                
                <div class="certificate-type-card" data-type="Membership Certificate">
                    <div class="certificate-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Membership Certificate</h4>
                    <p>Parish membership record</p>
                    <span class="fee">RWF 1,000</span>
                </div>
                
                <div class="certificate-type-card" data-type="Good Standing Certificate">
                    <div class="certificate-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Good Standing Certificate</h4>
                    <p>Certificate of good standing</p>
                    <span class="fee">RWF 1,000</span>
                </div>
            </div>
        </div>
    `;
    
    // Add click handlers for certificate selection
    const certificateCards = container.querySelectorAll('.certificate-type-card');
    certificateCards.forEach(card => {
        card.addEventListener('click', function() {
            const certificateType = this.getAttribute('data-type');
            // Update URL and load form
            const newUrl = `${window.location.pathname}?type=${encodeURIComponent(certificateType)}`;
            window.history.pushState({}, '', newUrl);
            loadApplicationForm(certificateType);
        });
    });
}

// Initialize Application Form
function initializeApplicationForm(certificateType) {
    // Get form elements
    const form = document.querySelector('#certificateApplicationForm');
    const nextBtn = document.querySelector('#nextBtn');

    if (!form || !nextBtn) return;

    // Navigation button events
    nextBtn.addEventListener('click', () => handleNextClick(certificateType));

    // Initialize file uploads and communication preferences
    initializeFileUploads(document);
    initializeCommunicationPreferences(document);
    updateUploadProgress(document);

    // Handle next button click
    function handleNextClick(certificateType) {
        if (!validateApplicationForm()) {
            return;
        }

        // Store form data in sessionStorage
        const formData = new FormData(form);
        const applicationData = {};

        // Convert FormData to object
        for (let [key, value] of formData.entries()) {
            if (applicationData[key]) {
                // Handle multiple values (like notification methods)
                if (Array.isArray(applicationData[key])) {
                    applicationData[key].push(value);
                } else {
                    applicationData[key] = [applicationData[key], value];
                }
            } else {
                applicationData[key] = value;
            }
        }

        // Store certificate type and application data
        sessionStorage.setItem('certificateType', certificateType);
        sessionStorage.setItem('applicationData', JSON.stringify(applicationData));

        // Redirect to review page
        window.location.href = 'review.html';
    }

    function validateApplicationForm() {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('error');
                isValid = false;
            } else {
                field.classList.remove('error');
            }
        });

        // Check notification methods
        const notificationMethods = document.querySelectorAll('input[name="notificationMethod"]:checked');
        if (notificationMethods.length === 0) {
            showNotification('Please select at least one notification method for receiving your payment code', 'error');
            isValid = false;
        }

        // Check document uploads
        const certificateInfo = getCertificateInfo(certificateType);
        const uploadedFiles = document.querySelectorAll('.upload-preview:not([style*="display: none"])');
        const requiredDocuments = certificateInfo.documents.length;

        if (uploadedFiles.length < requiredDocuments) {
            showNotification(`Please upload all ${requiredDocuments} required documents before proceeding`, 'error');
            isValid = false;
        }

        if (!isValid && requiredFields.length > 0) {
            showNotification('Please fill in all required fields', 'error');
        }

        return isValid;
    }
}

// Certificate-specific form fields (if not available from script.js)
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
            <div class="form-row">
                <div class="form-group">
                    <label for="sponsorName">Sponsor's Full Name *</label>
                    <input type="text" id="sponsorName" name="sponsorName" required>
                </div>
                <div class="form-group">
                    <label for="confirmationName">Confirmation Name</label>
                    <input type="text" id="confirmationName" name="confirmationName" placeholder="Saint name chosen">
                </div>
            </div>
        `,
        'Marriage Certificate': `
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
        `,
        'Ordination Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="ordinationDate">Ordination Date *</label>
                    <input type="date" id="ordinationDate" name="ordinationDate" required>
                </div>
                <div class="form-group">
                    <label for="ordinationType">Type of Ordination *</label>
                    <select id="ordinationType" name="ordinationType" required>
                        <option value="">Select ordination type</option>
                        <option value="deacon">Deacon</option>
                        <option value="priest">Priest</option>
                        <option value="bishop">Bishop</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="ordinationParish">Parish/Cathedral of Ordination *</label>
                    <input type="text" id="ordinationParish" name="ordinationParish" required>
                </div>
                <div class="form-group">
                    <label for="ordinationBishop">Ordaining Bishop *</label>
                    <input type="text" id="ordinationBishop" name="ordinationBishop" required>
                </div>
            </div>
        `,
        'Membership Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="membershipDate">Membership Start Date</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
                <div class="form-group">
                    <label for="currentParish">Current Parish *</label>
                    <input type="text" id="currentParish" name="currentParish" required>
                </div>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="employment">Employment</option>
                    <option value="education">Education</option>
                    <option value="travel">Travel/Visa</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `,
        'Good Standing Certificate': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Current Parish *</label>
                    <input type="text" id="currentParish" name="currentParish" required>
                </div>
                <div class="form-group">
                    <label for="membershipDuration">Years of Membership</label>
                    <input type="number" id="membershipDuration" name="membershipDuration" min="0" placeholder="Number of years">
                </div>
            </div>
            <div class="form-group">
                <label for="standingPurpose">Purpose of Certificate *</label>
                <select id="standingPurpose" name="standingPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="employment">Employment</option>
                    <option value="education">Education</option>
                    <option value="travel">Travel/Visa</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `
    };

    return specificFields[certificateType] || '';
}

// Certificate-specific review fields
function getCertificateSpecificReview(certificateType, formData) {
    const specificReviews = {
        'Baptism Certificate': `
            ${formData.get('baptismDate') ? `
                <div class="review-item">
                    <span>Baptism Date:</span>
                    <span>${formData.get('baptismDate')}</span>
                </div>
            ` : ''}
            ${formData.get('baptismParish') ? `
                <div class="review-item">
                    <span>Parish of Baptism:</span>
                    <span>${formData.get('baptismParish')}</span>
                </div>
            ` : ''}
            ${formData.get('fatherName') ? `
                <div class="review-item">
                    <span>Father's Name:</span>
                    <span>${formData.get('fatherName')}</span>
                </div>
            ` : ''}
            ${formData.get('motherName') ? `
                <div class="review-item">
                    <span>Mother's Name:</span>
                    <span>${formData.get('motherName')}</span>
                </div>
            ` : ''}
        `,
        'Confirmation Certificate': `
            ${formData.get('confirmationDate') ? `
                <div class="review-item">
                    <span>Confirmation Date:</span>
                    <span>${formData.get('confirmationDate')}</span>
                </div>
            ` : ''}
            ${formData.get('confirmationParish') ? `
                <div class="review-item">
                    <span>Parish of Confirmation:</span>
                    <span>${formData.get('confirmationParish')}</span>
                </div>
            ` : ''}
            ${formData.get('sponsorName') ? `
                <div class="review-item">
                    <span>Sponsor's Name:</span>
                    <span>${formData.get('sponsorName')}</span>
                </div>
            ` : ''}
            ${formData.get('confirmationName') ? `
                <div class="review-item">
                    <span>Confirmation Name:</span>
                    <span>${formData.get('confirmationName')}</span>
                </div>
            ` : ''}
        `,
        'Marriage Certificate': `
            ${formData.get('marriageDate') ? `
                <div class="review-item">
                    <span>Marriage Date:</span>
                    <span>${formData.get('marriageDate')}</span>
                </div>
            ` : ''}
            ${formData.get('marriageParish') ? `
                <div class="review-item">
                    <span>Parish of Marriage:</span>
                    <span>${formData.get('marriageParish')}</span>
                </div>
            ` : ''}
            ${formData.get('spouseFirstName') && formData.get('spouseLastName') ? `
                <div class="review-item">
                    <span>Spouse's Name:</span>
                    <span>${formData.get('spouseFirstName')} ${formData.get('spouseLastName')}</span>
                </div>
            ` : ''}
        `,
        'Ordination Certificate': `
            ${formData.get('ordinationDate') ? `
                <div class="review-item">
                    <span>Ordination Date:</span>
                    <span>${formData.get('ordinationDate')}</span>
                </div>
            ` : ''}
            ${formData.get('ordinationType') ? `
                <div class="review-item">
                    <span>Type of Ordination:</span>
                    <span>${formData.get('ordinationType').charAt(0).toUpperCase() + formData.get('ordinationType').slice(1)}</span>
                </div>
            ` : ''}
            ${formData.get('ordinationParish') ? `
                <div class="review-item">
                    <span>Parish/Cathedral:</span>
                    <span>${formData.get('ordinationParish')}</span>
                </div>
            ` : ''}
            ${formData.get('ordinationBishop') ? `
                <div class="review-item">
                    <span>Ordaining Bishop:</span>
                    <span>${formData.get('ordinationBishop')}</span>
                </div>
            ` : ''}
        `,
        'Membership Certificate': `
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>Membership Start Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Current Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipPurpose') ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${formData.get('membershipPurpose').charAt(0).toUpperCase() + formData.get('membershipPurpose').slice(1)}</span>
                </div>
            ` : ''}
        `,
        'Good Standing Certificate': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Current Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDuration') ? `
                <div class="review-item">
                    <span>Years of Membership:</span>
                    <span>${formData.get('membershipDuration')} years</span>
                </div>
            ` : ''}
            ${formData.get('standingPurpose') ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${formData.get('standingPurpose').charAt(0).toUpperCase() + formData.get('standingPurpose').slice(1)}</span>
                </div>
            ` : ''}
        `
    };

    return specificReviews[certificateType] || '';
}

// File Upload Functionality
function initializeFileUploads(container) {
    const uploadAreas = container.querySelectorAll('.file-upload-area');

    uploadAreas.forEach(uploadArea => {
        const fileInput = uploadArea.querySelector('input[type="file"]');
        const placeholder = uploadArea.querySelector('.upload-placeholder');
        const preview = uploadArea.querySelector('.upload-preview');
        const removeBtn = preview.querySelector('.remove-file');

        // Click to upload
        uploadArea.addEventListener('click', function(e) {
            if (e.target !== removeBtn) {
                fileInput.click();
            }
        });

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelection(files[0], uploadArea);
            }
        });

        // File input change
        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                handleFileSelection(e.target.files[0], uploadArea);
            }
        });

        // Remove file
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.value = '';
            placeholder.style.display = 'block';
            preview.style.display = 'none';
            updateUploadProgress(container);
        });
    });

    function handleFileSelection(file, uploadArea) {
        const placeholder = uploadArea.querySelector('.upload-placeholder');
        const preview = uploadArea.querySelector('.upload-preview');
        const fileName = preview.querySelector('.file-name');

        // Validate file
        if (!validateFile(file)) {
            return;
        }

        // Show preview
        fileName.textContent = file.name;
        placeholder.style.display = 'none';
        preview.style.display = 'flex';

        updateUploadProgress(container);
    }

    function validateFile(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

        if (file.size > maxSize) {
            showNotification('File size must be less than 5MB', 'error');
            return false;
        }

        if (!allowedTypes.includes(file.type)) {
            showNotification('Only PDF, JPG, and PNG files are allowed', 'error');
            return false;
        }

        return true;
    }
}

// Update Upload Progress Function
function updateUploadProgress(container) {
    const uploadedFiles = container.querySelectorAll('.upload-preview:not([style*="display: none"])');
    const totalDocuments = container.querySelectorAll('.upload-group').length;
    const progressFill = container.querySelector('#uploadProgressFill');
    const progressText = container.querySelector('#uploadProgressText');

    if (progressFill && progressText) {
        const progress = (uploadedFiles.length / totalDocuments) * 100;
        progressFill.style.width = `${progress}%`;
        progressText.textContent = `${uploadedFiles.length} of ${totalDocuments} documents uploaded`;

        if (uploadedFiles.length === totalDocuments) {
            progressFill.style.backgroundColor = '#1e753f';
        } else {
            progressFill.style.backgroundColor = '#f2c97e';
        }
    }
}

// Communication Preferences Functionality
function initializeCommunicationPreferences(container) {
    const preferenceOptions = container.querySelectorAll('input[name="notificationMethod"]');

    preferenceOptions.forEach(option => {
        option.addEventListener('change', function() {
            updatePreferenceSelection();
        });
    });

    function updatePreferenceSelection() {
        const selectedOptions = container.querySelectorAll('input[name="notificationMethod"]:checked');
        const preferenceNote = container.querySelector('.preference-note');

        if (selectedOptions.length === 0) {
            preferenceNote.style.color = '#d14438';
            preferenceNote.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Please select at least one notification method.</span>';
        } else {
            preferenceNote.style.color = '#1e753f';
            preferenceNote.innerHTML = '<i class="fas fa-info-circle"></i><span>You can select multiple notification methods. At least one method must be selected.</span>';
        }
    }
}

// Notification System (if not available from script.js)
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
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);

    // Close button functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    });
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    };
    return icons[type] || 'fa-info-circle';
}
