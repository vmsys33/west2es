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

        // Get the current revision info
        $stmt = $pdo->prepare("SELECT filename FROM $table2 WHERE id = :revision_id");
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $revision = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$revision) {
            echo json_encode(['status' => 'error', 'message' => 'Revision not found.']);
            exit;
        }
        $currentFileName = $revision['filename'];

        // Automatically append the original extension if missing
        $originalExtension = pathinfo($currentFileName, PATHINFO_EXTENSION);
        $newExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
        if (empty($newExtension) && !empty($originalExtension)) {
            $newFilename .= '.' . $originalExtension;
        }

        // Automatically convert all spaces (including multiple spaces) to underscores
        $newFilename = preg_replace('/\s+/', '_', $newFilename);

        // Validate new filename (must not be empty, must have extension)
        $newFilename = trim($newFilename);
        if (empty($newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'Filename cannot be empty.']);
            exit;
        }
        if (!preg_match('/\.[a-zA-Z0-9]+$/', $newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'Filename must have a valid extension.']);
            exit;
        }

        // Check for duplicate filename in the revision table
        $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM $table2 WHERE filename = :filename AND id != :revision_id");
        $stmt->bindParam(':filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        if ($existingCount > 0) {
            echo json_encode(['status' => 'error', 'message' => 'A file with this name already exists.']);
            exit;
        }

        // Prepare file paths
        $uploadDir = 'uploads/files/' . $table1 . '/';
        $physicalOldPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $currentFileName;
        $physicalNewPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $newFilename;

        $fileRenameWarning = '';
        if (file_exists($physicalOldPath)) {
            if (!rename($physicalOldPath, $physicalNewPath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to rename file on server.']);
                exit;
            }
        } else {
            $fileRenameWarning = 'Physical file was not found, but the database was updated.';
        }

        // Build the new file path and download path for the revision
        $newFilePath = 'uploads/files/' . $table1 . '/' . $newFilename;
        $newDownloadPath = $newFilePath; // Adjust if your download path differs

        // Update the database
        $stmt = $pdo->prepare("UPDATE $table2 SET filename = :new_filename, file_path = :file_path, download_path = :download_path WHERE id = :revision_id");
        $stmt->bindParam(':new_filename', $newFilename, PDO::PARAM_STR);
        $stmt->bindParam(':file_path', $newFilePath, PDO::PARAM_STR);
        $stmt->bindParam(':download_path', $newDownloadPath, PDO::PARAM_STR);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update database.']);
            exit;
        }

        // Also update the master_files table if a matching file_id exists
        if (isset($_POST['file_id'])) {
            $fileId = intval($_POST['file_id']);
            // Build the new file path and download path
            $newFilePath = 'uploads/files/' . $table1 . '/' . $newFilename;
            $newDownloadPath = $newFilePath; // Adjust if your download path differs
            $stmt = $pdo->prepare("UPDATE master_files SET filename = :new_filename, file_path = :file_path, download_path = :download_path WHERE file_id = :file_id");
            $stmt->bindParam(':new_filename', $newFilename, PDO::PARAM_STR);
            $stmt->bindParam(':file_path', $newFilePath, PDO::PARAM_STR);
            $stmt->bindParam(':download_path', $newDownloadPath, PDO::PARAM_STR);
            $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
            $stmt->execute();
        }

        $successMessage = "Revision file renamed successfully from '$currentFileName' to '$newFilename'";
        if ($fileRenameWarning) {
            $successMessage .= ' (Warning: ' . $fileRenameWarning . ')';
        }
        echo json_encode([
            'status' => 'success',
            'message' => $successMessage
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Rename revision file error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while renaming the file: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 