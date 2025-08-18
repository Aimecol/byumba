# Diocese of Byumba - Startup Guide

This guide will help you get the Diocese of Byumba system up and running on your local XAMPP installation.

## 🚀 Quick Start

### Step 1: Start XAMPP Services
1. Open **XAMPP Control Panel**
2. Click **"Start"** next to **Apache**
3. Click **"Start"** next to **MySQL**
4. Wait for both services to show **green status**

### Step 2: Check Database Status
1. Open your browser and go to: `http://localhost/byumba/admin/database_status.php`
2. This will show you the current database connection status
3. Follow any troubleshooting steps shown on the page

### Step 3: Import Database (if needed)
If the database doesn't exist:
1. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
2. Click **"New"** to create a new database
3. Name it: `diocese_byumba`
4. Click **"Create"**
5. Select the new database
6. Click **"Import"** tab
7. Choose file: `database/diocese_byumba.sql`
8. Click **"Go"**
9. Import `database/admin_activity_log.sql` (for admin activity tracking)

### Step 4: Test the System
1. Visit: `http://localhost/byumba/admin/test_connection.php`
2. All tests should show green checkmarks
3. If any tests fail, follow the troubleshooting steps

### Step 5: Access Admin Panel
1. Go to: `http://localhost/byumba/admin/`
2. Use one of these default admin accounts:
   - Email: `admin@diocesebyumba.rw`
   - Email: `bishop@diocesebyumba.rw`
   - Email: `secretary@diocesebyumba.rw`
3. Password: Use the password set during user creation

## 🔧 Troubleshooting

### "Connection Error: No connection could be made"
**Problem**: MySQL server is not running
**Solution**: 
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait for green status indicator

### "Database 'diocese_byumba' doesn't exist"
**Problem**: Database hasn't been imported
**Solution**:
1. Follow Step 3 above to import the database
2. Make sure both SQL files are imported

### "Access denied for user 'root'"
**Problem**: Database credentials are incorrect
**Solution**:
1. Check `config/database.php`
2. Verify username/password settings
3. Default XAMPP MySQL: username=`root`, password=`empty`

### "Table 'admin_activity_log' doesn't exist"
**Problem**: Admin activity table is missing
**Solution**:
1. Import `database/admin_activity_log.sql` via phpMyAdmin
2. Or run the SQL commands manually

### Admin Login Not Working
**Problem**: No admin users exist
**Solution**:
1. Create admin users via `admin/create_admin.php`
2. Or insert admin users directly into the database
3. Make sure email addresses match the allowed admin emails in `auth.php`

## 📁 File Structure

```
byumba/
├── admin/                     # Admin panel
│   ├── database_status.php    # Database status checker
│   ├── test_connection.php    # Connection test suite
│   ├── activities.php         # Admin activities management
│   └── ...
├── config/
│   └── database.php          # Database configuration
├── database/
│   ├── diocese_byumba.sql    # Main database schema
│   └── admin_activity_log.sql # Admin activity logging
└── ...
```

## 🎯 System Features

### Admin Panel Features
- **Dashboard**: Overview with recent admin activities
- **User Management**: Manage diocese members
- **Applications**: Handle certificate requests
- **Meetings**: Schedule and manage meetings
- **Blog**: Content management system
- **Activities**: Monitor admin actions and system usage
- **Notifications**: Send bulk notifications to users

### Security Features
- Role-based access control (Bishop, Admin, Secretary)
- Activity logging for all admin actions
- Session management
- Input sanitization and validation

## 🔍 Testing Your Installation

### 1. Database Connection Test
```
http://localhost/byumba/admin/database_status.php
```

### 2. Full System Test
```
http://localhost/byumba/admin/test_connection.php
```

### 3. Admin Panel Access
```
http://localhost/byumba/admin/
```

### 4. Main Website
```
http://localhost/byumba/
```

## 📞 Getting Help

If you encounter issues:

1. **Check the database status page** first
2. **Review error logs** in XAMPP control panel
3. **Verify file permissions** (especially on uploads folder)
4. **Check PHP error logs** for detailed error information

## 🔄 Regular Maintenance

### Database Backup
1. Use phpMyAdmin to export the database regularly
2. Keep backups of uploaded files and documents

### Activity Log Cleanup
1. Use the admin panel to clear old activity logs
2. Recommended: Keep 90 days of activity history

### Updates
1. Always backup before updating
2. Test updates on a development environment first

## ✅ Success Indicators

Your system is working correctly when:
- ✅ Database status page shows all green checkmarks
- ✅ Admin panel loads without errors
- ✅ You can log in with admin credentials
- ✅ Dashboard shows recent activities
- ✅ All admin functions work properly

## 🎉 You're Ready!

Once all tests pass, your Diocese of Byumba system is ready for use. You can now:
- Create user accounts
- Manage applications and meetings
- Publish blog content
- Monitor system activities
- Send notifications to users

For detailed usage instructions, refer to the admin panel documentation and user guides.
