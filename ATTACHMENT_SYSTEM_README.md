# Document Attachment System

A comprehensive file upload and management system for the Diocese of Byumba School Administration platform, allowing schools to attach supporting documents to their reports.

## 🎯 Overview

The Document Attachment System provides:

- **Secure File Upload**: Drag-and-drop interface with multiple file support
- **File Type Validation**: Support for common document formats (PDF, DOC, XLS, PPT, TXT, Images)
- **Size Limitations**: 10MB per file, 50MB total per report
- **Access Control**: School-specific file isolation and authentication
- **Download Management**: Secure file downloads with activity logging
- **Integration**: Seamless integration with report creation and viewing

## 📋 Features

### For Schools
- **Drag & Drop Upload**: Modern file upload interface with progress indicators
- **Multiple File Support**: Upload multiple documents simultaneously
- **File Preview**: See selected files before upload with file type icons
- **File Management**: Delete attachments from draft reports
- **Download Access**: Download previously uploaded attachments

### For Administrators
- **File Oversight**: View all attachments across school reports
- **Security Controls**: Secure file storage outside web root
- **Activity Logging**: Track all file upload/download activities
- **Storage Management**: Organized file structure by school

## 🗂️ File Structure

```
uploads/
└── school-reports/
    ├── .htaccess              # Security configuration
    ├── index.php              # Prevent directory listing
    ├── school_1/              # St. Mary's Primary School
    │   ├── index.php
    │   └── [uploaded files]
    ├── school_2/              # Holy Cross Secondary School
    │   ├── index.php
    │   └── [uploaded files]
    └── ...

school-admin/
├── includes/
│   └── file-handler.php       # Core file handling class
├── upload-attachment.php      # AJAX upload endpoint
├── download-attachment.php    # Secure download handler
├── create-report.php          # Enhanced with file upload
└── view-report.php            # Enhanced with attachment display
```

## 🚀 Installation & Setup

### 1. Create Upload Directories
```bash
php create_upload_directories.php
```

This script will:
- Create the main upload directory structure
- Set up security files (.htaccess, index.php)
- Create school-specific subdirectories
- Set appropriate permissions (755)

### 2. Verify Database Schema
The `report_attachments` table should exist with these columns:
- `id` (Primary Key)
- `report_id` (Foreign Key to school_reports)
- `file_name` (Unique filename on server)
- `original_name` (Original filename from user)
- `file_path` (Full path to file)
- `file_size` (File size in bytes)
- `mime_type` (File MIME type)
- `uploaded_by` (Foreign Key to school_users)
- `uploaded_at` (Upload timestamp)

### 3. Test the System
```bash
php test_attachments.php
```

Visit `http://your-domain/test_attachments.php` to verify all components.

## 🔧 Configuration

### File Type Support
Supported file types (configurable in `FileHandler` class):
- **Documents**: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT
- **Images**: JPG, JPEG, PNG

### Size Limits
- **Per File**: 10MB maximum
- **Total per Report**: 50MB maximum
- **Configurable** in `FileHandler` constructor

### Security Settings
- Files stored outside web root (recommended)
- .htaccess prevents direct access
- MIME type validation
- Filename sanitization
- Access control by school ownership

## 💻 Usage

### Creating Reports with Attachments

1. **Navigate to Create Report**: `school-admin/create-report.php`
2. **Fill Report Details**: Complete required fields
3. **Upload Files**: 
   - Drag files to upload zone, or
   - Click "Select Files" button
   - Multiple files supported
4. **Review Files**: See file list with icons and sizes
5. **Submit/Save**: Files are uploaded when report is saved

### Viewing Reports with Attachments

1. **Navigate to View Report**: `school-admin/view-report.php?id=X`
2. **View Attachments**: See all uploaded files with metadata
3. **Download Files**: Click download button for any attachment
4. **Manage Files**: Delete attachments from draft reports

### File Upload Process

```javascript
// Client-side validation
- File type checking
- Size validation
- Duplicate detection

// Server-side processing
- Security validation
- MIME type verification
- Unique filename generation
- Database storage
- Activity logging
```

## 🔒 Security Features

### File Validation
- **Extension Check**: Only allowed file types
- **MIME Type Validation**: Server-side content verification
- **Size Limits**: Prevent oversized uploads
- **Filename Sanitization**: Remove dangerous characters

### Access Control
- **School Isolation**: Files organized by school ID
- **Authentication Required**: Must be logged in to access
- **Ownership Verification**: Can only access own school's files
- **Activity Logging**: All actions tracked

### Storage Security
- **Outside Web Root**: Files not directly accessible
- **.htaccess Protection**: Additional access restrictions
- **Index Files**: Prevent directory listing
- **Secure Downloads**: Authentication required

## 🎨 User Interface

### File Upload Interface
- **Drag & Drop Zone**: Visual feedback for file dropping
- **Progress Indicators**: Upload progress display
- **File Icons**: Type-specific icons (PDF, Word, Excel, etc.)
- **File List**: Preview selected files before upload
- **Error Handling**: Clear error messages for invalid files

### Attachment Display
- **File Cards**: Clean display of attached files
- **Metadata**: File size, upload date, uploaded by
- **Download Buttons**: Easy file access
- **Delete Options**: Remove files from draft reports

## 📱 Responsive Design

- **Mobile Friendly**: Works on all device sizes
- **Touch Support**: Drag & drop works on touch devices
- **Responsive Layout**: Adapts to screen size
- **Accessible**: Keyboard navigation support

## 🔧 API Endpoints

### Upload Files
```
POST /school-admin/upload-attachment.php
Parameters:
- files[] (file array)
- report_id (integer)
- action = 'upload'

Response:
{
  "success": true,
  "files": [
    {
      "id": 123,
      "name": "document.pdf",
      "size": "2.5 MB",
      "icon": "fas fa-file-pdf text-danger"
    }
  ],
  "errors": []
}
```

### Delete Attachment
```
POST /school-admin/upload-attachment.php
Parameters:
- report_id (integer)
- attachment_id (integer)
- action = 'delete'

Response:
{
  "success": true
}
```

### Download File
```
GET /school-admin/download-attachment.php?id=123
Response: File download with proper headers
```

## 🐛 Troubleshooting

### Common Issues

1. **Upload Directory Not Writable**
   - Check directory permissions (755 or 775)
   - Ensure web server can write to uploads directory
   - Run `create_upload_directories.php`

2. **File Upload Fails**
   - Check PHP upload limits (`upload_max_filesize`, `post_max_size`)
   - Verify file type is allowed
   - Check available disk space

3. **Download Not Working**
   - Verify file exists on server
   - Check user has access to the report
   - Review error logs for details

4. **Security Errors**
   - Ensure .htaccess file is present
   - Check file permissions
   - Verify MIME type validation

### Debug Mode
Enable debug mode by adding to any PHP file:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📊 Performance Considerations

- **File Size Monitoring**: Regular cleanup of old files
- **Database Indexing**: Indexes on report_id and uploaded_by
- **Storage Limits**: Monitor disk space usage
- **Backup Strategy**: Include uploads in backup procedures

## 🔄 Maintenance

### Regular Tasks
- Monitor upload directory size
- Clean up orphaned files (files without database records)
- Review activity logs for suspicious activity
- Update file type restrictions as needed

### Backup Procedures
- Include uploads directory in regular backups
- Backup report_attachments table data
- Test restore procedures periodically

---

**Version**: 1.0  
**Last Updated**: September 2025  
**Compatibility**: PHP 7.4+, MySQL 5.7+, Modern Browsers  
**Dependencies**: Bootstrap 5, Font Awesome 6
