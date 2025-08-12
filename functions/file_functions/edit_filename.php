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
        if (!isset($_POST['file_id']) || !isset($_POST['filename']) || 
            !isset($_POST['table1'])) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            exit;
        }
        $fileId = filter_var($_POST['file_id'], FILTER_SANITIZE_NUMBER_INT);
        $newLabel = trim($_POST['filename']);
        $table1 = htmlspecialchars($_POST['table1'], ENT_QUOTES, 'UTF-8');
        // Validate label
        if (empty($newLabel)) {
            echo json_encode(['status' => 'error', 'message' => 'Label cannot be empty']);
            exit;
        }
        // Start transaction for data consistency
        $pdo->beginTransaction();
        
        try {
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
            
            // Commit transaction
            $pdo->commit();
            
            echo json_encode(['status' => 'success', 'message' => 'Label updated successfully in all tables']);
            
        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to update label: ' . $e->getMessage()]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 