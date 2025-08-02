<?php
require_once 'db_connection.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['table']) && isset($_GET['id'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];

    try {
        // Fetch file details
        $stmt = $pdo->prepare("SELECT id, filename FROM $table WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$file) {
            echo json_encode(['status' => 'error', 'message' => 'File not found.']);
            exit;
        }

        // Fetch revisions
        $revisionsTable = $table . '_versions';
        $stmt = $pdo->prepare("SELECT id, version_no, filename, file_path, datetime, file_size, file_id,download_path 
                                FROM $revisionsTable WHERE file_id = :id ORDER BY version_no DESC");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add table_name to each revision
        foreach ($revisions as &$revision) {
            $revision['table_name'] = $revisionsTable;
        }

        // Ensure the response matches the expected structure
        echo json_encode([
            'status' => 'success', 
            'data' => [
                'file' => $file, 
                'revisions' => $revisions 
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing parameters.']);
}
?>
