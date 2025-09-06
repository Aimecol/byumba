/**
 * Header Loader Utility
 * Diocese of Byumba System
 * 
 * Loads the dynamic header template into pages
 */

class HeaderLoader {
    constructor() {
        this.headerTemplateUrl = 'includes/header.html';
        this.isLoaded = false;
    }

    /**
     * Load header template into the page
     */
    async loadHeader(targetSelector = 'header') {
        try {
            // Show loading indicator
            this.showLoadingIndicator();

            // Fetch header template
            const response = await fetch(this.headerTemplateUrl);
            if (!response.ok) {
                throw new Error(`Failed to load header template: ${response.status}`);
            }

            const headerHtml = await response.text();
            
            // Find target element
            const targetElement = document.querySelector(targetSelector);
            if (!targetElement) {
                throw new Error(`Target element '${targetSelector}' not found`);
            }

            // Replace target element content
            targetElement.outerHTML = headerHtml;

            // Mark as loaded
            this.isLoaded = true;

            // Hide loading indicator
            this.hideLoadingIndicator();

            // Initialize header functionality
            this.initializeHeaderFunctionality();

            // Dispatch loaded event
            this.dispatchHeaderLoadedEvent();

        } catch (error) {
            console.error('Error loading header:', error);
            this.hideLoadingIndicator();
            this.showErrorFallback();
        }
    }

    /**
     * Show loading indicator
     */
    showLoadingIndicator() {
        const indicator = document.getElementById('headerLoadingIndicator');
        if (indicator) {
            indicator.style.display = 'block';
        }
    }

    /**
     * Hide loading indicator
     */
    hideLoadingIndicator() {
        const indicator = document.getElementById('headerLoadingIndicator');
        if (indicator) {
            indicator.style.display = 'none';
        }
    }

    /**
     * Show error fallback
     */
    showErrorFallback() {
        const headerElement = document.querySelector('.header');
        if (headerElement) {
            headerElement.innerHTML = `
                <div style="background: #dc3545; color: white; padding: 15px; text-align: center;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load header. Please refresh the page.
                    <button onclick="location.reload()" style="margin-left: 10px; padding: 5px 10px; background: white; color: #dc3545; border: none; border-radius: 3px; cursor: pointer;">
                        Refresh
                    </button>
                </div>
            `;
        }
    }

    /**
     * Initialize header functionality after loading
     */
    initializeHeaderFunctionality() {
        // Initialize mobile menu
        this.initializeMobileMenu();
        
        // Initialize language toggle
        this.initializeLanguageToggle();
        
        // Set active navigation item
        this.setActiveNavigation();
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
     * Set active navigation item based on current page
     */
    setActiveNavigation() {
        const currentPage = window.location.pathname.split('/').pop() || 'index.html';
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage || (currentPage === '' && href === 'index.html')) {
                link.closest('.nav-item').classList.add('active');
            } else {
                link.closest('.nav-item').classList.remove('active');
            }
        });
    }

    /**
     * Dispatch header loaded event
     */
    dispatchHeaderLoadedEvent() {
        const event = new CustomEvent('headerLoaded', {
            detail: {
                timestamp: Date.now()
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Check if header is loaded
     */
    isHeaderLoaded() {
        return this.isLoaded;
    }
}

// Auto-load header on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Only auto-load if there's a header element and no header manager is already running
    const headerElement = document.querySelector('header');
    if (headerElement && !window.headerLoaderInstance) {
        window.headerLoaderInstance = new HeaderLoader();
        
        // Check if this is a page that should use dynamic header
        const currentPage = window.location.pathname.split('/').pop();
        const dynamicHeaderPages = [
            'index.html', 'jobs.html', 'bishop-meeting.html', 'blog.html', 
            'application.html', 'dashboard.html', 'profile.html', 
            'my-applications.html', 'my-meetings.html', 'notifications.html'
        ];
        
        if (dynamicHeaderPages.includes(currentPage) || currentPage === '') {
            // Don't auto-load, let individual pages control this
            // window.headerLoaderInstance.loadHeader();
        }
    }
});

// Export for global use
window.HeaderLoader = HeaderLoader;
