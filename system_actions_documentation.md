# 🏫 West2ES System - Complete Actions Documentation

## 📋 **System Overview**
This document outlines all actions and functionalities available in the West2ES (West 2 Elementary School) File Management System based on the sidebar menu structure.

---

## 🏠 **1. DASHBOARD**

### **1.1 Overview**
- **Action**: View system statistics and metrics
- **Features**:
  - Total files count across all categories
  - Total events count
  - Pending users count (with clickable link)
  - Pending files count (with clickable link)
  - Total file versions count
  - Notifications count (with clickable link)
  - Total users count

### **1.2 Pending Users** *(Admin Only)*
- **Action**: Manage user registration approvals
- **Features**:
  - View list of pending user registrations
  - Approve user accounts
  - Reject user accounts
  - View user details (DepEd ID, names, email)
  - Real-time badge notifications

### **1.3 Pending Files** *(Admin Only)*
- **Action**: Review and approve uploaded files
- **Features**:
  - View list of pending file uploads
  - Approve files for publication
  - Reject files with reasons
  - View file details (name, version, uploader, size)
  - Real-time badge notifications

### **1.4 Notifications**
- **Action**: Manage system notifications
- **Features**:
  - View all system notifications
  - Mark notifications as read
  - Real-time badge count
  - Auto-mark notifications when accessed

---

## 📅 **2. EVENT MANAGEMENT**

### **2.1 Event Management**
- **Action**: Manage school events and activities
- **Features**:
  - **Add Event**: Create new events with name, description, location, date/time
  - **Edit Event**: Modify existing event details
  - **Delete Event**: Remove events (Admin only)
  - **View Events**: List all events in responsive table
  - **Event Details**: Name, description, location, date/time
  - **Role-based Access**: Faculty can view, Admin can manage

---

## 📁 **3. FILE MANAGEMENT**

### **3.1 Administrative Files**
- **Action**: Manage administrative documents
- **File Operations**:
  - **Upload Files**: Add new administrative documents
  - **Preview Files**: View file content and revisions
  - **Edit Files**: Update file content and add revisions
  - **Rename Files**: Change file labels/names
  - **Add Revisions**: Upload new versions of files
  - **Delete Files**: Remove files and all revisions (Admin only)
  - **Download Files**: Download files for offline use
  - **File History**: Track all versions and changes

### **3.2 Curriculum Implementation and Learning Delivery (CILD)**
- **Action**: Manage curriculum-related documents
- **Same Operations**: All file management operations as above

### **3.3 Localization and Utilization of Learning Resources (LULR)**
- **Action**: Manage learning resource documents
- **Same Operations**: All file management operations as above

### **3.4 Assessment/Evaluation of Learner's Development (AELD)**
- **Action**: Manage assessment and evaluation documents
- **Same Operations**: All file management operations as above

### **3.5 Approved Proposal**
- **Action**: Manage approved proposal documents
- **Same Operations**: All file management operations as above

---

## 💡 **4. INNOVATION FILES**

### **4.1 Proposals**
- **Action**: Manage innovation proposal documents
- **Same Operations**: All file management operations as above

### **4.2 Completed**
- **Action**: Manage completed innovation project documents
- **Same Operations**: All file management operations as above

---

## 📚 **5. RESEARCH PAPERS**

### **5.1 Proposals**
#### **5.1.1 BERF (Basic Education Research Fund)**
- **Action**: Manage BERF research proposal documents
- **Same Operations**: All file management operations as above

#### **5.1.2 Non-BERF**
- **Action**: Manage non-BERF research proposal documents
- **Same Operations**: All file management operations as above

### **5.2 Completed**
#### **5.2.1 BERF (Basic Education Research Fund)**
- **Action**: Manage completed BERF research documents
- **Same Operations**: All file management operations as above

#### **5.2.2 Non-BERF**
- **Action**: Manage completed non-BERF research documents
- **Same Operations**: All file management operations as above

---

## 📊 **6. TRANSPARENCY**

### **6.1 Liquidation Reports**
- **Action**: Manage financial liquidation report documents
- **Same Operations**: All file management operations as above

### **6.2 Project Proposals**
- **Action**: Manage transparency project proposal documents
- **Same Operations**: All file management operations as above

### **6.3 Realignment and Supplementals**
- **Action**: Manage budget realignment and supplemental documents
- **Same Operations**: All file management operations as above

---

## 👥 **7. USER MANAGEMENT**

### **7.1 Teacher's Profile** *(Admin Only)*
- **Action**: Manage faculty member profiles
- **Features**:
  - **View Faculty List**: Display all faculty members in responsive table
  - **Faculty Details**: Photo, DepEd ID, names, status, email
  - **Export to Excel**: Download faculty data as Excel file
  - **Profile Photos**: View and manage faculty photos
  - **Status Management**: Active, inactive, pending statuses
  - **Responsive Design**: Mobile-friendly table with DataTable features

### **7.2 My Profile** *(Faculty Only)*
- **Action**: Manage personal profile information
- **Features**:
  - **Edit Personal Info**: Update names, email, DepEd ID
  - **Upload Photo**: Change profile picture
  - **Status Management**: View and update account status
  - **Role Information**: View assigned role
  - **Profile Validation**: 7-digit DepEd ID validation

---

## 📈 **8. REPORT MANAGEMENT** *(Admin Only)*

### **8.1 Report Generation**
- **Action**: Generate comprehensive system reports
- **Features**:
  - **Date Range Reports**: Generate reports for specific date ranges
  - **File Category Reports**: Reports by file category
  - **User Activity Reports**: Track user uploads and activities
  - **PDF Export**: Download reports in PDF format
  - **Comprehensive Data**: File details, versions, uploaders, timestamps
  - **All Categories**: Reports for all file categories in the system

---

## 🎯 **9. MISSION & VISION**

### **9.1 Mission & Vision Management**
- **Action**: Manage school mission and vision statements
- **Features**:
  - **View Mission**: Display current school mission
  - **View Vision**: Display current school vision
  - **Edit Mission**: Update school mission statement
  - **Edit Vision**: Update school vision statement
  - **Save Changes**: Persist updates to database

---

## ⚙️ **10. SETTINGS** *(Admin Only)*

### **10.1 General Settings**
- **Action**: Configure system-wide settings
- **Features**:
  - **Website Name**: Set system display name
  - **Email Address**: Configure system email
  - **School Logo**: Upload and manage school logo
  - **Admin Name**: Set administrator display name

### **10.2 Admin Profile**
- **Action**: Manage administrator profile
- **Features**:
  - **Personal Information**: Update admin names, email, DepEd ID
  - **Profile Photo**: Upload and manage admin photo
  - **Account Details**: View and edit admin account information

---

## 🔐 **11. AUTHENTICATION**

### **11.1 Logout**
- **Action**: Securely end user session
- **Features**:
  - **Session Cleanup**: Clear all session data
  - **Security**: Proper session termination
  - **Redirect**: Return to login page

---

## 📱 **12. SYSTEM FEATURES**

### **12.1 Responsive Design**
- **Mobile Optimization**: All pages work on mobile devices
- **DataTable Responsive**: Tables adapt to screen size
- **Touch-Friendly**: Optimized for touch interfaces

### **12.2 File Management Features**
- **Version Control**: Track file revisions and history
- **File Preview**: View files without downloading
- **Download Management**: Secure file downloads
- **File Validation**: Size and format restrictions
- **Duplicate Prevention**: Prevent duplicate file uploads

### **12.3 Security Features**
- **Role-Based Access**: Different permissions for Admin and Faculty
- **Session Management**: Secure user sessions
- **Input Validation**: Sanitize all user inputs
- **File Upload Security**: Validate file types and sizes
- **SQL Injection Prevention**: Prepared statements

### **12.4 Notification System**
- **Real-time Badges**: Live notification counts
- **Auto-marking**: Mark notifications as read automatically
- **Persistent Storage**: Store notifications in database

### **12.5 Data Management**
- **Master Files Table**: Centralized file tracking
- **Transaction Support**: Data consistency across operations
- **Error Handling**: Comprehensive error management
- **Logging**: System activity logging

---

## 🔄 **13. WORKFLOW PROCESSES**

### **13.1 File Upload Workflow**
1. **Upload**: User uploads file to pending_files table
2. **Review**: Admin reviews pending file
3. **Approval**: Admin approves/rejects file
4. **Publication**: Approved files moved to main tables
5. **Master Files**: Entry created in master_files table

### **13.2 User Registration Workflow**
1. **Registration**: User registers with DepEd ID
2. **Validation**: System validates 7-digit DepEd ID
3. **Pending Status**: User placed in pending status
4. **Admin Review**: Admin reviews registration
5. **Approval**: Admin approves/rejects account
6. **Activation**: Approved users can access system

### **13.3 File Revision Workflow**
1. **Add Revision**: User uploads new version
2. **Version Tracking**: System increments version number
3. **History Maintenance**: Previous versions preserved
4. **Master Files Update**: master_files table updated
5. **Notification**: System notifies relevant users

---

## 📊 **14. STATISTICS & ANALYTICS**

### **14.1 Dashboard Metrics**
- Total files across all categories
- Total events in system
- Pending approvals (users and files)
- Active notifications
- Total registered users
- File version counts

### **14.2 Report Analytics**
- File upload trends by date range
- User activity analysis
- Category-wise file distribution
- Version history tracking
- Upload frequency patterns

---

## 🛠️ **15. TECHNICAL CAPABILITIES**

### **15.1 File Support**
- **Formats**: PDF, DOCX, XLSX, and other common formats
- **Size Limits**: Up to 10MB per file
- **Storage**: Organized folder structure
- **Backup**: File backup and recovery

### **15.2 Database Management**
- **Tables**: Multiple specialized tables for different file categories
- **Relationships**: Proper foreign key relationships
- **Indexing**: Optimized database performance
- **Transactions**: ACID compliance for data integrity

### **15.3 User Interface**
- **Bootstrap**: Modern responsive framework
- **DataTables**: Advanced table functionality
- **SweetAlert2**: Enhanced user notifications
- **Font Awesome**: Professional iconography
- **AJAX**: Dynamic content loading

---

## 🔧 **16. MAINTENANCE & ADMINISTRATION**

### **16.1 System Maintenance**
- **File Cleanup**: Remove orphaned files
- **Database Optimization**: Regular maintenance
- **Log Management**: System activity logs
- **Backup Management**: Regular data backups

### **16.2 User Administration**
- **Account Management**: Create, edit, delete users
- **Role Assignment**: Assign admin/faculty roles
- **Status Management**: Active/inactive/pending statuses
- **Profile Management**: User profile administration

---

*This documentation covers all actions and functionalities available in the West2ES File Management System based on the current sidebar menu structure and codebase analysis.*
