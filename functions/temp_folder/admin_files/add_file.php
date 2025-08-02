<?php
require_once '../db_connection.php';
require_once '../notification_helper.php';
header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Begin transaction as the first step
        $pdo->beginTransaction();


        // Get the uploaded file data and table1 value
        $variables = setFileTableVariables($_POST['table1'], $_FILES['fileInput']);

        // Now use the returned variables
        $uploadDir = $variables['uploadDir'];
        $file_path = $variables['file_path'];
        $download_path = $variables['download_path'];
        $table2 = $variables['table2'];
        $table1 = $_POST['table1'];

        // Continue with the file upload and database insertion logic using these values

        // Validate inputs
        if (empty($_POST['fileName']) || empty($_FILES['fileInput']['name'])) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            $pdo->rollBack();
            exit;
        }

        $fileName = filter_var($_POST['fileName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
        $file = $_FILES['fileInput'];

        // Extract file information
        $fileInfo = pathinfo($file['name']);
        $fileNameFull = $file['name']; // Full filename including extension


        // Check for duplicates in master_files table
            $stmtMaster = $pdo->prepare("SELECT COUNT(*) AS duplicate_count FROM master_files WHERE filename = :filename OR name = :name");
            $stmtMaster->bindParam(':filename', $fileNameFull, PDO::PARAM_STR);
            $stmtMaster->bindParam(':name', $fileName, PDO::PARAM_STR);
            $stmtMaster->execute();

            $duplicateCountInMasterFiles = $stmtMaster->fetch(PDO::FETCH_ASSOC)['duplicate_count'];

            if ($duplicateCountInMasterFiles > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Duplicate file found in master_files table.']);
                $pdo->rollBack();
                exit;
            }
        // Check for duplicates in master_files table end   


        // File upload directory
        // $uploadDir = '../../uploads/files/admin_files/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Save file
        $filePath = $uploadDir . basename($file['name']);
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload file.']);
            $pdo->rollBack();
            exit;
        }

        // Convert file size to human-readable format (Bytes, KB, MB)
        $fileSizeInBytes = filesize($filePath);
        if ($fileSizeInBytes < 1024) {
            $fileSize = $fileSizeInBytes . ' Bytes';
        } elseif ($fileSizeInBytes < 1024 * 1024) {
            $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB';
        } else {
            $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB';
        }

        // $file_path = '/west2es/uploads/files/admin_files/' . basename($file['name']);
        // $download_path = '../uploads/files/admin_files/' . basename($file['name']);
        
        date_default_timezone_set('Asia/Manila');
        $currentDatetime = date('Y-m-d H:i:s');

        // $table1 = 'admin_files';
        // $table2 = 'admin_files_versions';

        // Insert into pending_files
        $stmt = $pdo->prepare("INSERT INTO pending_files (name, filename, user_id, version_no, file_path, download_path, datetime, file_size, table1, table2) 
                               VALUES (:name, :filename, :user_id, 1, :file_path, :download_path, :datetime, :file_size, :table1, :table2)");
        $stmt->bindParam(':name', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':filename', $fileNameFull, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
        $stmt->bindParam(':download_path', $download_path, PDO::PARAM_STR);
        $stmt->bindParam(':datetime', $currentDatetime, PDO::PARAM_STR);
        $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_STR);
        $stmt->bindParam(':table1', $table1, PDO::PARAM_STR);
        $stmt->bindParam(':table2', $table2, PDO::PARAM_STR);
        $stmt->execute();

        // Log the notification
        $role = $_SESSION['user_role'];
        $userFullName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];

        logNotification($userId, $role, 'add', "User $userFullName added a new pending admin file: $fileName");

        // Commit the transaction
        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.']);
    } catch (Exception $e) {
        // Roll back the transaction if an error occurs
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
