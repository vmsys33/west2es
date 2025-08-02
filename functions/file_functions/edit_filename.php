<?php
/**
 * Edit Filename Handler
 * Handles updating filenames in the database and file system
 */

// Fix the path to work from different locations
$dbPath = __DIR__ . '/../db_connection.php';
if (file_exists($dbPath)) {
    require_once $dbPath;
} else {
    // Try alternative path
    require_once __DIR__ . '/../../functions/db_connection.php';
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
        if (!isset($_POST['file_id']) || !isset($_POST['new_filename']) || 
            !isset($_POST['table1']) || !isset($_POST['table2'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            exit;
        }

        $fileId = filter_var($_POST['file_id'], FILTER_SANITIZE_NUMBER_INT);
        $newFilename = trim($_POST['new_filename']);
        $table1 = htmlspecialchars($_POST['table1'], ENT_QUOTES, 'UTF-8');
        $table2 = htmlspecialchars($_POST['table2'], ENT_QUOTES, 'UTF-8');

        // Validate filename
        if (empty($newFilename)) {
            echo json_encode(['status' => 'error', 'message' => 'Filename cannot be empty']);
            exit;
        }

        // Get current file information (move this to before any use of $oldFilename)
        $stmt = $pdo->prepare("SELECT filename FROM {$table1} WHERE id = ?");
        $stmt->execute([$fileId]);
        $fileInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$fileInfo) {
            echo json_encode(['status' => 'error', 'message' => 'File not found']);
            exit;
        }
        $oldFilename = $fileInfo['filename'];

        // Sanitize filename (allow spaces, letters, numbers, dots, hyphens, underscores)
        $newFilename = preg_replace('/[^a-zA-Z0-9._\s-]/', '', $newFilename);
        
        // Clean up multiple consecutive periods
        $newFilename = preg_replace('/\.{2,}/', '.', $newFilename);
        
        // Trim trailing periods from the new filename
        $newFilename = rtrim($newFilename, '.');
        // Check if the new filename is now empty
        if (empty(trim($newFilename))) {
            echo json_encode(['status' => 'error', 'message' => 'Filename cannot be empty.']);
            exit;
        }
        // Check if the new filename already has an extension
        $newFilenameExtension = pathinfo($newFilename, PATHINFO_EXTENSION);
        // If no extension provided, use the original file's extension
        if (empty($newFilenameExtension)) {
            $fileExtension = pathinfo($oldFilename, PATHINFO_EXTENSION);
            $newFilenameWithExtension = $newFilename . '.' . $fileExtension;
        } else {
            $newFilenameWithExtension = $newFilename;
        }

        // Check if new filename already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table1} WHERE filename = ? AND id != ?");
        $stmt->execute([$newFilenameWithExtension, $fileId]);
        $existingCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($existingCount > 0) {
            echo json_encode(['status' => 'error', 'message' => 'A file with this name already exists']);
            exit;
        }

        // Construct file paths based on table name
        $uploadDir = 'uploads/files/' . $table1 . '/';
        $physicalOldPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $oldFilename;
        $physicalNewPath = $_SERVER['DOCUMENT_ROOT'] . '/west2es/' . $uploadDir . $newFilenameWithExtension;

        // Check if old file exists
        if (file_exists($physicalOldPath)) {
            // Rename the physical file
            if (!rename($physicalOldPath, $physicalNewPath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to rename physical file']);
                exit;
            }
        } else {
            // File doesn't exist physically, but we can still update the database
            // This is normal for some systems where files are stored differently
        }

        // Update filename in main table
        $stmt = $pdo->prepare("UPDATE {$table1} SET filename = ? WHERE id = ?");
        $stmt->execute([$newFilenameWithExtension, $fileId]);

        // Update filename in versions table if it exists
        try {
            $stmt = $pdo->prepare("UPDATE {$table2} SET filename = ? WHERE file_id = ?");
            $stmt->execute([$newFilenameWithExtension, $fileId]);
        } catch (Exception $e) {
            // Versions table might not exist or have different structure
            // This is not critical for the main functionality
        }

        echo json_encode([
            'status' => 'success', 
            'message' => 'Filename updated successfully from "' . $oldFilename . '" to "' . $newFilenameWithExtension . '"'
        ]);

    } catch (Exception $e) {
        error_log("Error in edit_filename.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while updating the filename']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 