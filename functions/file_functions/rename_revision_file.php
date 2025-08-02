<?php
/**
 * Rename Revision File Handler
 * Handles renaming revision files
 */

// Fix the path to work from different locations
$dbPath = __DIR__ . '/../db_connection.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
} else {
    // Try alternative path
    require_once __DIR__ . '/../../functions/db_connection.php';
}

// Fix the notification helper path
$notificationPath = __DIR__ . '/../notification_helper.php';
if (file_exists($notificationPath)) {
    require_once $notificationPath;
} else {
    // Try alternative path
    require_once __DIR__ . '/../../functions/notification_helper.php';
}

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
    try {
        // Validate required parameters
        if (!isset($_POST['revision_id']) || !isset($_POST['new_filename']) || 
            !isset($_POST['table1']) || !isset($_POST['table2'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            exit;
        }

        // Debug: Log received data
        error_log("Rename revision received data: " . print_r($_POST, true));
        
        // Sanitize and validate inputs
        $revisionId = filter_var($_POST['revision_id'], FILTER_SANITIZE_NUMBER_INT);
        $newFilename = trim($_POST['new_filename']);
        $table1 = trim($_POST['table1']);
        $table2 = trim($_POST['table2']);
        
        // Debug: Log sanitized data
        error_log("Sanitized data - revisionId: $revisionId, newFilename: $newFilename, table1: $table1, table2: $table2");

        // Validate revision ID
        if (!$revisionId || $revisionId <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid revision ID']);
            exit;
        }

        // Validate filename
        if (empty($newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'New filename cannot be empty']);
            exit;
        }

        // Sanitize filename (allow spaces, letters, numbers, dots, hyphens, underscores)
        $newFilename = preg_replace('/[^a-zA-Z0-9._\s-]/', '', $newFilename);
        
        // Clean up multiple consecutive periods
        $newFilename = preg_replace('/\.{2,}/', '.', $newFilename);
        
        // Remove trailing periods
        $newFilename = rtrim($newFilename, '.');
        
        // Clean up multiple spaces
        $newFilename = preg_replace('/\s+/', ' ', $newFilename);
        
        // Trim leading and trailing spaces
        $newFilename = trim($newFilename);
        
        // Trim trailing periods from the new filename
        $newFilename = rtrim($newFilename, '.');
        // Check if the new filename is now empty
        if (empty(trim($newFilename))) {
            echo json_encode(['status' => 'error', 'message' => 'Filename cannot be empty.']);
            exit;
        }
        // Check if the new filename has an extension
        $newFilenameExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
        // If no extension provided, use the original file's extension
        if (empty($newFilenameExtension)) {
            $fileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
            $newFilename = $newFilename . '.' . $fileExtension;
        }
        
        // Final validation - ensure filename has an extension
        if (!preg_match('/\.[a-zA-Z0-9]+$/', $newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'Filename must have a valid extension']);
            exit;
        }

        // Check if the new filename has an extension
        $newFilenameExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
        // New validation: Disallow filenames ending with a period
        if (preg_match('/\.$/', $newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'Filename cannot end with a period.']);
            exit;
        }
        // If no extension provided, use the original file's extension
        if (empty($newFilenameExtension)) {
            $fileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
            $newFilename = $newFilename . '.' . $fileExtension;
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

        // Start transaction
        $pdo->beginTransaction();

        // Get the current revision information
        $stmt = $pdo->prepare("SELECT filename FROM $table2 WHERE id = :revision_id");
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $revision = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log the query and result
        error_log("Looking for revision ID: $revisionId in table: $table2");
        error_log("Revision found: " . ($revision ? 'YES' : 'NO'));
        if ($revision) {
            error_log("Revision data: " . print_r($revision, true));
        }

        if (!$revision) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Revision not found']);
            exit;
        }

        // Get the current file name
        $currentFileName = $revision['filename'];
        
        // Check if the new filename has an extension
        $newFilenameExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
        
        if (empty($newFilenameExtension)) {
            // If no extension provided, use the original file's extension
            $fileExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
            $newFilename = $newFilename . '.' . $fileExtension;
        }
        
        // Final validation - ensure filename has an extension
        if (!preg_match('/\.[a-zA-Z0-9]+$/', $newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'Filename must have a valid extension']);
            exit;
        }

        // Check if new filename already exists in the same table
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM $table2 WHERE filename = :filename AND id != :revision_id");
        $stmt->bindParam(':filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($existingCount > 0) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'A file with this name already exists']);
            exit;
        }

        // Construct file paths based on table name
        $uploadDir = 'uploads/files/' . $table1 . '/';
        $physicalOldPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $currentFileName;
        $physicalNewPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $newFilename;

        // Check if the physical file exists
        if (file_exists($physicalOldPath)) {
            // Rename the physical file
            if (!rename($physicalOldPath, $physicalNewPath)) {
                $pdo->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Failed to rename file on server']);
                exit;
            }
        } else {
            // File doesn't exist physically, but we can still update the database
            // This is normal for some systems where files are stored differently
        }

        // Update the database record
        $stmt = $pdo->prepare("UPDATE $table2 SET filename = :new_filename WHERE id = :revision_id");
        $stmt->bindParam(':new_filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        
        if (!$stmt->execute()) {
            // Rollback file rename if database update fails
            if (file_exists($physicalNewPath)) {
                rename($physicalNewPath, $physicalOldPath);
            }
            $pdo->rollBack();
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Failed to update database: ' . $errorInfo[2]]);
            exit;
        }

        // Log the action
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        
        try {
            logNotification($userId, $role, 'rename_revision', "Renamed revision file from '$currentFileName' to '$newFilename' (Revision ID: $revisionId)");
        } catch (Exception $e) {
            error_log("Notification logging failed: " . $e->getMessage());
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => "Revision file renamed successfully from '$currentFileName' to '$newFilename'"
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Rename revision file error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while renaming the file']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 