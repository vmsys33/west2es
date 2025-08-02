<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_id']) && is_numeric($_POST['file_id'])) {
        $file_id = intval($_POST['file_id']);

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Fetch all file revisions before deletion
            $stmt = $pdo->prepare("SELECT filename FROM rp_proposal_berf_files_versions WHERE file_id = :file_id");
            $stmt->execute(['file_id' => $file_id]);
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Collect filenames for logging
            $fileNames = array_column($files, 'filename');
            $fileNamesString = implode(', ', $fileNames);

            // Loop through all revision files and delete them
            foreach ($files as $file) {
                $filePath = '../../uploads/files/rp_proposal_berf_files/' . $file['filename']; // Adjust the path as needed

                // Check if the file exists and delete it
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the file
                }
            }

            // Delete from rp_proposal_berf_files_versions
            $stmt1 = $pdo->prepare("DELETE FROM rp_proposal_berf_files_versions WHERE file_id = :file_id");
            $stmt1->execute(['file_id' => $file_id]);

            // Delete from rp_proposal_berf_files
            $stmt2 = $pdo->prepare("DELETE FROM rp_proposal_berf_files WHERE id = :file_id");
            $stmt2->execute(['file_id' => $file_id]);

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
        $response['message'] = 'Invalid file ID.';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
