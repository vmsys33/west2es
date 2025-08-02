<?php
require_once 'functions/db_connection.php'; // Ensure this path is correct
require_once 'functions/notification_helper.php';
require_once 'functions/file_operations.php';

header('Content-Type: application/json');

session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

try {
    // Begin transaction as the first step
    $pdo->beginTransaction();

    // Dummy data for testing
    $dummyFiles = [
        [
            'table1' => 'admin_files',
            'fileName' => '1.docx',
            'sourcePath' => 'dummy_files/1.docx'
        ],
        [
            'table1' => 'admin_files',
            'fileName' => '2.docx',
            'sourcePath' => 'dummy_files/2.docx'
        ],
        // Add more dummy files as needed
    ];

    foreach ($dummyFiles as $dummyFile) {
        // Get the uploaded file data and table1 value
        $variables = setFileTableVariables($dummyFile['table1'], ['name' => $dummyFile['fileName']]);

        // Now use the returned variables
        $uploadDir = $variables['uploadDir2'];
        $file_path = $variables['file_path'];
        $download_path = $variables['download_path'];
        $table2 = $variables['table2'];
        $table1 = $dummyFile['table1'];

        // Check if a pending file exists in pending_files
        $stmtCheckPending = $pdo->prepare("SELECT COUNT(*) AS pending_count FROM pending_files WHERE table2 = :table2 AND status = 'pending'");
        $stmtCheckPending->bindParam(':table2', $table2, PDO::PARAM_STR);
        $stmtCheckPending->execute();
        $pendingCount = $stmtCheckPending->fetch(PDO::FETCH_ASSOC)['pending_count'];

        // if ($pendingCount > 0) {
        //     echo json_encode(['status' => 'error', 'message' => 'You cannot add a new file while there is a pending file awaiting admin approval.']);
        //     $pdo->rollBack();
        //     exit;
        // }

        // Save dummy file to the upload directory
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filePath = $uploadDir . basename($dummyFile['fileName']);
        copy($dummyFile['sourcePath'], $filePath); // Copy the file from the source path

        // Convert file size to human-readable format (Bytes, KB, MB)
        $fileSizeInBytes = filesize($filePath);
        if ($fileSizeInBytes < 1024) {
            $fileSize = $fileSizeInBytes . ' Bytes';
        } elseif ($fileSizeInBytes < 1024 * 1024) {
            $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB';
        } else {
            $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB';
        }

        date_default_timezone_set('Asia/Manila');
        $currentDatetime = date('Y-m-d H:i:s');

        // Assuming user_id is stored in the session
        $userId = $_SESSION['user_id'];
        $fileName = $dummyFile['fileName'];

        // Insert into pending_files
        $stmt = $pdo->prepare("INSERT INTO pending_files (name, filename, user_id, version_no, file_path, download_path, datetime, file_size, table1, table2) 
                               VALUES (:name, :filename, :user_id, 1, :file_path, :download_path, :datetime, :file_size, :table1, :table2)");
        $stmt->bindParam(':name', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
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
    }

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['status' => 'success', 'message' => 'Dummy files added successfully.']);
} catch (Exception $e) {
    // Roll back the transaction if an error occurs
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>
