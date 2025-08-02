<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';
header('Content-Type: application/json');


session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['revision_id']) || !isset($_FILES['file'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
        exit;
    }

    $revisionId = filter_var($_POST['revision_id'], FILTER_SANITIZE_NUMBER_INT);

    // Handle file upload
    $file = $_FILES['file'];
    $uploadDir = '../../uploads/files/rp_completed_berf_files/';
    $fileName = basename($file['name']); // Get the original file name
    $filePath = $uploadDir . $fileName; // Save the file with the original name

    $file_path = '../uploads/files/rp_completed_berf_files/' . basename($file['name']);
    
    try {
        // Fetch the current file path for deletion
        $stmt = $pdo->prepare("SELECT file_path FROM rp_completed_berf_files_versions WHERE id = :revision_id");
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();
        $currentFile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($currentFile && file_exists($currentFile['file_path'])) {
            // Delete the existing file
            unlink($currentFile['file_path']);
        }

        // Move the new file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
        
        // $fileSize = filesize($filePath);
        // Get the file size

            // $fileSize = round(filesize($filePath) / 1024, 2);
        // Convert file size to KB

        $fileSizeInBytes = filesize($filePath);
        if ($fileSizeInBytes < 1024) {
            $fileSize = $fileSizeInBytes . ' Bytes'; // Less than 1 KB
        } elseif ($fileSizeInBytes < 1024 * 1024) {
            $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB'; // Between 1 KB and 1 MB
        } else {
            $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB'; // 1 MB or larger
        }


            // Update the revision in the database
            $stmt = $pdo->prepare("UPDATE rp_completed_berf_files_versions 
                                   SET file_path = :file_path, filename = :filename, datetime = NOW(), file_size = :file_size 
                                   WHERE id = :revision_id");
            $stmt->bindParam(':file_path', $file_path);
            $stmt->bindParam(':filename', $fileName); // Exact file name with extension
            $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_INT);
            $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
            $stmt->execute();



            $role = $_SESSION['user_role'];
            // Log the notification for file replacement
            $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
            try {
                logNotification($userId, '$role', 'replace', "Replaced file with filename: $fileName");
            } catch (Exception $e) {
                error_log("Notification logging failed: " . $e->getMessage());
            }

            echo json_encode(['status' => 'success', 'message' => 'Revision updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
