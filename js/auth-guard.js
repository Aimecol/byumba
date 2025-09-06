/**
 * Authentication Guard for Protected Pages
 * Diocese of Byumba System
 */

class AuthGuard {
    constructor() {
        this.apiBase = 'api/auth.php';
        this.loginPage = 'login.html';
        this.currentUser = null;
    }

    /**
     * Initialize authentication guard
     */
    async init() {
        try {
            const sessionData = await this.checkSession();
            
            if (!sessionData.logged_in) {
                this.redirectToLogin();
                return false;
            }
            
            this.currentUser = sessionData;
            this.updateUserInterface();
            return true;
        } catch (error) {
            console.error('Auth guard error:', error);
            this.redirectToLogin();
            return false;
        }
    }

    /**
     * Check current session status
     */
    async checkSession() {
        try {
            const response = await fetch(`${this.apiBase}?action=check-session`);
            const data = await response.json();
            
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Session check failed:', error);
            throw error;
        }
    }

    /**
     * Logout user
     */
    async logout() {
        try {
            const response = await fetch(`${this.apiBase}?action=logout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                // Clear current user data
                this.currentUser = null;

                // Dispatch logout event for header manager
                const logoutEvent = new CustomEvent('userLoggedOut');
                document.dispatchEvent(logoutEvent);

                this.showNotification('Logged out successfully', 'success');
                setTimeout(() => {
                    this.redirectToLogin();
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
     * Redirect to login page
     */
    redirectToLogin() {
        window.location.href = this.loginPage;
    }

    /**
     * Update user interface with logged-in user data
     */
    updateUserInterface() {
        if (!this.currentUser) return;

        // Update user name in header
        const userNameElements = document.querySelectorAll('.user-name');
        userNameElements.forEach(element => {
            element.textContent = this.currentUser.name;
        });

        // Update welcome message
        const welcomeElements = document.querySelectorAll('.dashboard-title');
        welcomeElements.forEach(element => {
            if (element.textContent.includes('Welcome back')) {
                element.textContent = `Welcome back, ${this.currentUser.name.split(' ')[0]}!`;
            }
        });

        // Update user email if displayed
        const userEmailElements = document.querySelectorAll('.user-email');
        userEmailElements.forEach(element => {
            element.textContent = this.currentUser.email;
        });
    }

    /**
     * Get current user data
     */
    getCurrentUser() {
        return this.currentUser;
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Use existing notification function if available
        if (typeof showNotification === 'function') {
            showNotification(message, type);
            return;
        }

        // Fallback notification
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
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
     * Setup logout event listeners
     */
    setupLogoutListeners() {
        // Find all logout links/buttons
        const logoutElements = document.querySelectorAll('a[href="index.html"], .logout-btn, [data-action="logout"]');
        
        logoutElements.forEach(element => {
            // Remove existing href to prevent default navigation
            if (element.tagName === 'A') {
                element.removeAttribute('href');
                element.style.cursor = 'pointer';
            }
            
            element.addEventListener('click', (e) => {
                e.preventDefault();
                this.logout();
            });
        });
    }
}

// Global auth guard instance
window.authGuard = new AuthGuard();

// Auto-initialize on DOM load for protected pages
document.addEventListener('DOMContentLoaded', async function() {
    // Only run on pages that need authentication
    const protectedPages = ['dashboard.html', 'profile.html', 'my-applications.html', 'my-meetings.html', 'notifications.html'];
    const currentPage = window.location.pathname.split('/').pop();
    
    if (protectedPages.includes(currentPage)) {
        const isAuthenticated = await window.authGuard.init();
        
        if (isAuthenticated) {
            // Setup logout listeners after successful authentication
            window.authGuard.setupLogoutListeners();
        }
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AuthGuard;
}
