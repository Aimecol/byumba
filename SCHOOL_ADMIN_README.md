# School Administration System

A comprehensive school management system for the Diocese of Byumba, allowing schools to submit reports to the diocese administration and manage their institutional data.

## 🏫 Overview

The School Administration System provides:

- **School Authentication**: Secure login system for school administrators
- **Report Management**: Create, submit, and track various types of reports
- **Dashboard Interface**: Overview of school statistics and recent activities
- **Admin Integration**: Diocese administrators can review and manage school reports
- **API Endpoints**: RESTful API for programmatic access

## 📋 Features

### For Schools
- **Secure Login**: Username/password authentication with session management
- **Dashboard**: Statistics overview and quick actions
- **Report Creation**: Dynamic forms based on report types
- **Report Tracking**: View submission status and administrator feedback
- **Multiple Report Types**: Academic, Financial, Infrastructure, Staff, Student Welfare, Religious Activities

### For Diocese Administrators
- **Report Review**: View and manage all school reports
- **Status Management**: Approve, reject, or request revisions
- **School Overview**: Monitor all schools in the diocese
- **Export Functionality**: Generate reports for analysis

## 🗂️ Directory Structure

```
school-admin/
├── index.php              # Login page
├── dashboard.php           # Main dashboard
├── reports.php            # Reports listing
├── create-report.php      # Create new report
├── view-report.php        # View report details
├── edit-report.php        # Edit draft reports
├── delete-report.php      # Delete draft reports
├── submit-report.php      # Submit reports
├── logout.php             # Logout handler
├── includes/
│   ├── functions.php      # Core functions and classes
│   ├── header.php         # Common header
│   └── footer.php         # Common footer
├── assets/
│   ├── css/
│   │   └── school-admin.css  # Custom styles
│   └── js/
│       └── school-admin.js   # JavaScript functionality
└── api/
    └── index.php          # API endpoints

admin/
└── school-reports.php     # Admin panel for managing school reports

database/
└── school_administration.sql  # Database schema and sample data
```

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Existing Diocese of Byumba system

### Setup Steps

1. **Run the setup script**:
   ```
   http://your-domain/setup_school_admin.php
   ```

2. **Verify installation**:
   - Check that all database tables are created
   - Ensure file permissions are correct
   - Test sample login credentials

3. **Access the system**:
   - School Admin: `http://your-domain/school-admin/`
   - Admin Panel: `http://your-domain/admin/school-reports.php`

## 🔐 Authentication

### School Users
Schools authenticate using username/password combinations. Each school can have multiple users with different roles:

- **Headmaster**: Full access to all school functions
- **Admin**: Administrative access
- **Secretary**: Limited administrative access
- **Teacher**: Basic access (future expansion)

### Sample Credentials
```
School: St. Mary's Primary School
Username: stmary_admin
Password: password

School: Holy Cross Secondary School  
Username: holycross_admin
Password: password
```

## 📊 Report Types

The system supports various report types:

1. **Academic Performance Report** (Quarterly)
   - Student enrollment and performance data
   - Pass rates and academic achievements

2. **Financial Report** (Monthly)
   - Income, expenses, and budget status
   - Fee collection and funding information

3. **Infrastructure Report** (Semester)
   - Building conditions and maintenance needs
   - Facility status and safety concerns

4. **Staff Report** (Quarterly)
   - Teacher information and qualifications
   - Staff development and training needs

5. **Student Welfare Report** (Quarterly)
   - Disciplinary cases and health issues
   - Support programs and counseling

6. **Religious Activities Report** (Semester)
   - Chapel services and spiritual programs
   - Religious education activities

7. **Emergency/Incident Report** (As needed)
   - Urgent matters requiring immediate attention

## 🔧 API Endpoints

### Authentication
- `POST /school-admin/api/?endpoint=auth` - Login/logout
- `GET /school-admin/api/?endpoint=auth` - Check authentication status

### Dashboard
- `GET /school-admin/api/?endpoint=dashboard` - Get dashboard data

### Reports
- `GET /school-admin/api/?endpoint=reports` - List reports
- `GET /school-admin/api/?endpoint=reports&id={id}` - Get specific report
- `POST /school-admin/api/?endpoint=reports` - Create new report

### Report Types
- `GET /school-admin/api/?endpoint=report-types` - Get available report types

## 🎨 Customization

### Styling
The system uses Bootstrap 5 with custom CSS in `assets/css/school-admin.css`. Key customization areas:

- Color scheme variables in CSS root
- Card and button styling
- Status badge colors
- Responsive breakpoints

### Report Fields
Report types and their required fields are stored in the database and can be customized:

```sql
UPDATE report_types 
SET required_fields = '["field1", "field2", "field3"]' 
WHERE type_code = 'ACADEMIC_QUARTERLY';
```

## 🔒 Security Features

- **Session Management**: Secure session handling with timeout
- **Input Validation**: Server-side validation for all inputs
- **SQL Injection Protection**: Prepared statements throughout
- **Access Control**: Role-based permissions
- **Activity Logging**: All actions are logged for audit trails
- **CSRF Protection**: Form tokens for sensitive operations

## 📱 Mobile Responsiveness

The system is fully responsive and works on:
- Desktop computers
- Tablets
- Mobile phones
- Various screen sizes and orientations

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check MySQL service is running
   - Verify database credentials in `config/database.php`
   - Ensure database exists and is accessible

2. **Login Issues**
   - Verify user credentials in `school_users` table
   - Check if school and user are active
   - Clear browser cache and cookies

3. **Permission Errors**
   - Ensure web server has read/write permissions
   - Check file ownership and permissions
   - Verify directory structure is intact

4. **Report Submission Fails**
   - Check required fields are filled
   - Verify report type is active
   - Check database connection

### Debug Mode
Enable debug mode by adding to the top of any PHP file:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 🔄 Updates and Maintenance

### Regular Maintenance
- Monitor database size and performance
- Review activity logs for security issues
- Update sample data as needed
- Backup database regularly

### Adding New Schools
```sql
INSERT INTO schools (school_code, school_name, school_type, ...) VALUES (...);
INSERT INTO school_users (school_id, username, password_hash, ...) VALUES (...);
```

### Adding New Report Types
```sql
INSERT INTO report_types (type_code, type_name, required_fields, ...) VALUES (...);
```

## 📞 Support

For technical support:
- Email: admin@diocesebyumba.rw
- Check system logs in browser developer tools
- Review database error logs
- Contact system administrator

## 📄 License

This system is part of the Diocese of Byumba management platform.
© 2025 Diocese of Byumba. All rights reserved.

---

**Version**: 1.0  
**Last Updated**: September 2025  
**Compatibility**: PHP 7.4+, MySQL 5.7+, Bootstrap 5
