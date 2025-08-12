<?php
/**
 * Delete All Files Data
 * ADMINISTRATIVE FUNCTION - USE WITH EXTREME CAUTION
 * This script deletes all data from all file tables and their corresponding physical files
 */

require_once 'db_connection.php';
require_once 'notification_helper.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Admin privileges required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $confirmation = $_POST['confirmation'] ?? '';
    $deletePhysicalFiles = isset($_POST['delete_physical_files']) ? true : false;
    
    // Require explicit confirmation
    if ($confirmation !== 'DELETE_ALL_FILES_CONFIRM') {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Confirmation code required. Please type "DELETE_ALL_FILES_CONFIRM" to proceed.'
        ]);
        exit;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        $deletedCounts = [];
        $errors = [];
        
        // Define all file tables and their corresponding version tables
        $fileTables = [
            'admin_files' => 'admin_files_versions',
            'aeld_files' => 'aeld_files_versions',
            'cild_files' => 'cild_files_versions',
            'if_completed_files' => 'if_completed_files_versions',
            'if_proposals_files' => 'if_proposals_files_versions',
            'lulr_files' => 'lulr_files_versions',
            'rp_completed_berf_files' => 'rp_completed_berf_files_versions',
            'rp_completed_nonberf_files' => 'rp_completed_nonberf_files_versions',
            'rp_proposal_berf_files' => 'rp_proposal_berf_files_versions',
            'rp_proposal_nonberf_files' => 'rp_proposal_nonberf_files_versions',
            't_lr_files' => 't_lr_files_versions',
            't_pp_files' => 't_pp_files_versions',
            't_rs_files' => 't_rs_files_versions',
            'approved_proposal' => 'approved_proposal_versions'
        ];
        
        // Also include other related tables (excluding user_data and system tables)
        $relatedTables = [
            'pending_files',
            'master_files',
            'file_preview_mapping'
        ];
        
        // IMPORTANT: Tables that should NEVER be deleted
        $protectedTables = [
            'user_data',           // User accounts and login info
            'general_setting',     // System settings
            'notifications',       // System notifications
            'file_approval_logs'   // Audit logs
        ];
        
        // Process each file table
        foreach ($fileTables as $mainTable => $versionTable) {
            try {
                // Check if tables exist
                $stmt = $pdo->query("SHOW TABLES LIKE '$mainTable'");
                if ($stmt->rowCount() == 0) {
                    $errors[] = "Table $mainTable does not exist";
                    continue;
                }
                
                $stmt = $pdo->query("SHOW TABLES LIKE '$versionTable'");
                if ($stmt->rowCount() == 0) {
                    $errors[] = "Table $versionTable does not exist";
                    continue;
                }
                
                // Get file paths before deletion (for physical file deletion)
                $filePaths = [];
                if ($deletePhysicalFiles) {
                    $stmt = $pdo->prepare("SELECT file_path FROM $versionTable WHERE file_path IS NOT NULL AND file_path != ''");
                    $stmt->execute();
                    $filePaths = $stmt->fetchAll(PDO::FETCH_COLUMN);
                }
                
                // Delete from version table first (due to foreign key constraints)
                $stmt = $pdo->prepare("DELETE FROM $versionTable");
                $stmt->execute();
                $versionDeleted = $stmt->rowCount();
                
                // Delete from main table
                $stmt = $pdo->prepare("DELETE FROM $mainTable");
                $stmt->execute();
                $mainDeleted = $stmt->rowCount();
                
                $deletedCounts[$mainTable] = [
                    'main_table' => $mainDeleted,
                    'version_table' => $versionDeleted,
                    'total' => $mainDeleted + $versionDeleted
                ];
                
                // Delete physical files if requested
                if ($deletePhysicalFiles && !empty($filePaths)) {
                    $physicalDeleted = 0;
                    foreach ($filePaths as $filePath) {
                        $fullPath = __DIR__ . '/../uploads/files/' . $filePath;
                        if (file_exists($fullPath)) {
                            if (unlink($fullPath)) {
                                $physicalDeleted++;
                            } else {
                                $errors[] = "Failed to delete physical file: $filePath";
                            }
                        }
                    }
                    $deletedCounts[$mainTable]['physical_files'] = $physicalDeleted;
                }
                
            } catch (Exception $e) {
                $errors[] = "Error processing $mainTable: " . $e->getMessage();
            }
        }
        
        // Process related tables
        foreach ($relatedTables as $table) {
            try {
                // Skip if table is in protected list
                if (in_array($table, $protectedTables)) {
                    $errors[] = "Skipped protected table: $table";
                    continue;
                }
                
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $pdo->prepare("DELETE FROM $table");
                    $stmt->execute();
                    $deletedCounts[$table] = $stmt->rowCount();
                }
            } catch (Exception $e) {
                $errors[] = "Error processing $table: " . $e->getMessage();
            }
        }
        
        // Log the action
        $adminId = $_SESSION['user_id'] ?? 0;
        $adminName = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
        
        try {
            logNotification($adminId, 'admin', 'delete_all', 
                "($adminName) deleted ALL files data from all tables. Physical files deleted: " . ($deletePhysicalFiles ? 'Yes' : 'No'));
        } catch (Exception $e) {
            error_log("Failed to log delete all action: " . $e->getMessage());
        }
        
        // Commit transaction
        $pdo->commit();
        
        // Calculate totals
        $totalMainRecords = 0;
        $totalVersionRecords = 0;
        $totalPhysicalFiles = 0;
        
        foreach ($deletedCounts as $table => $counts) {
            if (is_array($counts)) {
                $totalMainRecords += $counts['main_table'] ?? 0;
                $totalVersionRecords += $counts['version_table'] ?? 0;
                $totalPhysicalFiles += $counts['physical_files'] ?? 0;
            } else {
                $totalMainRecords += $counts;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'All file data has been deleted successfully',
            'summary' => [
                'total_main_records' => $totalMainRecords,
                'total_version_records' => $totalVersionRecords,
                'total_physical_files' => $totalPhysicalFiles,
                'physical_files_deleted' => $deletePhysicalFiles
            ],
            'details' => $deletedCounts,
            'errors' => $errors
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Delete all files error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while deleting files: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
