<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';
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
    if (!isset($_POST['file_id']) || !isset($_POST['file_table1']) || 
        !isset($_POST['file_table2']) || !isset($_POST['file_version'])) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }

    // Sanitize and validate inputs
    $file_id = filter_var($_POST['file_id'], FILTER_SANITIZE_NUMBER_INT);
    $fileTable1 = htmlspecialchars($_POST['file_table1'], ENT_QUOTES, 'UTF-8');
    $fileTable2 = htmlspecialchars($_POST['file_table2'], ENT_QUOTES, 'UTF-8');
    $fileVersion = filter_var($_POST['file_version'], FILTER_SANITIZE_NUMBER_INT);

    // Validate file ID
    if (!$file_id || $file_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file ID']);
        exit;
    }

    // Validate version number
    if (!$fileVersion || $fileVersion <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid version number']);
        exit;
    }

    // Define allowed tables to prevent SQL injection
    $allowedTables = [
        'admin_files', 'aeld_files', 'cild_files', 'if_completed_files', 
        'if_proposals_files', 'lulr_files', 'rp_completed_berf_files', 
        'rp_completed_nonberf_files', 'rp_proposal_berf_files', 
        'rp_proposal_nonberf_files', 't_lr_files', 't_pp_files', 't_rs_files',
        'approved_proposal',
        'admin_files_versions', 'aeld_files_versions', 'cild_files_versions', 
        'if_completed_files_versions', 'if_proposals_files_versions', 'lulr_files_versions', 
        'rp_completed_berf_files_versions', 'rp_completed_nonberf_files_versions', 
        'rp_proposal_berf_files_versions', 'rp_proposal_nonberf_files_versions', 
        't_lr_files_versions', 't_pp_files_versions', 't_rs_files_versions',
        'approved_proposal_versions'
    ];

    if (!in_array($fileTable1, $allowedTables) || !in_array($fileTable2, $allowedTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid table name']);
        exit;
    }

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Verify the revision exists and get file information
        $stmt = $pdo->prepare("SELECT id, filename, file_path FROM $fileTable2 WHERE file_id = :file_id AND version_no = :fileVersion");
        $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
        $stmt->bindParam(':fileVersion', $fileVersion, PDO::PARAM_INT);
        $stmt->execute();
        $revision = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$revision) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Revision not found']);
            exit;
        }

        // Check if this is the latest version (only allow deletion of latest version)
        $stmtLatest = $pdo->prepare("SELECT MAX(version_no) as max_version FROM $fileTable2 WHERE file_id = :file_id");
        $stmtLatest->bindParam(':file_id', $file_id, PDO::PARAM_INT);
        $stmtLatest->execute();
        $latestVersion = $stmtLatest->fetch(PDO::FETCH_ASSOC)['max_version'];

        if ($fileVersion != $latestVersion) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Only the latest version can be deleted']);
            exit;
        }

        // Check if this is version 1 (original file - should not be deleted)
        if ($fileVersion == 1) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Cannot delete the original file (version 1)']);
            exit;
        }

        // Delete the physical file - try multiple possible paths
        $possiblePaths = [
            $_SERVER['DOCUMENT_ROOT'] . $revision['file_path'],
            '../../uploads/files/' . str_replace('_versions', '', $fileTable2) . '/' . $revision['filename'],
            '../uploads/files/' . str_replace('_versions', '', $fileTable2) . '/' . $revision['filename'],
            'uploads/files/' . str_replace('_versions', '', $fileTable2) . '/' . $revision['filename']
        ];
        
        $fileDeleted = false;
        foreach ($possiblePaths as $physicalFilePath) {
            if (file_exists($physicalFilePath)) {
                if (unlink($physicalFilePath)) {
                    $fileDeleted = true;
                    error_log("Successfully deleted revision file: $physicalFilePath");
                    break;
                } else {
                    error_log("Failed to delete revision file: $physicalFilePath");
                }
            }
        }
        
        if (!$fileDeleted) {
            error_log("Could not find or delete revision file: " . $revision['filename']);
            // Continue with database deletion even if physical file deletion fails
        }

        // Delete from the versions table
        $stmtDeleteVersion = $pdo->prepare("DELETE FROM $fileTable2 WHERE file_id = :file_id AND version_no = :fileVersion");
        $stmtDeleteVersion->bindParam(':file_id', $file_id, PDO::PARAM_INT);
        $stmtDeleteVersion->bindParam(':fileVersion', $fileVersion, PDO::PARAM_INT);
        
        if (!$stmtDeleteVersion->execute()) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete revision from versions table']);
            exit;
        }

        // Delete from master_files table
        $stmtDeleteMaster = $pdo->prepare("DELETE FROM master_files WHERE file_id = :file_id AND table2 = :table2 AND version_no = :fileVersion");
        $stmtDeleteMaster->bindParam(':file_id', $file_id, PDO::PARAM_INT);
        $stmtDeleteMaster->bindParam(':table2', $fileTable2, PDO::PARAM_STR);
        $stmtDeleteMaster->bindParam(':fileVersion', $fileVersion, PDO::PARAM_INT);
        
        if (!$stmtDeleteMaster->execute()) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete revision from master_files table']);
            exit;
        }

        // Log notification
        $userId = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        try {
            logNotification($userId, $role, 'delete', "Deleted revision: {$revision['filename']}, Version: $fileVersion, File ID: $file_id");
        } catch (Exception $e) {
            error_log("Notification logging failed: " . $e->getMessage());
        }

        // Commit transaction
        $pdo->commit();

        echo json_encode([
            'status' => 'success', 
            'message' => "Revision '{$revision['filename']}' (Version $fileVersion) deleted successfully"
        ]);

    } catch (Exception $e) {
        // Rollback transaction
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Error in delete_revision.php: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while deleting the revision: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
