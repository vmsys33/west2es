<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


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
    if (!isset($_POST['revision_id']) || !isset($_FILES['file']) || !isset($_POST['table1']) || 
        !isset($_POST['table2']) || !isset($_POST['version_no'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }

    // Sanitize and validate inputs
    $revisionId = filter_var($_POST['revision_id'], FILTER_SANITIZE_NUMBER_INT);
    $table1 = htmlspecialchars($_POST['table1'], ENT_QUOTES, 'UTF-8');
    $table2 = htmlspecialchars($_POST['table2'], ENT_QUOTES, 'UTF-8');
    $version_no = filter_var($_POST['version_no'], FILTER_SANITIZE_NUMBER_INT);

    // Validate revision ID
    if (!$revisionId || $revisionId <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid revision ID']);
        exit;
    }

    // Validate version number
    if (!$version_no || $version_no <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid version number']);
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
        't_lr_files_versions', 't_pp_files_versions', 't_rs_files_versions'
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

        // Verify the revision exists
        $stmtCheckRevision = $pdo->prepare("SELECT id, file_path FROM $table2 WHERE id = :revision_id AND version_no = :version_no");
        $stmtCheckRevision->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmtCheckRevision->bindParam(':version_no', $version_no, PDO::PARAM_INT);
        $stmtCheckRevision->execute();
        $existingRevision = $stmtCheckRevision->fetch(PDO::FETCH_ASSOC);

        if (!$existingRevision) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Revision not found']);
            exit;
        }

        // Get file operations variables
        $variables = setFileTableVariables($table1, $file);
        $uploadDir = $variables['uploadDir'];
        $fileName = basename($file['name']);
        $file_path = $variables['file_path'];

        // Check for duplicates in master_files table
        $stmtMaster = $pdo->prepare("SELECT COUNT(*) AS duplicate_count FROM master_files WHERE filename = :filename AND id != :revision_id");
        $stmtMaster->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmtMaster->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmtMaster->execute();
        $duplicateCountInMasterFiles = $stmtMaster->fetch(PDO::FETCH_ASSOC)['duplicate_count'];

        if ($duplicateCountInMasterFiles > 0) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Duplicate file found in master_files table']);
            exit;
        }

        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
                exit;
            }
        }

        // Delete the existing file if it exists
        $oldFilePath = $_SERVER['DOCUMENT_ROOT'] . $existingRevision['file_path'];
        if (file_exists($oldFilePath)) {
            if (!unlink($oldFilePath)) {
                error_log("Failed to delete old file: " . $oldFilePath);
            }
        }

        // Move the new file to the uploads directory
        $newFilePath = $uploadDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file']);
            exit;
        }

        // Calculate file size
        $fileSizeInBytes = filesize($newFilePath);
        if ($fileSizeInBytes === false) {
            $pdo->rollBack();
            // Clean up uploaded file
            if (file_exists($newFilePath)) {
                unlink($newFilePath);
            }
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

        // Update the revision in the versions table
        $stmt = $pdo->prepare("UPDATE $table2 
                               SET file_path = :file_path, filename = :filename, datetime = NOW(), file_size = :file_size 
                               WHERE id = :revision_id AND version_no = :version_no");
        $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->bindParam(':version_no', $version_no, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            $pdo->rollBack();
            // Clean up uploaded file
            if (file_exists($newFilePath)) {
                unlink($newFilePath);
            }
            echo json_encode(['status' => 'error', 'message' => 'Failed to update revision in versions table']);
            exit;
        }

        // Update the revision in master_files table
        $stmtMaster = $pdo->prepare("UPDATE master_files 
                                     SET file_path = :file_path, filename = :filename, file_size = :file_size, datetime = NOW() 
                                     WHERE file_id = :file_id AND table2 = :table2 AND version_no = :version_no");
        $stmtMaster->bindParam(':file_path', $file_path, PDO::PARAM_STR);
        $stmtMaster->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmtMaster->bindParam(':file_size', $fileSize, PDO::PARAM_STR);
        $stmtMaster->bindParam(':file_id', $revisionId, PDO::PARAM_INT);
        $stmtMaster->bindParam(':table2', $table2, PDO::PARAM_STR);
        $stmtMaster->bindParam(':version_no', $version_no, PDO::PARAM_INT);
        
        if (!$stmtMaster->execute()) {
            $pdo->rollBack();
            // Clean up uploaded file
            if (file_exists($newFilePath)) {
                unlink($newFilePath);
            }
            echo json_encode(['status' => 'error', 'message' => 'Failed to update revision in master_files table']);
            exit;
        }

        // Log notification
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        try {
            logNotification($userId, $role, 'replace', "Replaced file with filename: $fileName, Version: $version_no");
        } catch (Exception $e) {
            error_log("Notification logging failed: " . $e->getMessage());
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => 'Revision updated successfully']);

    } catch (Exception $e) {
        // Rollback transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        // Clean up uploaded file if it exists
        if (isset($newFilePath) && file_exists($newFilePath)) {
            unlink($newFilePath);
        }
        
        error_log("Error in update_revision_file.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
