# System Functions Reference

## 🔧 West2ES System Functions Documentation

### Overview
This document provides a comprehensive reference for all functions in the West2ES system, organized by category and purpose.

## 📁 File Management Functions

### Core File Operations

#### 1. `add_file.php`
**Location**: `functions/file_functions/add_file.php`
**Purpose**: Handle file uploads to pending status

**Parameters**:
- `$_FILES['fileInput']`: Uploaded file
- `$_POST['fileName']`: User-friendly file name
- `$_POST['table1']`: Main table name
- `$_POST['table2']`: Version table name

**Returns**: JSON response with status and message

**Key Features**:
- File type validation
- File size validation (10MB limit)
- Secure file naming
- Database insertion
- Error handling

#### 2. `delete_file.php`
**Location**: `functions/file_functions/delete_file.php`
**Purpose**: Delete files and all versions

**Parameters**:
- `$fileId`: File ID to delete
- `$table1`: Main table name
- `$table2`: Version table name

**Returns**: JSON response with status

**Key Features**:
- Complete file removal
- Physical file deletion
- Database cleanup
- Transaction safety

#### 3. `edit_filename.php`
**Location**: `functions/file_functions/edit_filename.php`
**Purpose**: Rename file labels

**Parameters**:
- `$fileId`: File ID
- `$newLabel`: New file name
- `$table1`: Main table name

**Returns**: JSON response with status

**Key Features**:
- Database synchronization
- Transaction safety
- Audit logging

#### 4. `add_revision_file.php`
**Location**: `functions/file_functions/add_revision_file.php`
**Purpose**: Add new file versions

**Parameters**:
- `$fileId`: File ID
- `$newFile`: New file upload
- `$table1`: Main table name
- `$table2`: Version table name

**Returns**: JSON response with status

**Key Features**:
- Version increment
- File replacement
- History tracking

#### 5. `delete_revision.php`
**Location**: `functions/file_functions/delete_revision.php`
**Purpose**: Delete specific file versions

**Parameters**:
- `$fileId`: File ID
- `$versionNo`: Version number
- `$table2`: Version table name

**Returns**: JSON response with status

**Key Features**:
- Selective version deletion
- Physical file cleanup
- Database sync

#### 6. `download_file.php`
**Location**: `functions/file_functions/download_file.php`
**Purpose**: Handle file downloads

**Parameters**:
- `$fileId`: File ID
- `$table1`: Main table name
- `$table2`: Version table name

**Returns**: File download

**Key Features**:
- Access validation
- Download tracking
- Security headers

#### 7. `preview_file.php`
**Location**: `functions/file_functions/preview_file.php`
**Purpose**: Preview files in browser

**Parameters**:
- `$fileId`: File ID
- `$table1`: Main table name
- `$table2`: Version table name

**Returns**: File preview

**Key Features**:
- Browser preview
- MIME type detection
- Access control

## 🔐 Authentication Functions

### Login Processing

#### 1. `admin_login_process.php`
**Location**: `functions/admin_login_process.php`
**Purpose**: Process admin login

**Parameters**:
- `$deped_id_no`: 7-digit DepEd ID
- `$password`: User password

**Returns**: JSON response with status

**Key Features**:
- DepEd ID validation
- Password verification
- Session creation
- Role assignment

#### 2. `faculty_login_process.php`
**Location**: `functions/faculty_login_process.php`
**Purpose**: Process faculty login

**Parameters**:
- `$deped_id_no`: 7-digit DepEd ID
- `$password`: User password

**Returns**: JSON response with status

**Key Features**:
- DepEd ID validation
- Password verification
- Session creation
- Role assignment

### User Management

#### 3. `fetch_faculty.php`
**Location**: `functions/fetch_faculty.php`
**Purpose**: Fetch faculty data for display

**Returns**: JSON array of faculty data

**Key Features**:
- User data retrieval
- Photo inclusion
- Sorting by last name
- Role filtering

#### 4. `register_user.php`
**Location**: `functions/register_user.php`
**Purpose**: Register new users

**Parameters**:
- `$userData`: Array of user information
- `$role`: User role (faculty/admin)

**Returns**: JSON response with status

**Key Features**:
- Input validation
- Email verification
- Password hashing
- Duplicate checking

## 📊 System Management Functions

### File Approval

#### 1. `toggleStatus.php`
**Location**: `functions/toggleStatus.php`
**Purpose**: Approve pending files

**Parameters**:
- `$pendingFileId`: Pending file ID

**Returns**: JSON response with status

**Key Features**:
- Database transactions
- Multi-table updates
- Audit logging
- Error handling

### Data Management

#### 2. `delete_selected_files.php`
**Location**: `functions/delete_selected_files.php`
**Purpose**: Bulk delete files

**Parameters**:
- `$fileIds`: Array of file IDs
- `$tableNames`: Array of table names

**Returns**: JSON response with results

**Key Features**:
- Batch processing
- Progress tracking
- Error handling
- Transaction safety

#### 3. `delete_selected_events.php`
**Location**: `functions/delete_selected_events.php`
**Purpose**: Bulk delete events

**Parameters**:
- `$eventIds`: Array of event IDs

**Returns**: JSON response with results

**Key Features**:
- Batch deletion
- Transaction safety
- Audit logging

### Settings Management

#### 4. `update_settings.php`
**Location**: `functions/update_settings.php`
**Purpose**: Update system settings

**Parameters**:
- `$settings`: Array of setting values

**Returns**: JSON response with status

**Key Features**:
- Setting validation
- Database updates
- Cache clearing

## 🔔 Notification Functions

### Notification Management

#### 1. `create_notification.php`
**Location**: `functions/create_notification.php`
**Purpose**: Create system notifications

**Parameters**:
- `$userId`: Target user ID
- `$role`: User role
- `$activityType`: Type of activity
- `$description`: Notification description

**Returns**: Boolean success status

**Key Features**:
- Notification creation
- Role-based targeting
- Activity tracking

#### 2. `mark_notification_read.php`
**Location**: `functions/mark_notification_read.php`
**Purpose**: Mark notifications as read

**Parameters**:
- `$notificationId`: Notification ID

**Returns**: JSON response with status

**Key Features**:
- Read status update
- User feedback

## 📈 Reporting Functions

### Report Generation

#### 1. `generate_report.php`
**Location**: `functions/generate_report.php`
**Purpose**: Generate system reports

**Parameters**:
- `$reportType`: Type of report
- `$dateRange`: Date range for report
- `$filters`: Additional filters

**Returns**: Report data or file

**Key Features**:
- Multiple report types
- Date filtering
- Export options
- PDF generation

#### 2. `export_data.php`
**Location**: `functions/export_data.php`
**Purpose**: Export data to various formats

**Parameters**:
- `$tableName`: Table to export
- `$format`: Export format (CSV, Excel, PDF)
- `$filters`: Data filters

**Returns**: Export file

**Key Features**:
- Multiple formats
- Data filtering
- Large file handling

## 🔧 Utility Functions

### Database Operations

#### 1. `db_connection.php`
**Location**: `functions/db_connection.php`
**Purpose**: Database connection management

**Features**:
- PDO connection
- Error handling
- Connection pooling
- Security configuration

#### 2. `file_operations.php`
**Location**: `functions/file_operations.php`
**Purpose**: File operation utilities

**Functions**:
- `setFileTableVariables()`: Set table variables based on page
- `getFileCategories()`: Get available file categories
- `validateFileType()`: Validate file types
- `generateUniqueFilename()`: Generate unique file names

### Security Functions

#### 3. `csrf.php`
**Location**: `includes/csrf.php`
**Purpose**: CSRF protection

**Functions**:
- `generateCSRFToken()`: Generate CSRF tokens
- `validateCSRFToken()`: Validate CSRF tokens
- `refreshCSRFToken()`: Refresh expired tokens

#### 4. `input_validation.php`
**Location**: `functions/input_validation.php`
**Purpose**: Input validation utilities

**Functions**:
- `sanitizeInput()`: Sanitize user input
- `validateEmail()`: Validate email addresses
- `validateDepEdID()`: Validate DepEd ID format
- `validateFileUpload()`: Validate file uploads

## 📱 Frontend Functions

### JavaScript Functions

#### 1. `search_js.js`
**Location**: `assets/search_js.js`
**Purpose**: Search functionality

**Functions**:
- `performSearch()`: Execute search
- `updateResults()`: Update search results
- `clearSearch()`: Clear search

#### 2. `dataTables_config.js`
**Location**: `assets/dataTables_config.js`
**Purpose**: DataTables configuration

**Features**:
- Responsive configuration
- Custom sorting
- Export options
- Mobile optimization

## 🔄 Workflow Functions

### Approval Workflow

#### 1. `approval_workflow.php`
**Location**: `functions/approval_workflow.php`
**Purpose**: Manage approval workflow

**Functions**:
- `checkApprovalStatus()`: Check file approval status
- `approveFile()`: Approve specific file
- `rejectFile()`: Reject specific file
- `getPendingFiles()`: Get pending files list

### Version Control

#### 2. `version_control.php`
**Location**: `functions/version_control.php`
**Purpose**: Manage file versions

**Functions**:
- `getVersionHistory()`: Get file version history
- `createNewVersion()`: Create new version
- `rollbackVersion()`: Rollback to previous version
- `compareVersions()`: Compare file versions

## 📊 Analytics Functions

### Usage Analytics

#### 1. `analytics.php`
**Location**: `functions/analytics.php`
**Purpose**: System analytics

**Functions**:
- `getFileUsageStats()`: Get file usage statistics
- `getUserActivity()`: Get user activity data
- `getStorageStats()`: Get storage statistics
- `generateAnalyticsReport()`: Generate analytics report

### Audit Logging

#### 2. `audit_log.php`
**Location**: `functions/audit_log.php`
**Purpose**: Audit logging

**Functions**:
- `logActivity()`: Log user activity
- `getAuditTrail()`: Get audit trail
- `exportAuditLog()`: Export audit log
- `cleanupOldLogs()`: Cleanup old audit logs

## 🚨 Error Handling Functions

### Error Management

#### 1. `error_handler.php`
**Location**: `functions/error_handler.php`
**Purpose**: Centralized error handling

**Functions**:
- `logError()`: Log system errors
- `displayError()`: Display user-friendly errors
- `handleException()`: Handle exceptions
- `sendErrorReport()`: Send error reports

#### 2. `validation_handler.php`
**Location**: `functions/validation_handler.php`
**Purpose**: Input validation handling

**Functions**:
- `validateForm()`: Validate form data
- `getValidationErrors()`: Get validation errors
- `displayValidationErrors()`: Display validation errors
- `clearValidationErrors()`: Clear validation errors

## 🔧 Configuration Functions

### System Configuration

#### 1. `config_manager.php`
**Location**: `functions/config_manager.php`
**Purpose**: System configuration management

**Functions**:
- `loadConfig()`: Load system configuration
- `saveConfig()`: Save system configuration
- `getConfigValue()`: Get configuration value
- `setConfigValue()`: Set configuration value

#### 2. `cache_manager.php`
**Location**: `functions/cache_manager.php`
**Purpose**: Cache management

**Functions**:
- `setCache()`: Set cache value
- `getCache()`: Get cache value
- `clearCache()`: Clear cache
- `cacheExists()`: Check if cache exists

## 📋 Function Categories Summary

### Core Operations
- **File Management**: Upload, download, delete, edit, preview
- **User Management**: Login, registration, profile management
- **System Management**: Settings, configuration, maintenance

### Security & Validation
- **Authentication**: Login processing, session management
- **Authorization**: Role-based access control
- **Input Validation**: Data sanitization and validation
- **CSRF Protection**: Cross-site request forgery protection

### Data Management
- **Database Operations**: Connection, queries, transactions
- **File Operations**: File system operations, version control
- **Bulk Operations**: Batch processing, bulk deletions

### User Experience
- **Search & Filter**: Advanced search capabilities
- **Notifications**: System notifications and alerts
- **Responsive Design**: Mobile-friendly interfaces
- **DataTables**: Advanced table functionality

### Analytics & Reporting
- **Usage Analytics**: File usage, user activity
- **Audit Logging**: Activity tracking, audit trails
- **Report Generation**: PDF, Excel, CSV exports
- **Statistics**: System statistics and metrics

### Error Handling
- **Error Management**: Centralized error handling
- **Validation**: Input validation and error display
- **Logging**: Error logging and reporting
- **Recovery**: Error recovery and fallback mechanisms

---

**Last Updated**: August 12, 2025  
**Total Functions**: 50+ functions across 15+ categories  
**Related Files**: All files in `functions/` directory
