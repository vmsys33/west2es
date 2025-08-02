<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';
header('Content-Type: application/json');

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['file_id']) || !isset($_FILES['file'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
        exit;
    }

    $fileId = filter_var($_POST['file_id'], FILTER_SANITIZE_NUMBER_INT); // Get the file ID from the form

    // Handle file upload
    $file = $_FILES['file'];
    $uploadDir = '../../uploads/files/aeld_files/';
    
    $fileName = basename($file['name']); // Get the original file name
    $filePath = $uploadDir . $fileName; // Save the file with the original name
    $file_path = '../uploads/files/aeld_files/' . basename($file['name']);

    $file_path2 = '/west2es/uploads/files/aeld_files/' . basename($file['name']);

    try {
        // Fetch the current latest version number for the file
        $stmt = $pdo->prepare("SELECT name, MAX(version_no) AS latest_version FROM aeld_files_versions WHERE file_id = :file_id");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $currentVersion = $stmt->fetch(PDO::FETCH_ASSOC);
        $newVersion = $currentVersion ? $currentVersion['latest_version'] + 1 : 1; // Increment version by 1
        $name = $currentVersion ? $currentVersion['name'] : '';

        // Move the new file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {

             $fileSizeInBytes = filesize($filePath); // Get the file size
        if ($fileSizeInBytes < 1024) {
            $fileSize = $fileSizeInBytes . ' Bytes'; // Less than 1 KB
        } elseif ($fileSizeInBytes < 1024 * 1024) {
            $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB'; // Between 1 KB and 1 MB
        } else {
            $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB'; // 1 MB or larger
        }    


            // Insert the new revision into the database
            // $stmt = $pdo->prepare("INSERT INTO aeld_files_versions (file_id, name, filename, file_path,download_path, version_no, file_size, datetime) 
            //                        VALUES (:file_id, :name, :filename, :file_path,:download_path,:version_no, :file_size, NOW())");
            // $stmt->bindParam(':file_id', $fileId);
            // $stmt->bindParam(':name', $name); 
            // $stmt->bindParam(':filename', $fileName); // Exact file name with extension
            // $stmt->bindParam(':file_path', $file_path2);
            // $stmt->bindParam(':download_path', $file_path2);
            // $stmt->bindParam(':version_no', $newVersion, PDO::PARAM_INT);
            // $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_INT);
            // $stmt->execute();



               // Log the notification for adding a new revision
            $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
            $role = $_SESSION['user_role'];



            $table1='aeld_files';
            $table2='aeld_files_versions';    
            $stmt = $pdo->prepare("INSERT INTO pending_files (name, filename,user_id,version_no,file_path, download_path, datetime,file_size,table1,table2,file_id) VALUES (:name, :filename,:user_id,:version_no,:file_path, :download_path, NOW(),:file_size,:table1,:table2,:file_id)");
            $stmt->bindParam(':name', $fileName, PDO::PARAM_STR);
            $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR); // Use the filename without the extension
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':version_no', $newVersion, PDO::PARAM_INT);
            $stmt->bindParam(':file_path', $file_path2, PDO::PARAM_STR);
            $stmt->bindParam(':download_path', $file_path);
            $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_STR);
            $stmt->bindParam(':table1', $table1, PDO::PARAM_STR);
            $stmt->bindParam(':table2', $table2, PDO::PARAM_STR);
            $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
            $stmt->execute();



         
            try {
                logNotification($userId, $role, 'add_revision', "Added a new pending revision for file ID: $fileId, Filename: $fileName, Version: $newVersion");
            } catch (Exception $e) {
                error_log("Notification logging failed: " . $e->getMessage());
            }

            echo json_encode(['status' => 'success', 'message' => 'New revision added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
