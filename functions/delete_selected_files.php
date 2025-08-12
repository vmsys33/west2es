<?php
/**
 * Delete Selected Files/Tables
 * ADMINISTRATIVE FUNCTION - USE WITH EXTREME CAUTION
 * This script deletes data from selected tables, combining main tables with their version tables
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
    $tablesJson = $_POST['tables'] ?? '';
    
    // Require explicit confirmation
    if ($confirmation !== 'DELETE_SELECTED_TABLES_CONFIRM') {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Confirmation code required. Please type "DELETE_SELECTED_TABLES_CONFIRM" to proceed.'
        ]);
        exit;
    }
    
    if (empty($tablesJson)) {
        echo json_encode(['status' => 'error', 'message' => 'No tables selected']);
        exit;
    }
    
    $selectedTables = json_decode($tablesJson, true);
    
    if (!is_array($selectedTables) || empty($selectedTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid table selection']);
        exit;
    }
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        $deletedCounts = [];
        $errors = [];
        
        // Define file tables and their corresponding version tables
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
        
        // Process each selected table
        foreach ($selectedTables as $table) {
            try {
                // Check if it's a file table (has version table)
                if (isset($fileTables[$table])) {
                    $mainTable = $table;
                    $versionTable = $fileTables[$table];
                    
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
                    
                } else {
                    // Handle non-file tables (pending_files, master_files, file_preview_mapping)
                    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                    if ($stmt->rowCount() > 0) {
                        $stmt = $pdo->prepare("DELETE FROM $table");
                        $stmt->execute();
                        $deletedCounts[$table] = $stmt->rowCount();
                    } else {
                        $errors[] = "Table $table does not exist";
                    }
                }
                
            } catch (Exception $e) {
                $errors[] = "Error processing $table: " . $e->getMessage();
            }
        }
        
        // Log the action
        $adminId = $_SESSION['user_id'] ?? 0;
        $adminName = ($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '');
        $tableList = implode(', ', $selectedTables);
        
        try {
            logNotification($adminId, 'admin', 'delete_selected_tables', 
                "($adminName) deleted data from selected tables: $tableList. Physical files deleted: " . ($deletePhysicalFiles ? 'Yes' : 'No'));
        } catch (Exception $e) {
            error_log("Failed to log delete selected tables action: " . $e->getMessage());
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
            'message' => 'Selected table data has been deleted successfully',
            'summary' => [
                'total_main_records' => $totalMainRecords,
                'total_version_records' => $totalVersionRecords,
                'total_physical_files' => $totalPhysicalFiles,
                'physical_files_deleted' => $deletePhysicalFiles,
                'tables_processed' => count($selectedTables)
            ],
            'details' => $deletedCounts,
            'errors' => $errors
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        error_log("Delete selected tables error: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'An error occurred while deleting data: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>




