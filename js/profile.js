// Profile Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize profile page
    initializeProfile();
});

function initializeProfile() {
    // Initialize user menu
    initializeUserMenu();
    
    // Initialize profile picture upload
    initializeProfilePicture();
    
    // Initialize form handling
    initializeFormHandling();
    
    // Initialize save/cancel buttons
    initializeSaveCancel();
    
    // Track form changes
    trackFormChanges();
}

// User Menu Functionality (same as dashboard)
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

// Profile Picture Upload
function initializeProfilePicture() {
    const profilePicture = document.getElementById('profilePicture');
    const profileImageInput = document.getElementById('profileImageInput');
    const profileImage = document.getElementById('profileImage');
    
    if (profilePicture && profileImageInput && profileImage) {
        // Click to upload
        profilePicture.addEventListener('click', function() {
            profileImageInput.click();
        });
        
        // Handle file selection
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleProfileImageUpload(file, profileImage);
            }
        });
        
        // Drag and drop functionality
        profilePicture.addEventListener('dragover', function(e) {
            e.preventDefault();
            profilePicture.classList.add('drag-over');
        });
        
        profilePicture.addEventListener('dragleave', function(e) {
            e.preventDefault();
            profilePicture.classList.remove('drag-over');
        });
        
        profilePicture.addEventListener('drop', function(e) {
            e.preventDefault();
            profilePicture.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleProfileImageUpload(files[0], profileImage);
            }
        });
    }
}

// Handle Profile Image Upload
function handleProfileImageUpload(file, imageElement) {
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!allowedTypes.includes(file.type)) {
        showNotification('Please select a valid image file (JPG, PNG, or GIF)', 'error');
        return;
    }
    
    // Validate file size (5MB max)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
        showNotification('Image file size must be less than 5MB', 'error');
        return;
    }
    
    // Create file reader
    const reader = new FileReader();
    
    reader.onload = function(e) {
        imageElement.src = e.target.result;
        markFormAsChanged();
        showNotification('Profile picture updated. Don\'t forget to save your changes!', 'info');
    };
    
    reader.onerror = function() {
        showNotification('Error reading the image file', 'error');
    };
    
    reader.readAsDataURL(file);
}

// Form Handling
function initializeFormHandling() {
    const forms = document.querySelectorAll('.profile-form');
    
    forms.forEach(form => {
        // Add input event listeners to track changes
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('input', markFormAsChanged);
            input.addEventListener('change', markFormAsChanged);
        });
        
        // Prevent form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    });
    
    // Initialize notification preferences
    initializeNotificationPreferences();
}

// Initialize Notification Preferences
function initializeNotificationPreferences() {
    const checkboxes = document.querySelectorAll('.notification-preferences input[type="checkbox"]');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            markFormAsChanged();
            
            // Update preference display
            const optionContent = this.closest('.preference-option').querySelector('.option-content');
            if (this.checked) {
                optionContent.style.opacity = '1';
            } else {
                optionContent.style.opacity = '0.6';
            }
        });
    });
}

// Save and Cancel Buttons
function initializeSaveCancel() {
    const saveButton = document.getElementById('saveProfile');
    const cancelButton = document.getElementById('cancelChanges');
    
    if (saveButton) {
        saveButton.addEventListener('click', saveProfile);
    }
    
    if (cancelButton) {
        cancelButton.addEventListener('click', cancelChanges);
    }
}

// Track Form Changes
let formChanged = false;

function trackFormChanges() {
    // Store original form data
    storeOriginalFormData();
    
    // Warn user before leaving if there are unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });
}

function markFormAsChanged() {
    formChanged = true;
    updateSaveButtonState();
}

function markFormAsSaved() {
    formChanged = false;
    updateSaveButtonState();
}

function updateSaveButtonState() {
    const saveButton = document.getElementById('saveProfile');
    const cancelButton = document.getElementById('cancelChanges');
    
    if (saveButton && cancelButton) {
        if (formChanged) {
            saveButton.disabled = false;
            cancelButton.disabled = false;
            saveButton.classList.add('has-changes');
            cancelButton.classList.add('has-changes');
        } else {
            saveButton.disabled = true;
            cancelButton.disabled = true;
            saveButton.classList.remove('has-changes');
            cancelButton.classList.remove('has-changes');
        }
    }
}

// Store Original Form Data
let originalFormData = {};

function storeOriginalFormData() {
    const forms = document.querySelectorAll('.profile-form');
    
    forms.forEach((form, formIndex) => {
        const formData = new FormData(form);
        originalFormData[formIndex] = {};
        
        for (let [key, value] of formData.entries()) {
            originalFormData[formIndex][key] = value;
        }
    });
    
    // Store profile image src
    const profileImage = document.getElementById('profileImage');
    if (profileImage) {
        originalFormData.profileImageSrc = profileImage.src;
    }
    
    // Store notification preferences
    const checkboxes = document.querySelectorAll('.notification-preferences input[type="checkbox"]');
    originalFormData.notifications = {};
    checkboxes.forEach(checkbox => {
        originalFormData.notifications[checkbox.name + '_' + checkbox.value] = checkbox.checked;
    });
}

// Save Profile
function saveProfile() {
    if (!formChanged) {
        showNotification('No changes to save', 'info');
        return;
    }
    
    // Validate required fields
    if (!validateRequiredFields()) {
        return;
    }
    
    // Show loading state
    const saveButton = document.getElementById('saveProfile');
    const originalText = saveButton.innerHTML;
    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    saveButton.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        // Collect all form data
        const profileData = collectProfileData();
        
        // In real app, this would be an API call
        console.log('Saving profile data:', profileData);
        
        // Reset button state
        saveButton.innerHTML = originalText;
        saveButton.disabled = false;
        
        // Mark as saved
        markFormAsSaved();
        
        // Store new original data
        storeOriginalFormData();
        
        // Show success message
        showNotification('Profile updated successfully!', 'success');
        
    }, 2000);
}

// Cancel Changes
function cancelChanges() {
    if (!formChanged) {
        showNotification('No changes to cancel', 'info');
        return;
    }
    
    if (confirm('Are you sure you want to discard all changes?')) {
        restoreOriginalFormData();
        markFormAsSaved();
        showNotification('Changes discarded', 'info');
    }
}

// Validate Required Fields
function validateRequiredFields() {
    const requiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

// Collect Profile Data
function collectProfileData() {
    const data = {
        personalInfo: {},
        contactInfo: {},
        parishInfo: {},
        notifications: {},
        profileImage: null
    };
    
    // Personal Information
    const personalForm = document.getElementById('personalInfoForm');
    if (personalForm) {
        const formData = new FormData(personalForm);
        for (let [key, value] of formData.entries()) {
            data.personalInfo[key] = value;
        }
    }
    
    // Contact Information
    const contactForm = document.getElementById('contactInfoForm');
    if (contactForm) {
        const formData = new FormData(contactForm);
        for (let [key, value] of formData.entries()) {
            data.contactInfo[key] = value;
        }
    }
    
    // Parish Information
    const parishForm = document.getElementById('parishInfoForm');
    if (parishForm) {
        const formData = new FormData(parishForm);
        for (let [key, value] of formData.entries()) {
            data.parishInfo[key] = value;
        }
    }
    
    // Notification Preferences
    const checkboxes = document.querySelectorAll('.notification-preferences input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        if (!data.notifications[checkbox.name]) {
            data.notifications[checkbox.name] = [];
        }
        if (checkbox.checked) {
            data.notifications[checkbox.name].push(checkbox.value);
        }
    });
    
    // Profile Image
    const profileImage = document.getElementById('profileImage');
    if (profileImage && profileImage.src !== originalFormData.profileImageSrc) {
        data.profileImage = profileImage.src;
    }
    
    return data;
}

// Restore Original Form Data
function restoreOriginalFormData() {
    const forms = document.querySelectorAll('.profile-form');
    
    forms.forEach((form, formIndex) => {
        if (originalFormData[formIndex]) {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const originalValue = originalFormData[formIndex][input.name];
                if (originalValue !== undefined) {
                    input.value = originalValue;
                }
            });
        }
    });
    
    // Restore profile image
    const profileImage = document.getElementById('profileImage');
    if (profileImage && originalFormData.profileImageSrc) {
        profileImage.src = originalFormData.profileImageSrc;
    }
    
    // Restore notification preferences
    const checkboxes = document.querySelectorAll('.notification-preferences input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        const key = checkbox.name + '_' + checkbox.value;
        if (originalFormData.notifications && originalFormData.notifications[key] !== undefined) {
            checkbox.checked = originalFormData.notifications[key];
        }
    });
}

// Initialize form state on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial button states
    updateSaveButtonState();
    
    // Initialize notification preference display
    const checkboxes = document.querySelectorAll('.notification-preferences input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        const optionContent = checkbox.closest('.preference-option').querySelector('.option-content');
        if (!checkbox.checked) {
            optionContent.style.opacity = '0.6';
        }
    });
});
