# File CRUD Operations Documentation

## 📁 File Management CRUD Operations

### Overview
The West2ES system provides complete CRUD (Create, Read, Update, Delete) operations for file management across all categories. Each operation includes proper validation, error handling, and audit logging.

## 🔄 CRUD Operations Overview

### **C**reate Operations
- **File Upload**: Upload new files to pending status
- **File Approval**: Approve pending files to main storage
- **Version Creation**: Create new file versions/revisions

### **R**ead Operations
- **File Listing**: Display files in DataTables
- **File Preview**: Preview files in browser
- **File Download**: Download files to local system
- **File Search**: Search and filter files

### **U**pdate Operations
- **File Rename**: Update file labels/names
- **File Metadata**: Update file information
- **Version Update**: Update file versions

### **D**elete Operations
- **File Deletion**: Delete files and all versions
- **Version Deletion**: Delete specific file versions
- **Bulk Deletion**: Delete multiple files

## 📤 Create Operations

### 1. File Upload Process
**Location**: `functions/file_functions/add_file.php`

**Process Flow**:
1. **Client-Side Validation**
   - File type validation (PDF, DOCX, XLSX, etc.)
   - File size validation (10MB limit)
   - Required field validation

2. **Server-Side Processing**
   - Session validation
   - File upload handling
   - Database insertion
   - File system storage

**Code Example**:
```php
// File upload processing
$file = $_FILES['fileInput'];
$fileName = $_POST['fileName'];
$table1 = $_POST['table1'];
$table2 = $_POST['table2'];

// Validate and process upload
$uploadResult = processFileUpload($file, $fileName, $table1, $table2);
```

### 2. File Approval Process
**Location**: `functions/toggleStatus.php`

**Process Flow**:
1. **Validation**: Check pending file exists
2. **Database Transaction**: Begin transaction
3. **Insert to Main Tables**: Move to category tables
4. **Update Master Files**: Add to central registry
5. **Delete from Pending**: Remove from pending_files
6. **Audit Logging**: Log approval activity
7. **Commit Transaction**: Complete operation

**Code Example**:
```php
try {
    $pdo->beginTransaction();
    
    // Insert into main table
    $stmt = $pdo->prepare("INSERT INTO {$pendingFile['table1']} (filename, user_id) VALUES (?, ?)");
    $stmt->execute([$pendingFile['name'], $pendingFile['user_id']]);
    
    // Insert into version table
    $stmt = $pdo->prepare("INSERT INTO {$pendingFile['table2']} (file_id, version_no, file_path, datetime, file_size, filename) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fileId, $pendingFile['version_no'], $pendingFile['file_path'], $pendingFile['datetime'], $pendingFile['file_size'], $pendingFile['filename']]);
    
    // Update master_files
    $stmt = $pdo->prepare("INSERT INTO master_files (file_id, name, filename, user_id, version_no, file_path, datetime, file_size, table1, table2, download_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fileId, $pendingFile['name'], $pendingFile['filename'], $pendingFile['user_id'], $pendingFile['version_no'], $pendingFile['file_path'], $pendingFile['datetime'], $pendingFile['file_size'], $pendingFile['table1'], $pendingFile['table2'], $pendingFile['download_path']]);
    
    // Delete from pending_files
    $stmt = $pdo->prepare("DELETE FROM pending_files WHERE id = ?");
    $stmt->execute([$pendingFileId]);
    
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    throw $e;
}
```

## 📖 Read Operations

### 1. File Listing
**Location**: `pages/content.php`, `pages/admin_files.php`, etc.

**Features**:
- **DataTables Integration**: Advanced table functionality
- **Responsive Design**: Mobile-friendly display
- **Search & Filter**: Real-time search capabilities
- **Pagination**: Efficient data loading
- **Sorting**: Multi-column sorting

**Code Example**:
```php
// File listing query
$query = "SELECT 
    af.id, 
    af.filename, 
    af.user_id, 
    ud.first_name, 
    afv.version_no,
    ud.last_name, 
    MAX(afv.version_no) AS latest_version, 
    afv.datetime
FROM 
    {$table1} af
LEFT JOIN 
    {$table2} afv ON af.id = afv.file_id
LEFT JOIN 
    user_data ud ON af.user_id = ud.id_no
WHERE 
    1=1
GROUP BY 
    af.id, af.filename, af.user_id, ud.first_name, ud.last_name
ORDER BY 
    af.id DESC";
```

### 2. File Preview
**Location**: `functions/file_functions/preview_file.php`

**Features**:
- **Browser Preview**: Direct file viewing
- **Security Validation**: Access control
- **File Type Support**: PDF, images, text files
- **Download Fallback**: For unsupported types

**Code Example**:
```php
// File preview processing
function previewFile($fileId, $table1, $table2) {
    // Get file information
    $fileInfo = getFileInfo($fileId, $table1, $table2);
    
    // Validate access permissions
    if (!hasAccessPermission($fileInfo)) {
        throw new Exception('Access denied');
    }
    
    // Get file path
    $filePath = $fileInfo['file_path'];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        throw new Exception('File not found');
    }
    
    // Set appropriate headers
    $mimeType = mime_content_type($filePath);
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
    
    // Output file content
    readfile($filePath);
}
```

### 3. File Download
**Location**: `functions/file_functions/download_file.php`

**Features**:
- **Secure Download**: Access validation
- **File Integrity**: Checksum verification
- **Download Tracking**: Usage analytics
- **Bandwidth Control**: Large file handling

**Code Example**:
```php
// File download processing
function downloadFile($fileId, $table1, $table2) {
    // Get file information
    $fileInfo = getFileInfo($fileId, $table1, $table2);
    
    // Validate access permissions
    if (!hasAccessPermission($fileInfo)) {
        throw new Exception('Access denied');
    }
    
    // Get file path
    $filePath = $fileInfo['file_path'];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        throw new Exception('File not found');
    }
    
    // Set download headers
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
    header('Content-Length: ' . filesize($filePath));
    
    // Output file content
    readfile($filePath);
    
    // Log download activity
    logDownloadActivity($fileId, $fileInfo);
}
```

## ✏️ Update Operations

### 1. File Rename/Edit
**Location**: `functions/file_functions/edit_filename.php`

**Features**:
- **Label Update**: Change user-friendly names
- **Database Sync**: Update all related tables
- **Transaction Safety**: ACID compliance
- **Audit Logging**: Track changes

**Code Example**:
```php
// File rename processing
function renameFile($fileId, $newLabel, $table1, $table2) {
    try {
        $pdo->beginTransaction();
        
        // Update the filename in the main table
        $stmt = $pdo->prepare("UPDATE {$table1} SET filename = ? WHERE id = ?");
        if (!$stmt->execute([$newLabel, $fileId])) {
            throw new Exception('Failed to update label in main table');
        }
        
        // Update the filename in master_files table
        $stmtMaster = $pdo->prepare("UPDATE master_files SET filename = ? WHERE file_id = ? AND table1 = ?");
        if (!$stmtMaster->execute([$newLabel, $fileId, $table1])) {
            throw new Exception('Failed to update label in master_files table');
        }
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
```

### 2. File Version Update
**Location**: `functions/file_functions/add_revision_file.php`

**Features**:
- **Version Control**: Incremental versioning
- **File Replacement**: Update existing files
- **History Tracking**: Maintain version history
- **Rollback Support**: Revert to previous versions

**Code Example**:
```php
// File revision processing
function addFileRevision($fileId, $newFile, $table1, $table2) {
    // Get current version
    $currentVersion = getCurrentVersion($fileId, $table2);
    $newVersion = $currentVersion + 1;
    
    // Process new file upload
    $uploadResult = processFileUpload($newFile, $fileName, $table1, $table2);
    
    // Insert new version
    $stmt = $pdo->prepare("INSERT INTO {$table2} (file_id, version_no, file_path, datetime, file_size, filename) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fileId, $newVersion, $uploadResult['file_path'], date('Y-m-d H:i:s'), $uploadResult['file_size'], $uploadResult['filename']]);
    
    // Update master_files
    updateMasterFiles($fileId, $newVersion, $uploadResult);
}
```

## 🗑️ Delete Operations

### 1. Single File Deletion
**Location**: `functions/file_functions/delete_file.php`

**Features**:
- **Complete Removal**: Delete file and all versions
- **Physical Cleanup**: Remove from file system
- **Database Cleanup**: Remove from all tables
- **Transaction Safety**: Rollback on failure

**Code Example**:
```php
// File deletion processing
function deleteFile($fileId, $table1, $table2) {
    try {
        $pdo->beginTransaction();
        
        // Get all file versions
        $versions = getAllFileVersions($fileId, $table2);
        
        // Delete physical files
        foreach ($versions as $version) {
            $filePath = $version['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        // Delete from version table
        $stmt = $pdo->prepare("DELETE FROM {$table2} WHERE file_id = ?");
        $stmt->execute([$fileId]);
        
        // Delete from main table
        $stmt = $pdo->prepare("DELETE FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        
        // Delete from master_files
        $stmt = $pdo->prepare("DELETE FROM master_files WHERE file_id = ? AND table1 = ?");
        $stmt->execute([$fileId, $table1]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
```

### 2. Version Deletion
**Location**: `functions/file_functions/delete_revision.php`

**Features**:
- **Selective Deletion**: Delete specific versions
- **Version Validation**: Ensure version exists
- **Physical Cleanup**: Remove file from disk
- **Database Sync**: Update related records

**Code Example**:
```php
// Version deletion processing
function deleteFileVersion($fileId, $versionNo, $table2) {
    // Get version information
    $versionInfo = getVersionInfo($fileId, $versionNo, $table2);
    
    if (!$versionInfo) {
        throw new Exception('Version not found');
    }
    
    // Delete physical file
    $filePath = $versionInfo['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM {$table2} WHERE file_id = ? AND version_no = ?");
    $stmt->execute([$fileId, $versionNo]);
    
    // Update master_files if this was the latest version
    updateMasterFilesIfNeeded($fileId, $table2);
}
```

### 3. Bulk Deletion
**Location**: `functions/delete_selected_files.php`

**Features**:
- **Multiple Selection**: Delete multiple files
- **Batch Processing**: Efficient bulk operations
- **Progress Tracking**: Show deletion progress
- **Error Handling**: Continue on individual failures

**Code Example**:
```php
// Bulk deletion processing
function deleteMultipleFiles($fileIds, $table1, $table2) {
    $results = [];
    
    foreach ($fileIds as $fileId) {
        try {
            $result = deleteFile($fileId, $table1, $table2);
            $results[] = ['id' => $fileId, 'status' => 'success'];
        } catch (Exception $e) {
            $results[] = ['id' => $fileId, 'status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    return $results;
}
```

## 🔍 Search and Filter Operations

### 1. File Search
**Location**: DataTables integration in all file pages

**Features**:
- **Real-time Search**: Instant results
- **Multi-column Search**: Search across all fields
- **Advanced Filtering**: Date, user, type filters
- **Export Options**: CSV, Excel export

**Code Example**:
```javascript
// DataTables configuration with search
$('#filesTable').DataTable({
    responsive: true,
    processing: true,
    serverSide: false,
    searching: true,
    ordering: true,
    info: true,
    lengthChange: true,
    pageLength: 25,
    order: [[0, 'desc']],
    columnDefs: [
        { targets: [0], orderable: true },
        { targets: [1], orderable: true, searchable: true },
        { targets: [2], orderable: true, searchable: true },
        { targets: [3], orderable: true, searchable: true },
        { targets: [4], orderable: false, searchable: false }
    ]
});
```

### 2. Advanced Filtering
**Features**:
- **Date Range Filter**: Filter by upload date
- **User Filter**: Filter by file owner
- **File Type Filter**: Filter by file extension
- **Status Filter**: Filter by approval status

## 📊 File Statistics and Analytics

### 1. File Usage Tracking
**Location**: `functions/file_functions/track_file_usage.php`

**Features**:
- **Download Counts**: Track file downloads
- **Access Logs**: Record file access
- **User Analytics**: Usage by user
- **Popular Files**: Most accessed files

### 2. Storage Analytics
**Features**:
- **Storage Usage**: Total storage consumption
- **File Distribution**: Files by category
- **Growth Trends**: Storage over time
- **Cleanup Recommendations**: Unused files

## 🔒 Security and Access Control

### 1. Permission Validation
**Features**:
- **Role-Based Access**: Faculty vs Admin permissions
- **Ownership Validation**: Users can only access their files
- **Session Validation**: Secure session management
- **File Path Security**: Prevent directory traversal

### 2. Audit Logging
**Features**:
- **Activity Tracking**: All file operations logged
- **User Accountability**: Track who performed actions
- **Change History**: Complete audit trail
- **Security Monitoring**: Detect suspicious activity

## 🚨 Error Handling

### 1. Common Error Scenarios
- **File Not Found**: 404 errors for missing files
- **Permission Denied**: Access control violations
- **Upload Failures**: File upload errors
- **Database Errors**: Connection or query failures

### 2. Error Response Format
```json
{
    "status": "error",
    "message": "Detailed error description",
    "code": "ERROR_CODE",
    "timestamp": "2025-08-12 10:30:00"
}
```

## 📱 Mobile Responsiveness

### 1. Mobile-Optimized Operations
- **Touch-Friendly**: Optimized for touch devices
- **Responsive Tables**: Adaptive table layouts
- **Mobile Navigation**: Simplified mobile interface
- **Offline Support**: Basic offline functionality

### 2. Progressive Web App Features
- **App-like Experience**: Native app feel
- **Offline Caching**: Cache frequently accessed files
- **Push Notifications**: File status updates
- **Background Sync**: Sync when online

---

**Last Updated**: August 12, 2025  
**Related Files**: 
- `functions/file_functions/` (all CRUD operations)
- `pages/content.php` (file listing)
- `pages/admin_files.php` (admin file management)
- All category-specific file pages
