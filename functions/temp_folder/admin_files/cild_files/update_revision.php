<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['revision_id'], $_POST['version_no'], $_POST['file_path'], $_POST['datetime'], $_POST['file_size'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
        exit;
    }

    $revisionId = filter_var($_POST['revision_id'], FILTER_SANITIZE_NUMBER_INT);
    $versionNo = filter_var($_POST['version_no'], FILTER_SANITIZE_NUMBER_INT);
    $filePath = filter_var($_POST['file_path'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $datetime = filter_var($_POST['datetime'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $fileSize = filter_var($_POST['file_size'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    try {
        $stmt = $pdo->prepare("UPDATE cild_files_versions 
                               SET version_no = :version_no, file_path = :file_path, datetime = :datetime, file_size = :file_size 
                               WHERE id = :revision_id");
        $stmt->bindParam(':version_no', $versionNo);
        $stmt->bindParam(':file_path', $filePath);
        $stmt->bindParam(':datetime', $datetime);
        $stmt->bindParam(':file_size', $fileSize);
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Revision updated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
