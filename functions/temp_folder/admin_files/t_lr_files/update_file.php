<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['file_id']) || !isset($_POST['fileName']) || !isset($_POST['datetime'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
        exit;
    }

    $fileId = filter_var($_POST['file_id'], FILTER_SANITIZE_NUMBER_INT);
    $fileName = filter_var($_POST['fileName'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $datetime = filter_var($_POST['datetime'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        // Update file details
        $stmt = $pdo->prepare("UPDATE t_lr_files SET filename = :filename WHERE id = :file_id");
        $stmt->bindParam(':filename', $fileName);
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();

        // Update latest datetime in t_lr_files_versions
        $stmt = $pdo->prepare("UPDATE t_lr_files_versions SET datetime = :datetime WHERE file_id = :file_id ORDER BY version_no DESC LIMIT 1");
        $stmt->bindParam(':datetime', $datetime);
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'File updated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
