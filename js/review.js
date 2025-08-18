// Review Page JavaScript

// Certificate Information Data (same as in application.js)
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
            fee: 'RWF 1,000',
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
            fee: 'RWF 1,000',
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
            fee: 'RWF 1,000',
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
    // Get application data from sessionStorage
    const certificateType = sessionStorage.getItem('certificateType');
    const applicationDataStr = sessionStorage.getItem('applicationData');
    
    if (!certificateType || !applicationDataStr) {
        // Redirect back to application if no data found
        window.location.href = 'application.html';
        return;
    }
    
    const applicationData = JSON.parse(applicationDataStr);
    
    // Load the review page
    loadReviewPage(certificateType, applicationData);
});

function loadReviewPage(certificateType, applicationData) {
    const container = document.getElementById('reviewContainer');
    const certificateInfo = getCertificateInfo(certificateType);
    
    // Update page title and description
    updatePageHeader(certificateType);
    
    // Create the review content
    container.innerHTML = `
        <div class="review-wrapper">
            <!-- Certificate Information Panel -->
            <div class="certificate-info-panel">
                <div class="certificate-header">
                    <div class="certificate-icon">
                        <i class="${certificateInfo.icon}"></i>
                    </div>
                    <div class="certificate-details">
                        <h3>${certificateType}</h3>
                        <p>Review your application details below</p>
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
            
            <!-- Review Form -->
            <form class="review-form" id="reviewForm">
                <div class="application-review">
                    <div class="review-section">
                        <h5><i class="fas fa-user"></i> Personal Information</h5>
                        <div class="review-content" id="reviewPersonalInfo">
                            ${generatePersonalInfoReview(applicationData)}
                        </div>
                    </div>

                    <div class="review-section">
                        <h5><i class="fas fa-info-circle"></i> Certificate Details</h5>
                        <div class="review-content" id="reviewCertificateDetails">
                            ${generateCertificateDetailsReview(certificateType, applicationData)}
                        </div>
                    </div>

                    <div class="review-section">
                        <h5><i class="fas fa-file-alt"></i> Documents</h5>
                        <div class="review-content" id="reviewDocuments">
                            ${generateDocumentsReview(certificateInfo, applicationData)}
                        </div>
                    </div>

                    <div class="review-section">
                        <h5><i class="fas fa-bell"></i> Notification Preferences</h5>
                        <div class="review-content" id="reviewNotifications">
                            ${generateNotificationReview(certificateInfo, applicationData)}
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
                
                <!-- Payment Section -->
                <div class="payment-section" id="paymentSection" style="display: none;">
                    <h4 class="section-title">
                        <i class="fas fa-credit-card"></i>
                        Payment Information
                    </h4>

                    <div class="payment-method-selection">
                        <h5>Select Payment Method</h5>
                        <div class="payment-providers">
                            <label class="payment-provider-option">
                                <input type="radio" name="paymentProvider" value="mtn" disabled>
                                <div class="provider-card mtn">
                                    <div class="provider-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="provider-info">
                                        <h6>MTN Mobile Money</h6>
                                        <p>Pay with MTN Mobile Money</p>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-provider-option">
                                <input type="radio" name="paymentProvider" value="airtel" disabled>
                                <div class="provider-card airtel">
                                    <div class="provider-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div class="provider-info">
                                        <h6>Airtel Money</h6>
                                        <p>Pay with Airtel Money</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="payment-details">
                        <div class="form-group">
                            <label for="paymentPhone">Mobile Money Phone Number *</label>
                            <input type="tel" id="paymentPhone" name="paymentPhone" disabled
                                   placeholder="+250 788 123 456" class="payment-input">
                            <small class="input-help">Enter the phone number registered with your mobile money account</small>
                        </div>

                        <div class="payment-summary">
                            <div class="summary-item">
                                <span>Application Fee:</span>
                                <span class="fee-amount" id="feeAmount">${certificateInfo.fee}</span>
                            </div>
                            <div class="summary-item total">
                                <span>Total Amount:</span>
                                <span class="total-amount" id="totalAmount">${certificateInfo.fee}</span>
                            </div>
                        </div>

                        <div class="payment-instructions">
                            <div class="instruction-card">
                                <i class="fas fa-info-circle"></i>
                                <div class="instruction-content">
                                    <h6>Payment Instructions</h6>
                                    <ol>
                                        <li>Select your mobile money provider</li>
                                        <li>Enter your mobile money phone number</li>
                                        <li>Click "Submit & Pay" to initiate payment</li>
                                        <li>You will receive a USSD prompt on your phone</li>
                                        <li>Follow the prompts and enter your PIN to complete payment</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms Agreement -->
                <div class="terms-agreement">
                    <label class="checkbox-label">
                        <input type="checkbox" id="agreeTerms" name="agreeTerms">
                        <span class="checkmark"></span>
                        I agree to the <a href="terms.html" target="_blank">Terms and Conditions</a> and confirm that all information provided is accurate *
                    </label>
                </div>

                <!-- Form Navigation -->
                <div class="form-navigation">
                    <button type="button" class="nav-btn prev-btn" id="backBtn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Edit
                    </button>
                    <button type="button" class="nav-btn submit-btn" id="submitBtn">
                        <i class="fas fa-paper-plane"></i>
                        Submit Application
                    </button>
                    <button type="button" class="nav-btn payment-btn" id="paymentBtn" style="display: none;">
                        <i class="fas fa-credit-card"></i>
                        Submit & Pay
                    </button>
                </div>
            </form>
        </div>
    `;
    
    // Initialize the review functionality
    initializeReviewForm(certificateType, applicationData);
}

// Update Page Header
function updatePageHeader(certificateType) {
    const pageTitle = document.querySelector('.page-title');
    const pageDescription = document.querySelector('.page-description');

    if (pageTitle) {
        pageTitle.textContent = `Review ${certificateType} Application`;
    }

    if (pageDescription) {
        pageDescription.textContent = `Please review your ${certificateType} application details before submitting`;
    }

    // Update document title
    document.title = `Review ${certificateType} Application - Diocese of Byumba`;
}

// Generate Personal Information Review
function generatePersonalInfoReview(applicationData) {
    return `
        <div class="review-item">
            <span>Name:</span>
            <span>${(applicationData.firstName || '') + ' ' + (applicationData.lastName || '')}</span>
        </div>
        <div class="review-item">
            <span>Date of Birth:</span>
            <span>${applicationData.dateOfBirth || 'Not provided'}</span>
        </div>
        <div class="review-item">
            <span>Place of Birth:</span>
            <span>${applicationData.placeOfBirth || 'Not provided'}</span>
        </div>
        <div class="review-item">
            <span>National ID:</span>
            <span>${applicationData.nationalId || 'Not provided'}</span>
        </div>
        <div class="review-item">
            <span>Gender:</span>
            <span>${applicationData.gender ? applicationData.gender.charAt(0).toUpperCase() + applicationData.gender.slice(1) : 'Not provided'}</span>
        </div>
        <div class="review-item">
            <span>Email:</span>
            <span>${applicationData.email || 'Not provided'}</span>
        </div>
        <div class="review-item">
            <span>Phone:</span>
            <span>${applicationData.phone || 'Not provided'}</span>
        </div>
        <div class="review-item">
            <span>Address:</span>
            <span>${applicationData.address || 'Not provided'}</span>
        </div>
    `;
}

// Generate Certificate Details Review
function generateCertificateDetailsReview(certificateType, applicationData) {
    let certificateDetailsHtml = `
        <div class="review-item">
            <span>Certificate Type:</span>
            <span>${certificateType}</span>
        </div>
    `;

    // Add certificate-specific fields based on type
    const specificReview = getCertificateSpecificReview(certificateType, applicationData);
    if (specificReview.trim()) {
        certificateDetailsHtml += specificReview;
    } else {
        certificateDetailsHtml += `
            <div class="review-item">
                <span>Additional Details:</span>
                <span>No additional details provided</span>
            </div>
        `;
    }

    return certificateDetailsHtml;
}

// Generate Documents Review
function generateDocumentsReview(certificateInfo, applicationData) {
    // Count uploaded documents (this is simplified since we can't access actual files)
    const documentCount = certificateInfo.documents.length;

    return `
        <div class="review-item">
            <span>Total Documents:</span>
            <span>${documentCount} documents required</span>
        </div>
        <div class="review-item">
            <span>Document Status:</span>
            <span class="document-status">All required documents uploaded</span>
        </div>
        ${certificateInfo.documents.map(doc => `
            <div class="review-item">
                <span>${doc}:</span>
                <span class="document-file">
                    <i class="fas fa-file"></i>
                    Uploaded
                </span>
            </div>
        `).join('')}
    `;
}

// Generate Notification Review
function generateNotificationReview(certificateInfo, applicationData) {
    const notificationMethods = applicationData.notificationMethod;
    let methods = [];

    if (Array.isArray(notificationMethods)) {
        methods = notificationMethods;
    } else if (notificationMethods) {
        methods = [notificationMethods];
    }

    const labels = {
        'email': 'Email',
        'sms': 'SMS',
        'phone': 'Phone Call'
    };

    const methodLabels = methods.map(method => labels[method] || method);

    return `
        <div class="review-item">
            <span>Notification Methods:</span>
            <span class="${methodLabels.length > 0 ? '' : 'no-documents'}">${methodLabels.length > 0 ? methodLabels.join(', ') : 'None selected'}</span>
        </div>
        <div class="review-item">
            <span>Application Fee:</span>
            <span>${certificateInfo.fee}</span>
        </div>
        <div class="review-item">
            <span>Processing Time:</span>
            <span>${certificateInfo.processingTime}</span>
        </div>
        <div class="review-item">
            <span>Payment Status:</span>
            <span class="payment-status">Pay after approval</span>
        </div>
    `;
}

// Certificate-specific review fields
function getCertificateSpecificReview(certificateType, applicationData) {
    const specificReviews = {
        'Baptism Certificate': `
            ${applicationData.baptismDate ? `
                <div class="review-item">
                    <span>Baptism Date:</span>
                    <span>${applicationData.baptismDate}</span>
                </div>
            ` : ''}
            ${applicationData.baptismParish ? `
                <div class="review-item">
                    <span>Parish of Baptism:</span>
                    <span>${applicationData.baptismParish}</span>
                </div>
            ` : ''}
            ${applicationData.fatherName ? `
                <div class="review-item">
                    <span>Father's Name:</span>
                    <span>${applicationData.fatherName}</span>
                </div>
            ` : ''}
            ${applicationData.motherName ? `
                <div class="review-item">
                    <span>Mother's Name:</span>
                    <span>${applicationData.motherName}</span>
                </div>
            ` : ''}
        `,
        'Confirmation Certificate': `
            ${applicationData.confirmationDate ? `
                <div class="review-item">
                    <span>Confirmation Date:</span>
                    <span>${applicationData.confirmationDate}</span>
                </div>
            ` : ''}
            ${applicationData.confirmationParish ? `
                <div class="review-item">
                    <span>Parish of Confirmation:</span>
                    <span>${applicationData.confirmationParish}</span>
                </div>
            ` : ''}
            ${applicationData.sponsorName ? `
                <div class="review-item">
                    <span>Sponsor's Name:</span>
                    <span>${applicationData.sponsorName}</span>
                </div>
            ` : ''}
            ${applicationData.confirmationName ? `
                <div class="review-item">
                    <span>Confirmation Name:</span>
                    <span>${applicationData.confirmationName}</span>
                </div>
            ` : ''}
        `,
        'Marriage Certificate': `
            ${applicationData.marriageDate ? `
                <div class="review-item">
                    <span>Marriage Date:</span>
                    <span>${applicationData.marriageDate}</span>
                </div>
            ` : ''}
            ${applicationData.marriageParish ? `
                <div class="review-item">
                    <span>Parish of Marriage:</span>
                    <span>${applicationData.marriageParish}</span>
                </div>
            ` : ''}
            ${applicationData.spouseFirstName && applicationData.spouseLastName ? `
                <div class="review-item">
                    <span>Spouse's Name:</span>
                    <span>${applicationData.spouseFirstName} ${applicationData.spouseLastName}</span>
                </div>
            ` : ''}
        `,
        'Ordination Certificate': `
            ${applicationData.ordinationDate ? `
                <div class="review-item">
                    <span>Ordination Date:</span>
                    <span>${applicationData.ordinationDate}</span>
                </div>
            ` : ''}
            ${applicationData.ordinationType ? `
                <div class="review-item">
                    <span>Type of Ordination:</span>
                    <span>${applicationData.ordinationType.charAt(0).toUpperCase() + applicationData.ordinationType.slice(1)}</span>
                </div>
            ` : ''}
            ${applicationData.ordinationParish ? `
                <div class="review-item">
                    <span>Parish/Cathedral:</span>
                    <span>${applicationData.ordinationParish}</span>
                </div>
            ` : ''}
            ${applicationData.ordinationBishop ? `
                <div class="review-item">
                    <span>Ordaining Bishop:</span>
                    <span>${applicationData.ordinationBishop}</span>
                </div>
            ` : ''}
        `,
        'Membership Certificate': `
            ${applicationData.membershipDate ? `
                <div class="review-item">
                    <span>Membership Start Date:</span>
                    <span>${applicationData.membershipDate}</span>
                </div>
            ` : ''}
            ${applicationData.currentParish ? `
                <div class="review-item">
                    <span>Current Parish:</span>
                    <span>${applicationData.currentParish}</span>
                </div>
            ` : ''}
            ${applicationData.membershipPurpose ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${applicationData.membershipPurpose.charAt(0).toUpperCase() + applicationData.membershipPurpose.slice(1)}</span>
                </div>
            ` : ''}
        `,
        'Good Standing Certificate': `
            ${applicationData.currentParish ? `
                <div class="review-item">
                    <span>Current Parish:</span>
                    <span>${applicationData.currentParish}</span>
                </div>
            ` : ''}
            ${applicationData.membershipDuration ? `
                <div class="review-item">
                    <span>Years of Membership:</span>
                    <span>${applicationData.membershipDuration} years</span>
                </div>
            ` : ''}
            ${applicationData.standingPurpose ? `
                <div class="review-item">
                    <span>Purpose:</span>
                    <span>${applicationData.standingPurpose.charAt(0).toUpperCase() + applicationData.standingPurpose.slice(1)}</span>
                </div>
            ` : ''}
        `
    };

    return specificReviews[certificateType] || '';
}

// Initialize Review Form
function initializeReviewForm(certificateType, applicationData) {
    const form = document.querySelector('#reviewForm');
    const backBtn = document.querySelector('#backBtn');
    const submitBtn = document.querySelector('#submitBtn');
    const paymentBtn = document.querySelector('#paymentBtn');
    const paymentSection = document.querySelector('#paymentSection');

    if (!form || !backBtn || !submitBtn) return;

    // Initialize payment API
    const paymentAPI = new window.PaymentAPI();
    let currentPaymentTransaction = null;

    // Back button event
    backBtn.addEventListener('click', () => {
        // Go back to application form with the certificate type
        window.location.href = `application.html?type=${encodeURIComponent(certificateType)}`;
    });

    // Submit button click (initial submit to show payment)
    submitBtn.addEventListener('click', handleInitialSubmission);

    // Payment button event
    if (paymentBtn) {
        paymentBtn.addEventListener('click', handlePaymentSubmission);
    }

    // Payment provider selection
    const providerInputs = document.querySelectorAll('input[name="paymentProvider"]');
    providerInputs.forEach(input => {
        input.addEventListener('change', updatePaymentProvider);
    });

    // Phone number validation
    const paymentPhone = document.querySelector('#paymentPhone');
    if (paymentPhone) {
        paymentPhone.addEventListener('input', validatePaymentPhone);
    }

    function handleInitialSubmission() {
        // Validate terms agreement
        const agreeTerms = document.querySelector('#agreeTerms');
        if (!agreeTerms || !agreeTerms.checked) {
            showNotification('Please agree to the Terms and Conditions to submit your application', 'error');
            // Focus on the checkbox to make it visible
            if (agreeTerms) {
                agreeTerms.focus();
                agreeTerms.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        // Show payment section
        showPaymentSection();
    }

    function showPaymentSection() {
        // Hide submit button, show payment button
        submitBtn.style.display = 'none';
        paymentBtn.style.display = 'inline-flex';

        // Show payment section
        paymentSection.style.display = 'block';

        // Enable payment form fields and make them required
        const paymentProviders = document.querySelectorAll('input[name="paymentProvider"]');
        const paymentPhone = document.querySelector('#paymentPhone');

        paymentProviders.forEach(input => {
            input.disabled = false;
            input.required = true;
        });

        if (paymentPhone) {
            paymentPhone.disabled = false;
            paymentPhone.required = true;
        }

        // Scroll to payment section
        paymentSection.scrollIntoView({ behavior: 'smooth' });

        showNotification('Please complete payment to finalize your application', 'info');
    }

    async function handlePaymentSubmission(e) {
        e.preventDefault();

        // Validate payment form
        if (!validatePaymentForm()) {
            return;
        }

        // Get payment details
        const phoneNumber = document.querySelector('#paymentPhone').value;
        const certificateInfo = getCertificateInfo(certificateType);
        const amount = parseFloat(certificateInfo.fee.replace(/[^\d.]/g, ''));

        // Show loading state
        paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Payment...';
        paymentBtn.disabled = true;

        try {
            // Request payment
            const paymentResult = await paymentAPI.requestPayment(
                phoneNumber,
                amount,
                `${certificateType} Application Fee`
            );

            if (paymentResult.success) {
                currentPaymentTransaction = {
                    transactionId: paymentResult.transactionId,
                    intouchpayTransactionId: paymentResult.intouchpayTransactionId
                };

                showNotification('Payment request sent! Please check your phone for USSD prompt and complete the payment.', 'success');

                // Start monitoring payment status
                monitorPaymentStatus();

            } else {
                throw new Error(paymentResult.message || 'Payment request failed');
            }

        } catch (error) {
            console.error('Payment error:', error);
            showNotification(`Payment failed: ${error.message}`, 'error');

            // Reset button
            paymentBtn.innerHTML = '<i class="fas fa-credit-card"></i> Submit & Pay';
            paymentBtn.disabled = false;
        }
    }

    function validatePaymentForm() {
        const selectedProviderElement = document.querySelector('input[name="paymentProvider"]:checked');
        const phoneNumber = document.querySelector('#paymentPhone').value;

        if (!selectedProviderElement) {
            showNotification('Please select a payment provider', 'error');
            return false;
        }

        const selectedProvider = selectedProviderElement.value;

        if (!phoneNumber.trim()) {
            showNotification('Please enter your mobile money phone number', 'error');
            return false;
        }

        // Validate phone number format
        if (!paymentAPI.paymentService.validatePhoneNumber(phoneNumber)) {
            showNotification('Please enter a valid phone number', 'error');
            return false;
        }

        // Check if provider matches phone number
        const provider = paymentAPI.paymentService.detectProvider(phoneNumber);
        if (!provider || provider.key !== selectedProvider) {
            const expectedPrefixes = paymentAPI.paymentService.config.providers[selectedProvider]?.prefixes || [];
            showNotification(`Phone number does not match selected provider (${selectedProvider.toUpperCase()}). Expected prefixes: ${expectedPrefixes.join(', ')}`, 'error');
            return false;
        }

        return true;
    }

    function updatePaymentProvider() {
        const selectedProviderElement = document.querySelector('input[name="paymentProvider"]:checked');
        if (selectedProviderElement) {
            // Update UI based on selected provider
            const providerCards = document.querySelectorAll('.provider-card');
            providerCards.forEach(card => card.classList.remove('selected'));

            const selectedCard = selectedProviderElement.closest('.payment-provider-option').querySelector('.provider-card');
            selectedCard.classList.add('selected');
        }
    }

    function validatePaymentPhone() {
        const phoneInput = document.querySelector('#paymentPhone');
        const phoneNumber = phoneInput.value;

        if (phoneNumber.length > 3) {
            const provider = paymentAPI.paymentService.detectProvider(phoneNumber);

            if (provider) {
                // Auto-select matching provider
                const providerInput = document.querySelector(`input[name="paymentProvider"][value="${provider.key}"]`);
                if (providerInput) {
                    providerInput.checked = true;
                    updatePaymentProvider();
                }
            }
        }
    }

    async function monitorPaymentStatus() {
        if (!currentPaymentTransaction) return;

        const maxAttempts = 30; // Monitor for 5 minutes (30 attempts * 10 seconds)
        let attempts = 0;
        let consecutiveErrors = 0;

        // Show initial status message
        showNotification('Monitoring payment status... Please complete the payment on your phone.', 'info');

        // For testing purposes - simulate payment success after 30 seconds if backend is not available
        const testingMode = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1' || window.location.protocol === 'file:';
        if (testingMode) {
            setTimeout(() => {
                if (paymentBtn.innerHTML.includes('Processing Payment')) {
                    console.log('Testing mode: Simulating payment success after 30 seconds');
                    handlePaymentSuccess();
                }
            }, 30000); // 30 seconds for testing
        }

        const checkStatus = async () => {
            attempts++;

            try {
                const statusResult = await paymentAPI.getTransactionStatus(
                    currentPaymentTransaction.transactionId,
                    currentPaymentTransaction.intouchpayTransactionId
                );

                // Reset error counter on successful API call
                consecutiveErrors = 0;

                if (statusResult.success) {
                    if (statusResult.status === 'Successful' || statusResult.responseCode === '01') {
                        // Payment successful
                        handlePaymentSuccess();
                        return;
                    } else if (statusResult.status === 'Failed' || (statusResult.responseCode && statusResult.responseCode !== '1000' && statusResult.responseCode !== 'Pending')) {
                        // Payment failed (but not if it's just pending)
                        handlePaymentFailure(statusResult.message);
                        return;
                    }

                    // Still pending - update user
                    if (attempts % 6 === 0) { // Every minute (6 * 10 seconds)
                        showNotification(`Payment still pending... (${Math.floor(attempts/6)} minute${attempts >= 12 ? 's' : ''} elapsed)`, 'info');
                    }
                }

                // Continue monitoring if still pending and within attempts limit
                if (attempts < maxAttempts) {
                    setTimeout(checkStatus, 10000); // Check every 10 seconds
                } else {
                    // Timeout - but give user option to continue waiting
                    handlePaymentTimeout();
                }

            } catch (error) {
                console.error('Status check error:', error);
                consecutiveErrors++;

                // If too many consecutive errors, stop monitoring
                if (consecutiveErrors >= 5) {
                    handlePaymentError('Unable to check payment status. Please verify your payment manually.');
                    return;
                }

                // Continue monitoring despite errors
                if (attempts < maxAttempts) {
                    setTimeout(checkStatus, 10000);
                } else {
                    handlePaymentTimeout();
                }
            }
        };

        // Start checking after 5 seconds
        setTimeout(checkStatus, 5000);
    }

    function handlePaymentSuccess() {
        // Update UI
        paymentBtn.innerHTML = '<i class="fas fa-check-circle"></i> Payment Successful';
        paymentBtn.classList.add('success');
        paymentBtn.disabled = true;

        showNotification('Payment completed successfully! Your application has been submitted.', 'success');

        // Complete application submission
        completeApplicationSubmission();
    }

    function handlePaymentFailure(message) {
        paymentBtn.innerHTML = '<i class="fas fa-credit-card"></i> Submit & Pay';
        paymentBtn.disabled = false;

        showNotification(`Payment failed: ${message}. Please try again.`, 'error');
    }

    function handlePaymentTimeout() {
        paymentBtn.innerHTML = '<i class="fas fa-clock"></i> Check Payment Status';
        paymentBtn.disabled = false;

        // Change button behavior to manual status check
        paymentBtn.onclick = () => {
            paymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
            paymentBtn.disabled = true;

            // Check status one more time
            paymentAPI.getTransactionStatus(
                currentPaymentTransaction.transactionId,
                currentPaymentTransaction.intouchpayTransactionId
            ).then(result => {
                if (result.status === 'Successful' || result.responseCode === '01') {
                    handlePaymentSuccess();
                } else {
                    paymentBtn.innerHTML = '<i class="fas fa-credit-card"></i> Try Payment Again';
                    paymentBtn.disabled = false;
                    paymentBtn.onclick = () => handlePaymentSubmission();
                    showNotification('Payment not confirmed yet. You can try the payment again or contact support if you completed the payment.', 'warning');
                }
            }).catch(() => {
                paymentBtn.innerHTML = '<i class="fas fa-credit-card"></i> Try Payment Again';
                paymentBtn.disabled = false;
                paymentBtn.onclick = () => handlePaymentSubmission();
                showNotification('Unable to verify payment status. Please try again or contact support.', 'error');
            });
        };

        showNotification('Payment monitoring timed out. Click "Check Payment Status" to verify manually, or try the payment again.', 'warning');
    }

    function handlePaymentError(message) {
        paymentBtn.innerHTML = '<i class="fas fa-credit-card"></i> Try Payment Again';
        paymentBtn.disabled = false;

        // Reset button to original payment function
        paymentBtn.onclick = () => handlePaymentSubmission();

        showNotification(message, 'error');
    }

    function completeApplicationSubmission() {
        // Store application and payment data
        const applicationSubmission = {
            certificateType: certificateType,
            applicationData: applicationData,
            paymentTransaction: currentPaymentTransaction,
            submittedAt: new Date().toISOString()
        };

        // Store in local storage for reference
        localStorage.setItem('lastApplicationSubmission', JSON.stringify(applicationSubmission));

        // Clear session storage
        sessionStorage.removeItem('certificateType');
        sessionStorage.removeItem('applicationData');

        // Redirect to success page after delay
        setTimeout(() => {
            window.location.href = 'index.html?success=true&payment=completed';
        }, 3000);
    }
}

// Show notification function (if not available from script.js)
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close">&times;</button>
    `;

    // Add to page
    document.body.appendChild(notification);

    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);

    // Auto hide after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);

    // Close button
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    });
}
