<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

// Whitelist of allowed tables
$allowedTables = ['admin_files_versions', 'other_table_name'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    // $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // $table = filter_input(INPUT_POST, 'table', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $table = filter_input(INPUT_POST, 'table', FILTER_SANITIZE_FULL_SPECIAL_CHARS);



    // Validate inputs
    if (!$id || !$status || !in_array($table, $allowedTables)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
        exit;
    }

    try {
        // Update the status in the specified table
        $stmt = $pdo->prepare("UPDATE $table SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);

        echo json_encode(['status' => 'success', 'message' => 'Status updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status: ' . $e->getMessage()]);
    }
}
?>
