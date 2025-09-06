/**
 * Dynamic Header Manager
 * Diocese of Byumba System
 * 
 * Manages header state changes based on user authentication status
 */

class HeaderManager {
    constructor() {
        this.apiBase = 'api/auth.php';
        this.currentUser = null;
        this.isAuthenticated = false;
        this.headerElement = null;
        this.templates = {
            authenticated: null,
            unauthenticated: null
        };
        
        // Initialize on DOM load
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    /**
     * Initialize the header manager
     */
    async init() {
        this.headerElement = document.querySelector('.header');
        if (!this.headerElement) {
            console.warn('Header element not found');
            return;
        }

        // Load templates
        await this.loadTemplates();
        
        // Check current authentication status
        await this.checkAuthenticationStatus();
        
        // Update header based on current status
        this.updateHeader();
        
        // Set up event listeners
        this.setupEventListeners();
    }

    /**
     * Load header templates
     */
    async loadTemplates() {
        // Create templates from existing headers
        this.createTemplatesFromExistingHeaders();
    }

    /**
     * Create templates from existing header structures
     */
    createTemplatesFromExistingHeaders() {
        // Authenticated header template (from dashboard.html structure)
        this.templates.authenticated = `
            <!-- Top Header -->
            <div class="header-top">
                <div class="container">
                    <div class="header-top-content">
                        <div class="logo-section">
                            <div class="logo-container">
                                <img src="images/logo/logo.png" alt="Diocese of Byumba Logo" class="diocese-logo">
                            </div>
                            <div class="diocese-info">
                                <h1 class="diocese-name">EAR Diocese of Byumba</h1>
                                <p class="diocese-subtitle">EAR Diyosezi ya Byumba</p>
                            </div>
                        </div>
                        
                        <!-- Mobile Menu Toggle -->
                        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </button>
                        
                        <div class="header-actions">
                            <div class="language-toggle">
                                <button class="lang-btn active" data-lang="en">EN</button>
                                <button class="lang-btn" data-lang="rw">RW</button>
                                <button class="lang-btn" data-lang="fr">FR</button>
                            </div>
                            <div class="user-menu">
                                <button class="user-menu-toggle" id="userMenuToggle">
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="user-name">Loading...</span>
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                                <div class="user-dropdown" id="userDropdown">
                                    <a href="dashboard.html" class="dropdown-item">
                                        <i class="fas fa-tachometer-alt"></i>
                                        Dashboard
                                    </a>
                                    <a href="profile.html" class="dropdown-item">
                                        <i class="fas fa-user-edit"></i>
                                        Edit Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item logout-btn" data-action="logout">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="navigation" id="mainNavigation">
                <div class="container">
                    <ul class="nav-menu" id="navMenu">
                        <li class="nav-item">
                            <a href="index.html" class="nav-link">
                                <i class="fas fa-certificate"></i>
                                <span>Certificates</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="jobs.html" class="nav-link">
                                <i class="fas fa-briefcase"></i>
                                <span>Jobs</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="bishop-meeting.html" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Bishop Meeting</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="blog.html" class="nav-link">
                                <i class="fas fa-blog"></i>
                                <span>Blog</span>
                            </a>
                        </li>
                        
                        <!-- Mobile-only items -->
                        <li class="nav-item mobile-only">
                            <div class="mobile-language-toggle">
                                <span class="mobile-lang-label">Language:</span>
                                <div class="mobile-lang-buttons">
                                    <button class="lang-btn active" data-lang="en">EN</button>
                                    <button class="lang-btn" data-lang="rw">RW</button>
                                    <button class="lang-btn" data-lang="fr">FR</button>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item mobile-only">
                            <a href="#" class="nav-link mobile-login logout-btn" data-action="logout">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Mobile Menu Overlay -->
            <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
        `;

        // Unauthenticated header template (from index.html structure)
        this.templates.unauthenticated = `
            <!-- Top Header -->
            <div class="header-top">
                <div class="container">
                    <div class="header-top-content">
                        <div class="logo-section">
                            <div class="logo-container">
                                <img src="images/logo/logo.png" alt="Diocese of Byumba Logo" class="diocese-logo">
                            </div>
                            <div class="diocese-info">
                                <h1 class="diocese-name" data-translate="site_name">EAR Diocese of Byumba</h1>
                                <p class="diocese-subtitle" data-translate="site_subtitle">EAR Diyosezi ya Byumba</p>
                            </div>
                        </div>

                        <!-- Mobile Menu Toggle -->
                        <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle mobile menu">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </button>

                        <div class="header-actions">
                            <div class="language-toggle">
                                <button class="lang-btn active" data-lang="en">EN</button>
                                <button class="lang-btn" data-lang="rw">RW</button>
                                <button class="lang-btn" data-lang="fr">FR</button>
                            </div>
                            <a href="login.html" class="login-btn">
                                <i class="fas fa-user"></i>
                                <span class="login-text" data-translate="login">Login</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="navigation" id="mainNavigation">
                <div class="container">
                    <ul class="nav-menu" id="navMenu">
                        <li class="nav-item">
                            <a href="index.html" class="nav-link">
                                <i class="fas fa-certificate"></i>
                                <span data-translate="certificates">Certificates</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="jobs.html" class="nav-link">
                                <i class="fas fa-briefcase"></i>
                                <span data-translate="jobs">Jobs</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="bishop-meeting.html" class="nav-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span data-translate="bishop_meeting">Bishop Meeting</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="blog.html" class="nav-link">
                                <i class="fas fa-blog"></i>
                                <span data-translate="blog">Blog</span>
                            </a>
                        </li>

                        <!-- Mobile-only items -->
                        <li class="nav-item mobile-only">
                            <div class="mobile-language-toggle">
                                <span class="mobile-lang-label">Language:</span>
                                <div class="mobile-lang-buttons">
                                    <button class="lang-btn active" data-lang="en">EN</button>
                                    <button class="lang-btn" data-lang="rw">RW</button>
                                    <button class="lang-btn" data-lang="fr">FR</button>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item mobile-only">
                            <a href="login.html" class="nav-link mobile-login">
                                <i class="fas fa-user"></i>
                                <span data-translate="login">Login</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Mobile Menu Overlay -->
            <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>
        `;
    }

    /**
     * Check current authentication status
     */
    async checkAuthenticationStatus() {
        try {
            const response = await fetch(`${this.apiBase}?action=check-session`);
            const data = await response.json();

            if (data.success && data.data.logged_in) {
                this.isAuthenticated = true;
                this.currentUser = data.data;
            } else {
                this.isAuthenticated = false;
                this.currentUser = null;
            }
        } catch (error) {
            console.error('Error checking authentication status:', error);
            this.isAuthenticated = false;
            this.currentUser = null;
        }
    }

    /**
     * Update header based on authentication status
     */
    updateHeader() {
        if (!this.headerElement) return;

        // Add updating class for smooth transition
        this.headerElement.classList.add('updating');

        const template = this.isAuthenticated ?
            this.templates.authenticated :
            this.templates.unauthenticated;

        // Delay the update slightly for smooth transition
        setTimeout(() => {
            // Update header content
            this.headerElement.innerHTML = template;

            // Update user information if authenticated
            if (this.isAuthenticated && this.currentUser) {
                this.updateUserInfo();
            }

            // Re-initialize header functionality
            this.reinitializeHeaderFunctionality();

            // Remove updating class and add updated class
            this.headerElement.classList.remove('updating');
            this.headerElement.classList.add('updated');

            // Remove updated class after animation
            setTimeout(() => {
                this.headerElement.classList.remove('updated');
            }, 400);

            // Trigger custom event for other components
            this.dispatchHeaderUpdateEvent();
        }, 150);
    }

    /**
     * Update user information in the header
     */
    updateUserInfo() {
        if (!this.currentUser) return;

        // Update user name
        const userNameElements = document.querySelectorAll('.user-name');
        userNameElements.forEach(element => {
            element.textContent = this.currentUser.name;
        });

        // Update user email if displayed
        const userEmailElements = document.querySelectorAll('.user-email');
        userEmailElements.forEach(element => {
            element.textContent = this.currentUser.email;
        });
    }

    /**
     * Re-initialize header functionality after template update
     */
    reinitializeHeaderFunctionality() {
        // Re-initialize mobile menu
        this.initializeMobileMenu();

        // Re-initialize user menu dropdown
        this.initializeUserMenu();

        // Re-initialize language toggle
        this.initializeLanguageToggle();

        // Re-initialize logout functionality
        this.initializeLogoutFunctionality();
    }

    /**
     * Initialize mobile menu functionality
     */
    initializeMobileMenu() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mainNavigation = document.getElementById('mainNavigation');
        const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

        if (mobileMenuToggle && mainNavigation && mobileMenuOverlay) {
            mobileMenuToggle.addEventListener('click', () => {
                mainNavigation.classList.toggle('active');
                mobileMenuOverlay.classList.toggle('active');
                document.body.classList.toggle('menu-open');
            });

            mobileMenuOverlay.addEventListener('click', () => {
                mainNavigation.classList.remove('active');
                mobileMenuOverlay.classList.remove('active');
                document.body.classList.remove('menu-open');
            });
        }
    }

    /**
     * Initialize user menu dropdown
     */
    initializeUserMenu() {
        const userMenuToggle = document.getElementById('userMenuToggle');
        const userDropdown = document.getElementById('userDropdown');

        if (userMenuToggle && userDropdown) {
            userMenuToggle.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.remove('active');
                }
            });
        }
    }

    /**
     * Initialize language toggle functionality
     */
    initializeLanguageToggle() {
        const langButtons = document.querySelectorAll('.lang-btn');

        langButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                const lang = e.target.dataset.lang;

                // Update active state
                langButtons.forEach(btn => btn.classList.remove('active'));
                e.target.classList.add('active');

                // Trigger language change event
                if (window.languageManager) {
                    window.languageManager.setLanguage(lang);
                }
            });
        });
    }

    /**
     * Initialize logout functionality
     */
    initializeLogoutFunctionality() {
        const logoutElements = document.querySelectorAll('.logout-btn, [data-action="logout"]');

        logoutElements.forEach(element => {
            element.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        });
    }

    /**
     * Handle user logout
     */
    async handleLogout() {
        try {
            const response = await fetch(`${this.apiBase}?action=logout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                // Update authentication status
                this.isAuthenticated = false;
                this.currentUser = null;

                // Update header to unauthenticated state
                this.updateHeader();

                // Show success notification
                this.showNotification('Logged out successfully', 'success');

                // Redirect to login page after a short delay
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 1000);
            } else {
                this.showNotification('Logout failed', 'error');
            }
        } catch (error) {
            console.error('Logout error:', error);
            this.showNotification('Logout failed', 'error');
        }
    }

    /**
     * Handle successful login (called from login page)
     */
    async handleLoginSuccess(userData) {
        this.isAuthenticated = true;
        this.currentUser = userData;
        this.updateHeader();
    }

    /**
     * Set up event listeners
     */
    setupEventListeners() {
        // Listen for authentication events
        document.addEventListener('userLoggedIn', (event) => {
            this.handleLoginSuccess(event.detail);
        });

        document.addEventListener('userLoggedOut', () => {
            this.isAuthenticated = false;
            this.currentUser = null;
            this.updateHeader();
        });

        // Listen for session expiration
        document.addEventListener('sessionExpired', () => {
            this.isAuthenticated = false;
            this.currentUser = null;
            this.updateHeader();
            this.showNotification('Session expired. Please log in again.', 'warning');
        });
    }

    /**
     * Dispatch header update event
     */
    dispatchHeaderUpdateEvent() {
        const event = new CustomEvent('headerUpdated', {
            detail: {
                isAuthenticated: this.isAuthenticated,
                user: this.currentUser
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas ${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 10000;
            animation: slideIn 0.3s ease;
            max-width: 300px;
        `;

        document.body.appendChild(notification);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    /**
     * Get notification icon based on type
     */
    getNotificationIcon(type) {
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        return icons[type] || icons.info;
    }

    /**
     * Get notification color based on type
     */
    getNotificationColor(type) {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        return colors[type] || colors.info;
    }

    /**
     * Force refresh authentication status
     */
    async refreshAuthStatus() {
        await this.checkAuthenticationStatus();
        this.updateHeader();
    }
}

// Create global instance
window.headerManager = new HeaderManager();

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HeaderManager;
}
