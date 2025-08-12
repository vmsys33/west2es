<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure both file_id and table names are provided
    if (isset($_POST['file_id']) && is_numeric($_POST['file_id']) && isset($_POST['file_table1']) && isset($_POST['file_table2']) && isset($_POST['file_version'])) {
        $file_id = intval($_POST['file_id']);
        $fileTable1 = filter_var($_POST['file_table1'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Get the first table name
        $fileTable2 = filter_var($_POST['file_table2'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Get the second table name
        $fileVersion = filter_var($_POST['file_version'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
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
            $response['message'] = 'Invalid table name.';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Fetch all file revisions before deletion
            $stmt = $pdo->prepare("SELECT filename FROM $fileTable2 WHERE file_id = :file_id");
            $stmt->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt->execute();
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Collect filenames for logging
            $fileNames = array_column($files, 'filename');
            $fileNamesString = implode(', ', $fileNames);

            // Loop through all revision files and delete them
            foreach ($files as $file) {
                // Try multiple possible file paths
                $possiblePaths = [
                    '../../uploads/files/' . $fileTable1 . '/' . $file['filename'],
                    '../uploads/files/' . $fileTable1 . '/' . $file['filename'],
                    'uploads/files/' . $fileTable1 . '/' . $file['filename']
                ];
                
                $fileDeleted = false;
                foreach ($possiblePaths as $filePath) {
                    if (file_exists($filePath)) {
                        if (unlink($filePath)) {
                            $fileDeleted = true;
                            error_log("Successfully deleted file: $filePath");
                            break;
                        } else {
                            error_log("Failed to delete file: $filePath");
                        }
                    }
                }
                
                if (!$fileDeleted) {
                    error_log("Could not find or delete file: " . $file['filename']);
                }
            }
            
              // Delete from the file versions table (dynamic table)
            $stmt1 = $pdo->prepare("DELETE FROM $fileTable1 WHERE id = :file_id");
            $stmt1->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt1->execute();

              // Delete from the file versions table (dynamic table)
            $stmt2 = $pdo->prepare("DELETE FROM $fileTable2 WHERE file_id = :file_id");
            $stmt2->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmt2->execute();

            // Delete from master_files with conditions on table1 and version_no
            $stmtMaster = $pdo->prepare("DELETE FROM master_files WHERE file_id = :file_id AND table1 = :fileTable1");
            $stmtMaster->bindParam(':file_id', $file_id, PDO::PARAM_INT);
            $stmtMaster->bindParam(':fileTable1', $fileTable1, PDO::PARAM_STR);
            $stmtMaster->execute();

            // Commit transaction
            $pdo->commit();


            // Log the notification for file deletion
            $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
            $role = $_SESSION['user_role'];
            try {
                logNotification($userId, $role, 'delete', "Deleted file(s): $fileNamesString and revisions file ID: $file_id");
            } catch (Exception $e) {
                error_log("Notification logging failed: " . $e->getMessage());
            }

            $response['status'] = 'success';
            $response['message'] = 'File and all its revisions deleted successfully.';
        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            $response['message'] = 'An error occurred while deleting the file: ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Invalid file ID or table names.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
