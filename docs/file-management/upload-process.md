# File Upload Process Documentation

## 📁 File Upload System

### Overview
The West2ES file upload system provides secure, organized file storage with automatic categorization, version control, and approval workflows. Files are uploaded to a pending status and require admin approval before becoming available in the main system.

## 🔄 Upload Process Flow

### 1. File Upload Interface
**Location**: `pages/content.php` (Add File Modal)

**Features**:
- Drag-and-drop file selection
- File type validation
- File size limits (10MB max)
- Progress indicators
- Real-time validation feedback

**Code Example**:
```html
<div class="modal fade" id="addFileModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="addFileForm" enctype="multipart/form-data">
                <input type="text" name="fileName" placeholder="Enter file name" required>
                <input type="file" name="fileInput" required>
                <input type="hidden" name="table1" value="<?= $table1 ?>">
                <input type="hidden" name="table2" value="<?= $table2 ?>">
                <button type="submit">Upload File</button>
            </form>
        </div>
    </div>
</div>
```

### 2. Client-Side Validation
**Location**: JavaScript in `pages/content.php`

**Validation Rules**:
```javascript
// File size validation (10MB limit)
const maxSize = 10 * 1024 * 1024; // 10MB in bytes
if (file.size > maxSize) {
    Swal.fire('Error', 'File size exceeds 10MB limit', 'error');
    return false;
}

// File type validation
const allowedTypes = ['pdf', 'docx', 'doc', 'xlsx', 'xls', 'ppt', 'pptx'];
const fileExtension = file.name.split('.').pop().toLowerCase();
if (!allowedTypes.includes(fileExtension)) {
    Swal.fire('Error', 'File type not supported', 'error');
    return false;
}
```

### 3. Server-Side Processing
**Location**: `functions/file_functions/add_file.php`

**Process Steps**:
1. **Input Validation**
2. **File Upload Handling**
3. **Database Insertion**
4. **File System Storage**
5. **Response Generation**

**Code Structure**:
```php
<?php
// 1. Validate session and permissions
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// 2. Validate file upload
if (!isset($_FILES['fileInput']) || $_FILES['fileInput']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'File upload failed']);
    exit;
}

// 3. Process file upload
$file = $_FILES['fileInput'];
$fileName = $_POST['fileName'];
$table1 = $_POST['table1'];
$table2 = $_POST['table2'];

// 4. Validate file type and size
// 5. Generate unique filename
// 6. Move file to upload directory
// 7. Insert into pending_files table
// 8. Return success response
?>
```

### 4. File Storage Organization
**Directory Structure**:
```
uploads/files/
├── admin_files/           # Administrative documents
├── aeld_files/           # Adult Education files
├── cild_files/           # Curriculum Implementation files
├── if_completed_files/   # Implementation completed files
├── if_proposals_files/   # Implementation proposal files
├── lulr_files/          # Learning resources
├── rp_completed_berf_files/     # Research completed BERF
├── rp_completed_nonberf_files/  # Research completed non-BERF
├── rp_proposal_berf_files/      # Research proposal BERF
├── rp_proposal_nonberf_files/   # Research proposal non-BERF
├── t_lr_files/          # Transparency liquidation reports
├── t_pp_files/          # Transparency project proposals
├── t_rs_files/          # Transparency realignment/supplementals
└── approved_proposal/   # Approved proposals
```

### 5. Database Storage
**Tables Used**:
- `pending_files`: Temporary storage for uploaded files
- `master_files`: Central file registry
- Category-specific tables: `admin_files`, `aeld_files`, etc.

**Pending Files Table Structure**:
```sql
CREATE TABLE pending_files (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    download_path VARCHAR(255) NOT NULL,
    file_size VARCHAR(50) NOT NULL,
    user_id INT NOT NULL,
    table1 VARCHAR(100) NOT NULL,
    table2 VARCHAR(100) NOT NULL,
    version_no VARCHAR(10) DEFAULT '1',
    datetime DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
);
```

## 🔒 Security Features

### File Upload Security
- **Type Validation**: Whitelist of allowed file extensions
- **Size Limits**: 10MB maximum file size
- **Virus Scanning**: File content validation
- **Path Traversal Prevention**: Secure file naming

### Access Control
- **Session Validation**: User must be logged in
- **Role-Based Permissions**: Faculty vs Admin access
- **Ownership Validation**: Users can only upload to their account

### Data Protection
- **SQL Injection Prevention**: PDO prepared statements
- **XSS Prevention**: Input sanitization
- **File System Security**: Secure directory permissions

## 📊 File Processing

### File Size Calculation
**Location**: `functions/file_functions/add_file.php`

```php
// Convert file size to human-readable format
$fileSizeInBytes = filesize($filePath);
if ($fileSizeInBytes < 1024) {
    $fileSize = $fileSizeInBytes . ' Bytes';
} elseif ($fileSizeInBytes < 1024 * 1024) {
    $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB';
} else {
    $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB';
}
```

### File Naming Convention
- **Original Name**: Preserved for display
- **System Name**: Unique identifier with timestamp
- **Extension**: Preserved from original file

**Example**:
- Original: `MISSION_VISION.pdf`
- System: `mission_vision_1754267569.pdf`

## 🚨 Error Handling

### Common Upload Errors
- "File size exceeds 10MB limit"
- "File type not supported"
- "Upload directory not writable"
- "Database connection error"
- "Invalid file category"

### Error Response Format
```json
{
    "status": "error",
    "message": "Detailed error description",
    "code": "ERROR_CODE"
}
```

## 📱 User Experience

### Progress Indicators
- Upload progress bar
- Real-time status updates
- Success/error notifications
- File preview capabilities

### Responsive Design
- Mobile-friendly upload interface
- Touch-optimized file selection
- Adaptive modal sizing

## 🔧 Configuration

### File Upload Settings
**Location**: `functions/file_operations.php`

```php
// File upload configuration
$maxFileSize = 10 * 1024 * 1024; // 10MB
$allowedExtensions = ['pdf', 'docx', 'doc', 'xlsx', 'xls', 'ppt', 'pptx'];
$uploadBasePath = '../../uploads/files/';
```

### Directory Permissions
- **Upload Directories**: 755 (rwxr-xr-x)
- **Files**: 644 (rw-r--r--)
- **Owner**: Web server user (www-data, apache, etc.)

## 📈 Performance Optimization

### Upload Optimization
- **Chunked Uploads**: Large file handling
- **Compression**: Automatic file compression
- **Caching**: File metadata caching
- **Async Processing**: Background file processing

### Storage Optimization
- **Deduplication**: Prevent duplicate file storage
- **Cleanup**: Automatic temporary file cleanup
- **Archiving**: Old file archiving system

---

**Last Updated**: August 12, 2025  
**Related Files**: 
- `functions/file_functions/add_file.php`
- `functions/file_operations.php`
- `pages/content.php` (upload modal)
- `modals/add_file_modal.php`
