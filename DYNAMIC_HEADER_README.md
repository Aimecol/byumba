# Dynamic Header System - Diocese of Byumba

## Overview

The Dynamic Header System provides a seamless way to manage header states based on user authentication status across all pages of the Diocese of Byumba application. The header automatically switches between authenticated and unauthenticated states without requiring page refreshes.

## Features

- **Automatic State Detection**: Checks authentication status on page load
- **Real-time Updates**: Header updates immediately after login/logout actions
- **Session Persistence**: Maintains header state across page refreshes
- **Smooth Transitions**: Animated transitions between header states
- **Mobile Responsive**: Works seamlessly on all device sizes
- **Event-Driven**: Uses custom events for communication between components

## Components

### 1. HeaderManager (`js/header-manager.js`)
The main class that manages header state changes.

**Key Methods:**
- `init()`: Initializes the header manager
- `checkAuthenticationStatus()`: Checks current session status
- `updateHeader()`: Updates header based on authentication state
- `handleLogout()`: Handles user logout
- `refreshAuthStatus()`: Forces refresh of authentication status

### 2. HeaderLoader (`js/header-loader.js`)
Utility class for loading header templates (optional, for future template-based approach).

### 3. Header Templates
Two main header states are defined in the HeaderManager:
- **Authenticated**: Shows user menu with dropdown (profile, dashboard, logout)
- **Unauthenticated**: Shows login button

## Implementation

### 1. Include Required Scripts
Add these scripts to your HTML pages:

```html
<!-- Dynamic Header System -->
<script src="js/header-manager.js"></script>
<script src="js/header-loader.js"></script>
```

### 2. Update Header HTML
Replace static header with dynamic header placeholder:

```html
<!-- Dynamic Header - Will be loaded by header-manager.js -->
<header class="header" id="dynamicHeader">
    <!-- Header content will be dynamically updated based on authentication status -->
    <div class="header-loading" style="padding: 20px; text-align: center; background: linear-gradient(135deg, #1e753f 0%, #2a8f4f 100%); color: white;">
        <i class="fas fa-spinner fa-spin"></i> Loading header...
    </div>
</header>
```

### 3. Authentication Integration
The system integrates with existing authentication:

**Login Process:**
```javascript
// After successful login, dispatch event
const loginEvent = new CustomEvent('userLoggedIn', {
    detail: {
        user_id: data.user_id,
        name: data.name,
        email: data.email,
        logged_in: true
    }
});
document.dispatchEvent(loginEvent);
```

**Logout Process:**
```javascript
// After successful logout, dispatch event
const logoutEvent = new CustomEvent('userLoggedOut');
document.dispatchEvent(logoutEvent);
```

## API Integration

The system works with the existing authentication API (`api/auth.php`):

- **Session Check**: `GET api/auth.php?action=check-session`
- **Logout**: `POST api/auth.php?action=logout`

## Events

### Custom Events Dispatched:
- `userLoggedIn`: When user successfully logs in
- `userLoggedOut`: When user logs out
- `sessionExpired`: When session expires
- `headerUpdated`: When header state changes

### Event Listeners:
The HeaderManager listens for authentication events and updates the header accordingly.

## CSS Classes

### Header States:
- `.header.updating`: Applied during header transition
- `.header.updated`: Applied after header update (for animation)

### Notification Styles:
- `.notification`: Base notification style
- `.notification-success`: Success notifications
- `.notification-error`: Error notifications
- `.notification-warning`: Warning notifications
- `.notification-info`: Info notifications

## Testing

Use the test page (`test-header.html`) to verify functionality:

1. **Load Test**: Header should load in correct initial state
2. **Login Simulation**: Test header change to authenticated state
3. **Logout Simulation**: Test header change to unauthenticated state
4. **Real Authentication**: Test with actual login/logout
5. **Session Persistence**: Test state persistence across page refreshes

## Pages Updated

The following pages have been updated to use the dynamic header system:

- `index.html` - Main certificates page
- `dashboard.html` - User dashboard (protected)
- `application.html` - Certificate application page
- `test-header.html` - Testing page

## Troubleshooting

### Common Issues:

1. **Header not updating after login/logout**
   - Check if events are being dispatched correctly
   - Verify HeaderManager is initialized
   - Check browser console for errors

2. **Header shows loading state indefinitely**
   - Check API connectivity
   - Verify authentication API responses
   - Check for JavaScript errors

3. **Styles not applied correctly**
   - Ensure CSS files are loaded
   - Check for CSS conflicts
   - Verify header HTML structure

### Debug Mode:
Enable debug logging by setting:
```javascript
window.headerManager.debug = true;
```

## Future Enhancements

1. **Template Caching**: Cache header templates for better performance
2. **User Avatar Support**: Add profile picture support
3. **Notification Center**: Integrate with notification system
4. **Theme Support**: Add dark/light theme switching
5. **Accessibility**: Enhanced ARIA support and keyboard navigation

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Dependencies

- Font Awesome 6.0+ (for icons)
- Modern browser with ES6 support
- Existing authentication system

## Security Considerations

- Session validation is handled server-side
- No sensitive data stored in client-side code
- CSRF protection through existing authentication system
- XSS protection through proper HTML escaping
