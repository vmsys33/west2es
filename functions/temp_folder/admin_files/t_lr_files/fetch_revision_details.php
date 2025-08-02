<?php
require_once '../db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['revision_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Revision ID is required.']);
        exit;
    }

    $revisionId = filter_var($_GET['revision_id'], FILTER_SANITIZE_NUMBER_INT);

    try {
        $stmt = $pdo->prepare("SELECT id, version_no, file_path, datetime, file_size 
                               FROM t_lr_files_versions 
                               WHERE id = :revision_id");
        $stmt->bindParam(':revision_id', $revisionId, PDO::PARAM_INT);
        $stmt->execute();

        $revision = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($revision) {
            echo json_encode(['status' => 'success', 'data' => $revision]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Revision not found.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
