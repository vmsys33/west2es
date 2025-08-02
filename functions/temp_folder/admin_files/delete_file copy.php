<?php
require_once '../db_connection.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['file_id']) && is_numeric($_POST['file_id'])) {
        $file_id = intval($_POST['file_id']);

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Fetch all file revisions before deletion
            $stmt = $pdo->prepare("SELECT filename FROM admin_files_versions WHERE file_id = :file_id");
            $stmt->execute(['file_id' => $file_id]);
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Loop through all revision files and delete them
            foreach ($files as $file) {
                $filePath = '../../uploads/files/admin_files/' . $file['filename']; // Adjust the path as needed

                // Check if the file exists and delete it
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the file
                }
            }

            // Delete from admin_files_versions
            $stmt1 = $pdo->prepare("DELETE FROM admin_files_versions WHERE file_id = :file_id");
            $stmt1->execute(['file_id' => $file_id]);

            // Delete from admin_files
            $stmt2 = $pdo->prepare("DELETE FROM admin_files WHERE id = :file_id");
            $stmt2->execute(['file_id' => $file_id]);

            // Commit transaction
            $pdo->commit();

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
