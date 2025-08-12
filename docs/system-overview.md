# West2ES System Documentation

## 🏫 System Overview

**West2ES** (West 2 Elementary School) is a comprehensive file management system designed for educational institutions. The system provides secure file storage, user management, and administrative controls for managing educational documents and resources.

### 🎯 System Purpose
- **File Management**: Secure storage and organization of educational documents
- **User Management**: Role-based access control for faculty and administrators
- **Document Control**: Version control and approval workflows for files
- **Reporting**: Comprehensive reporting and analytics capabilities

### 🏗️ Architecture
- **Backend**: PHP (Procedural)
- **Database**: MySQL with PDO
- **Frontend**: HTML5, CSS3, Bootstrap 5, jQuery
- **Libraries**: DataTables, SweetAlert2, PHPMailer, TCPDF
- **File Storage**: Local file system with organized directory structure

### 📁 Directory Structure
```
west2es/
├── assets/                 # CSS, JS, and image files
├── functions/             # Core PHP functions
│   ├── file_functions/    # File management operations
│   └── db_connection.php  # Database connection
├── includes/              # Header, footer, navigation
├── modals/               # Bootstrap modals
├── pages/                # Main application pages
├── uploads/              # File storage
│   └── files/            # Organized by category
├── vendor/               # Third-party libraries
└── docs/                 # Documentation (this directory)
```

### 🔐 Security Features
- **Session Management**: Secure user sessions
- **Input Validation**: Server-side and client-side validation
- **SQL Injection Prevention**: PDO prepared statements
- **File Upload Security**: Type and size validation
- **Role-Based Access**: Faculty vs Admin permissions

### 📊 Database Design
- **User Management**: `user_data`, `user_data_details`
- **File Storage**: Category-based tables with version control
- **System Tables**: `notifications`, `events`, `general_setting`
- **Audit Trail**: `file_approval_logs`, `master_files`

### 🔄 Workflow
1. **User Registration**: Faculty/Admin registration with email verification
2. **File Upload**: Files uploaded to pending status
3. **Approval Process**: Admin approval moves files to main storage
4. **Version Control**: Multiple revisions supported
5. **Access Control**: Role-based file access and management

### 🚀 Key Features
- ✅ **Multi-User Support**: Faculty and Admin roles
- ✅ **File Versioning**: Complete revision history
- ✅ **Approval Workflow**: Admin approval system
- ✅ **Mobile Responsive**: Bootstrap 5 responsive design
- ✅ **Real-time Notifications**: System notifications
- ✅ **Search & Filter**: DataTables integration
- ✅ **Export Capabilities**: PDF generation
- ✅ **Audit Logging**: Complete activity tracking

### 📈 System Statistics
- **File Categories**: 13 different document categories
- **User Roles**: 2 (Faculty, Admin)
- **Database Tables**: 25+ tables
- **File Types**: PDF, DOCX, XLSX, and more
- **Max File Size**: 10MB per file

### 🔧 Technical Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Web Server**: Apache/Nginx
- **Browser Support**: Modern browsers (Chrome, Firefox, Safari, Edge)

---

**Last Updated**: August 12, 2025  
**Version**: 2.0  
**Maintainer**: System Administrator
