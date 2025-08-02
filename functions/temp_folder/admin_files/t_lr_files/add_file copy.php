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

        // Validate inputs
        if (empty($_POST['fileName']) || empty($_FILES['fileInput'])) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
            exit;
        }

        $fileName = filter_var($_POST['fileName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
        $file = $_FILES['fileInput'];

        // Extract the filename without the extension using pathinfo
        $fileInfo = pathinfo($file['name']);
        $fileNameWithoutExtension = $fileInfo['filename']; // Filename without the extension

        // Get the original file name (including extension)
        $fileName2 = basename($file['name']); 

        // Check for duplicates in t_lr_files and t_lr_files_versions based on filename
        $stmt = $pdo->prepare("
            SELECT COUNT(*) AS duplicate_count 
            FROM (
                SELECT filename FROM t_lr_files
                UNION ALL
                SELECT filename FROM t_lr_files_versions
            ) AS combined
            WHERE filename = :filename
        ");
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmt->execute();

        $duplicateCount = $stmt->fetch(PDO::FETCH_ASSOC)['duplicate_count'];

        if ($duplicateCount > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Duplicate file found in the database.']);
            $pdo->rollBack();
            exit;
        }

        // File upload directory
        $uploadDir = '../../uploads/files/t_lr_files/';
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
            $fileSize = $fileSizeInBytes . ' Bytes'; // Less than 1 KB
        } elseif ($fileSizeInBytes < 1024 * 1024) {
            $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB'; // Between 1 KB and 1 MB
        } else {
            $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB'; // 1 MB or larger
        }

        $file_path = '/west2es/uploads/files/t_lr_files/' . basename($file['name']);
        $file_path2 = $file_path;

        date_default_timezone_set('Asia/Manila');
        $currentDatetime = date('Y-m-d H:i:s');

        $table1 = 't_lr_files';
        $table2 = 't_lr_files_versions';

        // Insert into pending_files
        $stmt = $pdo->prepare("INSERT INTO pending_files (name, filename, user_id, version_no, file_path, download_path, datetime, file_size, table1, table2) 
                               VALUES (:name, :filename, :user_id, 1, :file_path, :download_path, :datetime, :file_size, :table1, :table2)");
        $stmt->bindParam(':name', $fileName, PDO::PARAM_STR); // Name provided by the user
        $stmt->bindParam(':filename', $fileName2, PDO::PARAM_STR); // Full file name with extension
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':file_path', $file_path2, PDO::PARAM_STR);
        $stmt->bindParam(':download_path', $filePath, PDO::PARAM_STR);
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
