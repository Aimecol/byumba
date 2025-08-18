# Admin Activities Management

A comprehensive activity logging and monitoring system for the Diocese of Byumba Admin Panel.

## Features

### 📊 Activity Dashboard
- **Real-time Statistics**: Total activities, active admins, today's activities, and weekly summaries
- **Visual Metrics**: Color-coded statistics cards with icons
- **Activity Counts**: Track administrative actions across the system

### 🔍 Advanced Filtering
- **Admin Filter**: Filter activities by specific administrator
- **Action Type Filter**: Filter by specific action types (login, logout, create, update, delete, etc.)
- **Date Range Filter**: Filter activities between specific dates
- **Search Functionality**: Search across actions, details, and admin emails
- **Combined Filters**: Use multiple filters simultaneously for precise results

### 📋 Activity Log Display
- **Detailed Information**: Date/time, admin details, action type, details, IP address, and user agent
- **Admin Profiles**: Display admin avatars and names with email addresses
- **Action Badges**: Color-coded badges for different action types
- **Browser Detection**: Automatic detection and display of browser information
- **Responsive Table**: Mobile-friendly table with horizontal scrolling

### 🛠 Management Tools
- **Clear Old Activities**: Remove activities older than specified days (30, 60, 90, 180, 365 days)
- **Export Functionality**: Export filtered activities to CSV format
- **Pagination**: Navigate through large activity logs efficiently
- **Auto-refresh**: Automatic page refresh every 30 seconds (when no filters applied)

### 🔐 Security & Permissions
- **Role-based Access**: Different permissions for different admin roles
- **Activity Logging**: All admin actions are automatically logged
- **IP Tracking**: Track IP addresses for security monitoring
- **User Agent Logging**: Record browser and device information

## Database Schema

### admin_activity_log Table
```sql
CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `admin_email` (`admin_email`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Logged Actions

The system automatically logs the following types of administrative actions:

### Authentication
- `login` - Admin login
- `logout` - Admin logout

### User Management
- `create_user` - New user creation
- `update_user` - User information updates
- `delete_user` - User deletion
- `toggle_user_status` - User activation/deactivation

### Application Management
- `update_application_status` - Application status changes
- `approve_application` - Application approvals
- `reject_application` - Application rejections

### Content Management
- `create_blog_post` - New blog post creation
- `update_blog_post` - Blog post updates
- `delete_blog_post` - Blog post deletion
- `toggle_blog_post` - Blog post publish/unpublish

### System Management
- `send_notification` - Bulk notifications
- `clear_old_activities` - Activity log cleanup
- `export_users` - User data export
- `export_activities` - Activity data export

## Usage

### Accessing Activities
1. Navigate to the admin panel
2. Click on "Activities" in the sidebar menu
3. View the activity dashboard and log

### Filtering Activities
1. Use the filter form to specify criteria:
   - Select specific admin from dropdown
   - Choose action type from dropdown
   - Set date range with from/to dates
   - Enter search terms
2. Click "Apply Filters" to filter results
3. Click "Clear Filters" to reset all filters

### Exporting Activities
1. Apply desired filters (optional)
2. Click the "Export" button
3. CSV file will be downloaded with filtered results

### Managing Old Activities
1. Click "Clear Old Activities" button (admin/bishop only)
2. Select time period (30, 60, 90, 180, or 365 days)
3. Confirm deletion of old records

## Files

- `activities.php` - Main activities management page
- `activities_export.php` - CSV export functionality
- `database/admin_activity_log.sql` - Database schema and sample data
- `functions.php` - Contains `logAdminActivity()` function
- `ACTIVITIES_README.md` - This documentation file

## Integration

The activity logging is integrated throughout the admin panel:

```php
// Log an admin activity
logAdminActivity('action_name', 'Detailed description of the action');

// Example usage
logAdminActivity('update_user', "Updated user ID: $user_id");
logAdminActivity('approve_application', "Approved application $app_number");
```

## Security Considerations

- All activities are logged with IP addresses for security monitoring
- User agent strings are recorded for device tracking
- Foreign key constraints ensure data integrity
- Role-based permissions control access to sensitive operations
- Old activity cleanup helps manage database size while maintaining audit trails

## Browser Support

- Modern browsers with JavaScript enabled
- DataTables for enhanced table functionality
- Bootstrap 5 for responsive design
- Font Awesome icons for visual elements
