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
        // Fetch file details
        $stmt = $pdo->prepare("SELECT id, filename FROM rp_proposal_berf_files WHERE id = :file_id");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file) {
            echo json_encode(['status' => 'error', 'message' => 'File not found.']);
            exit;
        }

        // Fetch revisions
        $stmt = $pdo->prepare("SELECT id, version_no, file_path, datetime, file_size FROM rp_proposal_berf_files_versions WHERE file_id = :file_id ORDER BY version_no DESC");
        $stmt->bindParam(':file_id', $fileId, PDO::PARAM_INT);
        $stmt->execute();
        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => ['file' => $file, 'revisions' => $revisions]]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
