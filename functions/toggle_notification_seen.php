<?php
session_start();
require_once 'db_connection.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid notification ID']);
    exit;
}

$notificationId = $data['id'];

try {
    // Toggle the seen status
    $stmt = $pdo->prepare("UPDATE notifications SET seen = 1 WHERE id = :id");
    $stmt->bindParam(':id', $notificationId);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Notification marked as seen']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update notification: ' . $e->getMessage()]);
}
?>
