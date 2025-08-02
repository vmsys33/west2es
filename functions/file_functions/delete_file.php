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
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Fetch all file revisions before deletion
            $stmt = $pdo->prepare("SELECT filename FROM $fileTable2 WHERE file_id = :file_id");
            $stmt->execute(['file_id' => $file_id]);
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Collect filenames for logging
            $fileNames = array_column($files, 'filename');
            $fileNamesString = implode(', ', $fileNames);

            // Loop through all revision files and delete them
            foreach ($files as $file) {
                $filePath = '../../uploads/files/' . $fileTable1 . '/' . $file['filename']; // Adjust the path dynamically

                // Check if the file exists and delete it
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the file
                }
            }
            
              // Delete from the file versions table (dynamic table)
            $stmt1 = $pdo->prepare("DELETE FROM $fileTable1 WHERE id = :file_id");
            $stmt1->execute([
                'file_id' => $file_id
            ]);

              // Delete from the file versions table (dynamic table)
            $stmt2 = $pdo->prepare("DELETE FROM $fileTable2 WHERE file_id = :file_id");
            $stmt2->execute([
                'file_id' => $file_id
            ]);

            // Delete from master_files with conditions on table1 and version_no
            $stmtMaster = $pdo->prepare("DELETE FROM master_files WHERE file_id = :file_id AND table1 = :fileTable1");
            $stmtMaster->execute([
                'file_id' => $file_id,
                'fileTable1' => $fileTable1
        
        
            ]);

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
