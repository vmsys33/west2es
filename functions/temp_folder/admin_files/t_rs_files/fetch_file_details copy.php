<?php
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['table'], $_GET['id'])) {
    $table = $_GET['table'];
    $id = $_GET['id'];

    // Ensure table is valid
    $validTables = ['t_rs_files', 't_rs_files'];
    if (!in_array($table, $validTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid table.']);
        exit;
    }

    try {
        // Fetch file details
        $stmt = $pdo->prepare("SELECT id, filename FROM $table WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($file) {
            $file['source_table'] = $table;
            echo json_encode(['status' => 'success', 'file' => $file]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File not found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
