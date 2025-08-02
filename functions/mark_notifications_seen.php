<?php
require_once 'db_connection.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ids = $input['ids'] ?? [];

    if (empty($ids)) {
        echo json_encode(['status' => 'error', 'message' => 'No notifications selected.']);
        exit;
    }

    try {
        // Generate placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        // Prepare the SQL statement
        $stmt = $pdo->prepare("UPDATE notifications SET seen = 1 WHERE id IN ($placeholders)");

        // Execute the statement with the IDs
        $stmt->execute($ids);

        echo json_encode(['status' => 'success', 'message' => 'Selected notifications marked as seen.']);
    } catch (PDOException $e) {
        error_log("Failed to update notifications: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Failed to update notifications.']);
    }
}
