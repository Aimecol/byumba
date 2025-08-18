# Diocese of Byumba - Admin Dashboard

A comprehensive administrative panel for managing the Diocese of Byumba's digital operations.

## Features

### 🔐 Authentication & Security
- Secure admin login system
- Role-based access control (Bishop, Admin, Secretary)
- Session management
- Activity logging

### 📊 Dashboard Overview
- Real-time statistics
- Recent activities timeline
- Application status charts
- Monthly trends analysis
- Quick action buttons

### 👥 User Management
- View and manage all registered users
- User verification status
- Search and filter capabilities
- User activity tracking
- Export functionality

### 📋 Application Management
- Certificate application processing
- Status updates (Pending → Processing → Approved → Completed)
- Document management
- Payment tracking
- Bulk operations

### 📅 Meeting Management
- Schedule bishop/priest meetings
- Meeting type categorization
- Status tracking
- Calendar integration
- Notification system

### 💼 Job Management
- Post job opportunities
- Multilingual support
- Application tracking
- Category management
- Deadline monitoring

### 📝 Blog Management
- Create and edit blog posts
- Category management
- Featured posts
- Publishing workflow
- SEO optimization

### ⛪ Parish Management
- Parish information
- Priest assignments
- Contact details
- Activity tracking

### 🔔 Notification System
- System notifications
- User alerts
- Email integration
- Bulk messaging

### ⚙️ Settings
- General site settings
- Email configuration
- System information
- Backup management

## Technology Stack

- **Backend**: PHP 8.x with PDO
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript
- **Icons**: Font Awesome 6
- **Charts**: Chart.js
- **Tables**: DataTables
- **Server**: Apache/Nginx (XAMPP compatible)

## Installation

1. **Prerequisites**
   - XAMPP or similar (Apache, MySQL, PHP 8.x)
   - Web browser
   - Text editor (optional)

2. **Setup**
   ```bash
   # Place the project in your web server directory
   # For XAMPP: C:\xampp\htdocs\byumba
   
   # Import the database
   # Use phpMyAdmin or MySQL command line
   mysql -u root -p < database/diocese_byumba.sql
   ```

3. **Configuration**
   - Update database credentials in `config/database.php`
   - Configure email settings in admin panel
   - Set appropriate file permissions

4. **Admin Access**
   - Default admin emails: `admin@diocesebyumba.rw`, `bishop@diocesebyumba.rw`
   - Create admin users in the database or use existing user accounts
   - Access admin panel: `http://localhost/byumba/admin/`

## File Structure

```
admin/
├── assets/
│   └── css/
│       └── admin.css          # Custom admin styles
├── includes/
│   ├── header.php             # Common header with navigation
│   └── footer.php             # Common footer with scripts
├── index.php                  # Login page
├── auth.php                   # Authentication system
├── functions.php              # Common admin functions
├── dashboard.php              # Main dashboard
├── users.php                  # User management
├── applications.php           # Application management
├── meetings.php               # Meeting management
├── blog.php                   # Blog management
├── settings.php               # System settings
├── logout.php                 # Logout handler
└── README.md                  # This file
```

## User Roles & Permissions

### Bishop
- Full access to all features
- Can manage all users and content
- System configuration access
- Financial oversight

### Admin
- Most administrative functions
- User management
- Content management
- Report generation
- Limited system settings

### Secretary
- Application processing
- Meeting scheduling
- Basic user management
- Content creation
- Limited reporting

## Security Features

- Password hashing (PHP password_hash)
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- CSRF protection (session tokens)
- Role-based access control
- Activity logging
- Session timeout

## Responsive Design

The admin panel is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- Various screen sizes

## Browser Support

- Chrome (recommended)
- Firefox
- Safari
- Edge
- Internet Explorer 11+

## Customization

### Themes
- Modify `admin/assets/css/admin.css` for styling
- Update color variables in CSS
- Customize Bootstrap components

### Features
- Add new admin pages following existing patterns
- Extend authentication system
- Add custom reports
- Integrate third-party services

## Troubleshooting

### Common Issues

1. **Login Issues**
   - Check database connection
   - Verify user credentials
   - Check session configuration

2. **Permission Errors**
   - Verify file permissions
   - Check user roles in database
   - Review authentication logic

3. **Database Errors**
   - Check database credentials
   - Verify table structure
   - Review SQL queries

### Support

For technical support or questions:
- Check the main project documentation
- Review error logs
- Contact system administrator

## Contributing

1. Follow existing code patterns
2. Test thoroughly before deployment
3. Document new features
4. Maintain security standards
5. Update this README as needed

## License

This project is part of the Diocese of Byumba management system.
All rights reserved.

---

**Version**: 1.0.0  
**Last Updated**: June 2025  
**Developed for**: Diocese of Byumba, Rwanda
