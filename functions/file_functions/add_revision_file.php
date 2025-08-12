<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';
require_once '../file_operations.php';
header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required parameters
    if (!isset($_POST['file_id']) || !isset($_FILES['file']) || !isset($_POST['file_name']) || 
        !isset($_POST['table1']) || !isset($_POST['table2'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }

    // Sanitize and validate inputs
    $fileId = filter_var($_POST['file_id'], FILTER_SANITIZE_NUMBER_INT);
    $fileFilename = trim($_POST['file_name']);
    $table1 = htmlspecialchars($_POST['table1'], ENT_QUOTES, 'UTF-8');
    $table2 = htmlspecialchars($_POST['table2'], ENT_QUOTES, 'UTF-8');

    // Validate file ID
    if (!$fileId || $fileId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file ID']);
        exit;
    }

    // Validate filename
    if (empty($fileFilename)) {
        echo json_encode(['status' => 'error', 'message' => 'Filename cannot be empty']);
        exit;
    }

    // Define allowed tables to prevent SQL injection
    $allowedTables = [
        'admin_files', 'aeld_files', 'cild_files', 'if_completed_files', 
        'if_proposals_files', 'lulr_files', 'rp_completed_berf_files', 
        'rp_completed_nonberf_files', 'rp_proposal_berf_files', 
        'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files',
        'admin_files_versions', 'aeld_files_versions', 'cild_files_versions', 
        'if_completed_files_versions', 'if_proposals_files_versions', 'lulr_files_versions', 
        'rp_completed_berf_files_versions', 'rp_completed_nonberf_files_versions', 
        'rp_proposal_berf_files_versions', 'rp_proposal_nonberf_files_versions', 
        't_lr_files_versions', 't_pp_files_versions', 't_rs_files_versions',
        'approved_proposal', 'approved_proposal_versions'
    ];

    if (!in_array($table1, $allowedTables) || !in_array($table2, $allowedTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid table name']);
        exit;
    }

    // Handle file upload
    $file = $_FILES['file'];
    
    // Validate file upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        $errorMessage = isset($uploadErrors[$file['error']]) ? $uploadErrors[$file['error']] : 'Unknown upload error';
        echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        exit;
    }

    // Validate file size (10MB limit)
    $maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($file['size'] > $maxFileSize) {
        echo json_encode(['status' => 'error', 'message' => 'File size exceeds 10MB limit']);
        exit;
    }

    // Validate file type
    $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['status' => 'error', 'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions)]);
        exit;
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Check if a pending file exists in pending_files
        $stmtCheckPending = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM pending_files WHERE file_id = :file_id AND table2 = :table2 AND status = 'pending'");
        $stmtCheckPending->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmtCheckPending->bindParam(':table2', $table2, PDO::PARAM_STR);
        $stmtCheckPending->execute();
        $pendingCount = $stmtCheckPending->fetch(PDO::FETCH_ASSOC)['pending_count'];

        if ($pendingCount > 0) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'You cannot add a new revision while there is a pending file awaiting admin approval.']);
            exit;
        }

        // Verify the original file exists
        $stmtCheckFile = $pdo->prepare("SELECT id FROM $table1 WHERE id = :file_id");
        $stmtCheckFile->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmtCheckFile->execute();
        
        if (!$stmtCheckFile->fetch()) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Original file not found']);
            exit;
        }

        // Get file operations variables
        $variables = setFileTableVariables($table1, $file);
        $uploadDir = $variables['uploadDir'];
        $fileName = basename($file['name']);
        $filePath = $uploadDir . $fileName;
        $file_path = $variables['file_path'];
        $file_path2 = '/west2es/uploads/files/' . $table1 . '/' . $fileName;

        // Check for duplicate filenames in master_files
        $stmt = $pdo->prepare("SELECT COUNT(*) AS duplicate_count FROM master_files WHERE filename = :filename");
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmt->execute();
        $duplicateCount = $stmt->fetch(PDO::FETCH_ASSOC)['duplicate_count'];

        if ($duplicateCount > 0) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'File already exists']);
            exit;
        }

        // Check for duplicate filenames in pending_files
        $stmt = $pdo->prepare("SELECT COUNT(*) AS duplicate_count FROM pending_files WHERE filename = :filename");
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmt->execute();
        $duplicateCount = $stmt->fetch(PDO::FETCH_ASSOC)['duplicate_count'];

        if ($duplicateCount > 0) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'File name already exists in pending files']);
            exit;
        }

        // Fetch the current latest version number for the file
        $stmt = $pdo->prepare("SELECT name, MAX(version_no) AS latest_version FROM $table2 WHERE file_id = :file_id");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $currentVersion = $stmt->fetch(PDO::FETCH_ASSOC);
        $newVersion = $currentVersion ? $currentVersion['latest_version'] + 1 : 1;
        $name = $currentVersion ? $currentVersion['name'] : '';

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
                exit;
            }
        }

        // Move the new file to the uploads directory
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
            exit;
        }

        // Calculate file size
        $fileSizeInBytes = filesize($filePath);
        if ($fileSizeInBytes === false) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to get file size']);
            exit;
        }

        if ($fileSizeInBytes < 1024) {
            $fileSize = $fileSizeInBytes . ' Bytes';
        } elseif ($fileSizeInBytes < 1024 * 1024) {
            $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB';
        } else {
            $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB';
        }

        // Insert the new revision into pending_files
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];

        $stmt = $pdo->prepare("INSERT INTO pending_files (name, filename, user_id, version_no, file_path, download_path, datetime, file_size, table1, table2, file_id) 
                               VALUES (:name, :filename, :user_id, :version_no, :file_path, :download_path, NOW(), :file_size, :table1, :table2, :file_id)");
        $stmt->bindParam(':name', $fileFilename, PDO::PARAM_STR);
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':version_no', $newVersion, PDO::PARAM_INT);
        $stmt->bindParam(':file_path', $file_path2, PDO::PARAM_STR);
        $stmt->bindParam(':download_path', $file_path, PDO::PARAM_STR);
        $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_STR);
        $stmt->bindParam(':table1', $table1, PDO::PARAM_STR);
        $stmt->bindParam(':table2', $table2, PDO::PARAM_STR);
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            $pdo->rollBack();
            // Clean up uploaded file
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            echo json_encode(['status' => 'error', 'message' => 'Failed to save revision to database']);
            exit;
        }

        // Log notification
        try {
            logNotification($userId, $role, 'add_revision', "Added a new pending revision for file ID: $fileId, Filename: $fileName, Version: $newVersion");
        } catch (Exception $e) {
            error_log("Notification logging failed: " . $e->getMessage());
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => 'New revision added successfully and is pending approval.']);

    } catch (Exception $e) {
        // Rollback transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Clean up uploaded file if it exists
        if (isset($filePath) && file_exists($filePath)) {
            unlink($filePath);
        }
        
        error_log("Error in add_revision_file.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
