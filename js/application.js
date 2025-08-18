// Certificate Application Page JavaScript

// Certificate Information Data
function getCertificateInfo(certificateType) {
    const certificateData = {
        'Abasheshakanguhe': {
            icon: 'fas fa-certificate',
            processingTime: '7 business days',
            fee: 'RWF 2,000',
            documents: [
                'National ID Copy',
                'Membership Proof',
                'Passport Photo'
            ]
        },
        'Ebenezer': {
            icon: 'fas fa-star',
            processingTime: '7 business days',
            fee: 'RWF 2,000',
            documents: [
                'National ID Copy',
                'Group Membership Proof',
                'Passport Photo'
            ]
        },
        'Father\'s Union': {
            icon: 'fas fa-users',
            processingTime: '5 business days',
            fee: 'RWF 2,500',
            documents: [
                'National ID Copy',
                'Marriage Certificate',
                'Passport Photo'
            ]
        },
        'Icyemezo cyo gusura kwa korare': {
            icon: 'fas fa-home',
            processingTime: '3 business days',
            fee: 'RWF 1,500',
            documents: [
                'National ID Copy',
                'Request Letter',
                'Passport Photo'
            ]
        },
        'Icyemezo cyuko winjiye mumuryango wa GFS': {
            icon: 'fas fa-female',
            processingTime: '5 business days',
            fee: 'RWF 2,000',
            documents: [
                'National ID Copy',
                'Application Form',
                'Passport Photo'
            ]
        },
        'Icyemezo cyumukirisitu': {
            icon: 'fas fa-cross',
            processingTime: '3 business days',
            fee: 'RWF 1,500',
            documents: [
                'National ID Copy',
                'Baptism Certificate',
                'Passport Photo'
            ]
        },
        'Marriage': {
            icon: 'fas fa-ring',
            processingTime: '7 business days',
            fee: 'RWF 5,000',
            documents: [
                'National ID Copy',
                'Birth Certificate',
                'Passport Photo',
                'Medical Certificate'
            ]
        },
        'Mother\'s Union': {
            icon: 'fas fa-heart',
            processingTime: '5 business days',
            fee: 'RWF 2,500',
            documents: [
                'National ID Copy',
                'Marriage Certificate',
                'Passport Photo'
            ]
        },
        'Youth Union': {
            icon: 'fas fa-graduation-cap',
            processingTime: '3 business days',
            fee: 'RWF 1,500',
            documents: [
                'National ID Copy',
                'School Certificate',
                'Passport Photo'
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
                <div class="certificate-type-card" data-type="Abasheshakanguhe">
                    <div class="certificate-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Abasheshakanguhe</h4>
                    <p>Certificate for Abasheshakanguhe members</p>
                    <span class="fee">RWF 2,000</span>
                </div>

                <div class="certificate-type-card" data-type="Ebenezer">
                    <div class="certificate-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4>Ebenezer</h4>
                    <p>Certificate for Ebenezer group members</p>
                    <span class="fee">RWF 2,000</span>
                </div>

                <div class="certificate-type-card" data-type="Father's Union">
                    <div class="certificate-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Father's Union</h4>
                    <p>Certificate for Father's Union members</p>
                    <span class="fee">RWF 2,500</span>
                </div>

                <div class="certificate-type-card" data-type="Icyemezo cyo gusura kwa korare">
                    <div class="certificate-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h4>Icyemezo cyo gusura kwa korare</h4>
                    <p>Certificate for visiting korare</p>
                    <span class="fee">RWF 1,500</span>
                </div>

                <div class="certificate-type-card" data-type="Icyemezo cyuko winjiye mumuryango wa GFS">
                    <div class="certificate-icon">
                        <i class="fas fa-female"></i>
                    </div>
                    <h4>GFS Membership</h4>
                    <p>Certificate for joining GFS organization</p>
                    <span class="fee">RWF 2,000</span>
                </div>

                <div class="certificate-type-card" data-type="Icyemezo cyumukirisitu">
                    <div class="certificate-icon">
                        <i class="fas fa-cross"></i>
                    </div>
                    <h4>Icyemezo cyumukirisitu</h4>
                    <p>Christian certificate</p>
                    <span class="fee">RWF 1,500</span>
                </div>

                <div class="certificate-type-card" data-type="Marriage">
                    <div class="certificate-icon">
                        <i class="fas fa-ring"></i>
                    </div>
                    <h4>Marriage</h4>
                    <p>Marriage certificate</p>
                    <span class="fee">RWF 5,000</span>
                </div>

                <div class="certificate-type-card" data-type="Mother's Union">
                    <div class="certificate-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h4>Mother's Union</h4>
                    <p>Certificate for Mother's Union members</p>
                    <span class="fee">RWF 2,500</span>
                </div>

                <div class="certificate-type-card" data-type="Youth Union">
                    <div class="certificate-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h4>Youth Union</h4>
                    <p>Certificate for Youth Union members</p>
                    <span class="fee">RWF 1,500</span>
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
        'Abasheshakanguhe': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="membershipDate">Date of joining Abasheshakanguhe (if known)</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="transfer">Transfer to another parish</option>
                    <option value="employment">Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="additionalInfo">Additional Information</label>
                <textarea id="additionalInfo" name="additionalInfo" rows="3" placeholder="Any additional information about your membership"></textarea>
            </div>
        `,
        'Ebenezer': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="membershipDate">Date of joining Ebenezer (if known)</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="transfer">Transfer to another parish</option>
                    <option value="employment">Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="additionalInfo">Additional Information</label>
                <textarea id="additionalInfo" name="additionalInfo" rows="3" placeholder="Any additional information about your membership"></textarea>
            </div>
        `,
        'Father\'s Union': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="membershipDate">Date of joining Father's Union (if known)</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="spouseFirstName">Wife's First Name *</label>
                    <input type="text" id="spouseFirstName" name="spouseFirstName" required>
                </div>
                <div class="form-group">
                    <label for="spouseLastName">Wife's Last Name *</label>
                    <input type="text" id="spouseLastName" name="spouseLastName" required>
                </div>
            </div>
            <div class="form-group">
                <label for="marriageDate">Marriage Date *</label>
                <input type="date" id="marriageDate" name="marriageDate" required>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="transfer">Transfer to another parish</option>
                    <option value="employment">Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `,
        'Icyemezo cyo gusura kwa korare': `
            <div class="form-row">
                <div class="form-group">
                    <label for="choirName">Choir Name *</label>
                    <input type="text" id="choirName" name="choirName" required placeholder="Enter choir name">
                </div>
                <div class="form-group">
                    <label for="currentParish">Home Parish *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your home parish">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="visitDate">Visit Date *</label>
                    <input type="date" id="visitDate" name="visitDate" required>
                </div>
                <div class="form-group">
                    <label for="visitLocation">Visit Location *</label>
                    <input type="text" id="visitLocation" name="visitLocation" required placeholder="Where the choir will visit">
                </div>
            </div>
            <div class="form-group">
                <label for="choirLeaderName">Choir Leader Name *</label>
                <input type="text" id="choirLeaderName" name="choirLeaderName" required placeholder="Name of choir leader">
            </div>
            <div class="form-group">
                <label for="visitPurpose">Purpose of Visit</label>
                <textarea id="visitPurpose" name="visitPurpose" rows="3" placeholder="Describe the purpose of the choir visit"></textarea>
            </div>
        `,
        'Icyemezo cyuko winjiye mumuryango wa GFS': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="membershipDate">Date of joining GFS *</label>
                    <input type="date" id="membershipDate" name="membershipDate" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="ceremonyLocation">Where the ceremony took place *</label>
                    <input type="text" id="ceremonyLocation" name="ceremonyLocation" required placeholder="Location of GFS enrollment ceremony">
                </div>
                <div class="form-group">
                    <label for="gfsLeaderName">GFS Leader Name *</label>
                    <input type="text" id="gfsLeaderName" name="gfsLeaderName" required placeholder="Name of GFS leader who conducted ceremony">
                </div>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="transfer">Transfer to another parish</option>
                    <option value="employment">Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `,
        'Icyemezo cyumukirisitu': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="baptismDate">Baptism Date *</label>
                    <input type="date" id="baptismDate" name="baptismDate" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="baptismLocation">Where you were baptized *</label>
                    <input type="text" id="baptismLocation" name="baptismLocation" required placeholder="Location of baptism">
                </div>
                <div class="form-group">
                    <label for="confirmationDate">Confirmation Date (if confirmed)</label>
                    <input type="date" id="confirmationDate" name="confirmationDate">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="confirmationLocation">Where you were confirmed</label>
                    <input type="text" id="confirmationLocation" name="confirmationLocation" placeholder="Location of confirmation">
                </div>
                <div class="form-group">
                    <label for="churchRole">Role in Church</label>
                    <input type="text" id="churchRole" name="churchRole" placeholder="Your role/ministry in church">
                </div>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="employment">Employment</option>
                    <option value="education">Education</option>
                    <option value="travel">Travel/Visa</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `,
        'Marriage': `
            <div class="form-row">
                <div class="form-group">
                    <label for="marriageDate">Marriage Date *</label>
                    <input type="date" id="marriageDate" name="marriageDate" required>
                </div>
                <div class="form-group">
                    <label for="marriageLocation">Where marriage took place *</label>
                    <input type="text" id="marriageLocation" name="marriageLocation" required placeholder="Location of marriage ceremony">
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
            <div class="form-row">
                <div class="form-group">
                    <label for="spouseDateOfBirth">Spouse's Date of Birth *</label>
                    <input type="date" id="spouseDateOfBirth" name="spouseDateOfBirth" required>
                </div>
                <div class="form-group">
                    <label for="spouseParish">Spouse's Parish *</label>
                    <input type="text" id="spouseParish" name="spouseParish" required placeholder="Spouse's parish">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="spouseBaptismDate">Spouse's Baptism Date</label>
                    <input type="date" id="spouseBaptismDate" name="spouseBaptismDate">
                </div>
                <div class="form-group">
                    <label for="spouseConfirmationDate">Spouse's Confirmation Date</label>
                    <input type="date" id="spouseConfirmationDate" name="spouseConfirmationDate">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="fatherName">Father's Name</label>
                    <input type="text" id="fatherName" name="fatherName" placeholder="Your father's full name">
                </div>
                <div class="form-group">
                    <label for="motherName">Mother's Name</label>
                    <input type="text" id="motherName" name="motherName" placeholder="Your mother's full name">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="spouseFatherName">Spouse's Father's Name</label>
                    <input type="text" id="spouseFatherName" name="spouseFatherName" placeholder="Spouse's father's full name">
                </div>
                <div class="form-group">
                    <label for="spouseMotherName">Spouse's Mother's Name</label>
                    <input type="text" id="spouseMotherName" name="spouseMotherName" placeholder="Spouse's mother's full name">
                </div>
            </div>
            <div class="form-group">
                <label for="officiantName">Name of person who performed ceremony *</label>
                <input type="text" id="officiantName" name="officiantName" required placeholder="Name of priest/bishop who performed marriage">
            </div>
        `,
        'Mother\'s Union': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="membershipDate">Date of joining Mother's Union (if known)</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="spouseFirstName">Husband's First Name *</label>
                    <input type="text" id="spouseFirstName" name="spouseFirstName" required>
                </div>
                <div class="form-group">
                    <label for="spouseLastName">Husband's Last Name *</label>
                    <input type="text" id="spouseLastName" name="spouseLastName" required>
                </div>
            </div>
            <div class="form-group">
                <label for="marriageDate">Marriage Date *</label>
                <input type="date" id="marriageDate" name="marriageDate" required>
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="transfer">Transfer to another parish</option>
                    <option value="employment">Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `,
        'Youth Union': `
            <div class="form-row">
                <div class="form-group">
                    <label for="currentParish">Parish where you worship *</label>
                    <input type="text" id="currentParish" name="currentParish" required placeholder="Enter your parish name">
                </div>
                <div class="form-group">
                    <label for="membershipDate">Date of joining Youth Union (if known)</label>
                    <input type="date" id="membershipDate" name="membershipDate">
                </div>
            </div>
            <div class="form-group">
                <label for="schoolName">School/Institution Name</label>
                <input type="text" id="schoolName" name="schoolName" placeholder="Current school or institution">
            </div>
            <div class="form-group">
                <label for="membershipPurpose">Purpose of Certificate *</label>
                <select id="membershipPurpose" name="membershipPurpose" required>
                    <option value="">Select purpose</option>
                    <option value="official_record">Official Record</option>
                    <option value="transfer">Transfer to another parish</option>
                    <option value="education">Education</option>
                    <option value="employment">Employment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="additionalInfo">Additional Information</label>
                <textarea id="additionalInfo" name="additionalInfo" rows="3" placeholder="Any additional information about your membership"></textarea>
            </div>
        `
    };

    return specificFields[certificateType] || '';
}

// Certificate-specific review fields
function getCertificateSpecificReview(certificateType, formData) {
    const specificReviews = {
        'Abasheshakanguhe': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>Membership Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipPurpose') ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${formData.get('membershipPurpose').charAt(0).toUpperCase() + formData.get('membershipPurpose').slice(1)}</span>
                </div>
            ` : ''}
        `,
        'Ebenezer': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>Membership Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipPurpose') ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${formData.get('membershipPurpose').charAt(0).toUpperCase() + formData.get('membershipPurpose').slice(1)}</span>
                </div>
            ` : ''}
        `,
        'Father\'s Union': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>Membership Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('spouseFirstName') && formData.get('spouseLastName') ? `
                <div class="review-item">
                    <span>Wife's Name:</span>
                    <span>${formData.get('spouseFirstName')} ${formData.get('spouseLastName')}</span>
                </div>
            ` : ''}
            ${formData.get('marriageDate') ? `
                <div class="review-item">
                    <span>Marriage Date:</span>
                    <span>${formData.get('marriageDate')}</span>
                </div>
            ` : ''}
        `,
        'Icyemezo cyo gusura kwa korare': `
            ${formData.get('choirName') ? `
                <div class="review-item">
                    <span>Choir Name:</span>
                    <span>${formData.get('choirName')}</span>
                </div>
            ` : ''}
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Home Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('visitDate') ? `
                <div class="review-item">
                    <span>Visit Date:</span>
                    <span>${formData.get('visitDate')}</span>
                </div>
            ` : ''}
            ${formData.get('visitLocation') ? `
                <div class="review-item">
                    <span>Visit Location:</span>
                    <span>${formData.get('visitLocation')}</span>
                </div>
            ` : ''}
            ${formData.get('choirLeaderName') ? `
                <div class="review-item">
                    <span>Choir Leader:</span>
                    <span>${formData.get('choirLeaderName')}</span>
                </div>
            ` : ''}
        `,
        'Icyemezo cyuko winjiye mumuryango wa GFS': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>GFS Membership Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('ceremonyLocation') ? `
                <div class="review-item">
                    <span>Ceremony Location:</span>
                    <span>${formData.get('ceremonyLocation')}</span>
                </div>
            ` : ''}
            ${formData.get('gfsLeaderName') ? `
                <div class="review-item">
                    <span>GFS Leader:</span>
                    <span>${formData.get('gfsLeaderName')}</span>
                </div>
            ` : ''}
        `,
        'Icyemezo cyumukirisitu': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('baptismDate') ? `
                <div class="review-item">
                    <span>Baptism Date:</span>
                    <span>${formData.get('baptismDate')}</span>
                </div>
            ` : ''}
            ${formData.get('baptismLocation') ? `
                <div class="review-item">
                    <span>Baptism Location:</span>
                    <span>${formData.get('baptismLocation')}</span>
                </div>
            ` : ''}
            ${formData.get('confirmationDate') ? `
                <div class="review-item">
                    <span>Confirmation Date:</span>
                    <span>${formData.get('confirmationDate')}</span>
                </div>
            ` : ''}
            ${formData.get('churchRole') ? `
                <div class="review-item">
                    <span>Church Role:</span>
                    <span>${formData.get('churchRole')}</span>
                </div>
            ` : ''}
        `,
        'Marriage': `
            ${formData.get('marriageDate') ? `
                <div class="review-item">
                    <span>Marriage Date:</span>
                    <span>${formData.get('marriageDate')}</span>
                </div>
            ` : ''}
            ${formData.get('marriageLocation') ? `
                <div class="review-item">
                    <span>Marriage Location:</span>
                    <span>${formData.get('marriageLocation')}</span>
                </div>
            ` : ''}
            ${formData.get('spouseFirstName') && formData.get('spouseLastName') ? `
                <div class="review-item">
                    <span>Spouse's Name:</span>
                    <span>${formData.get('spouseFirstName')} ${formData.get('spouseLastName')}</span>
                </div>
            ` : ''}
            ${formData.get('spouseDateOfBirth') ? `
                <div class="review-item">
                    <span>Spouse's Date of Birth:</span>
                    <span>${formData.get('spouseDateOfBirth')}</span>
                </div>
            ` : ''}
            ${formData.get('spouseParish') ? `
                <div class="review-item">
                    <span>Spouse's Parish:</span>
                    <span>${formData.get('spouseParish')}</span>
                </div>
            ` : ''}
            ${formData.get('officiantName') ? `
                <div class="review-item">
                    <span>Officiant:</span>
                    <span>${formData.get('officiantName')}</span>
                </div>
            ` : ''}
        `,
        'Mother\'s Union': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>Membership Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('spouseFirstName') && formData.get('spouseLastName') ? `
                <div class="review-item">
                    <span>Husband's Name:</span>
                    <span>${formData.get('spouseFirstName')} ${formData.get('spouseLastName')}</span>
                </div>
            ` : ''}
            ${formData.get('marriageDate') ? `
                <div class="review-item">
                    <span>Marriage Date:</span>
                    <span>${formData.get('marriageDate')}</span>
                </div>
            ` : ''}
        `,
        'Youth Union': `
            ${formData.get('currentParish') ? `
                <div class="review-item">
                    <span>Parish:</span>
                    <span>${formData.get('currentParish')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipDate') ? `
                <div class="review-item">
                    <span>Membership Date:</span>
                    <span>${formData.get('membershipDate')}</span>
                </div>
            ` : ''}
            ${formData.get('schoolName') ? `
                <div class="review-item">
                    <span>School/Institution:</span>
                    <span>${formData.get('schoolName')}</span>
                </div>
            ` : ''}
            ${formData.get('membershipPurpose') ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${formData.get('membershipPurpose').charAt(0).toUpperCase() + formData.get('membershipPurpose').slice(1)}</span>
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
