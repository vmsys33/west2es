<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

try {
    // Mark all unseen notifications as seen for the current user
    $stmt = $pdo->prepare("UPDATE notifications SET seen = 1 WHERE seen = 0");
    $stmt->execute();
    
    $affectedRows = $stmt->rowCount();
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'All notifications marked as seen',
        'affected_rows' => $affectedRows
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update notifications: ' . $e->getMessage()]);
}
?>
