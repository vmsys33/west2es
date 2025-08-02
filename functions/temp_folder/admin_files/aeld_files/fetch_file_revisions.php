<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['file_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'File ID is required.']);
        exit;
    }

    $fileId = filter_var($_GET['file_id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        // Fetch revisions
        $stmt = $pdo->prepare("SELECT id, filename, version_no, file_path, datetime, file_size 
                               FROM aeld_files_versions 
                               WHERE file_id = :file_id 
                               ORDER BY version_no DESC");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($revisions) {
            echo json_encode(['status' => 'success', 'data' => $revisions]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No revisions found for this file.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
