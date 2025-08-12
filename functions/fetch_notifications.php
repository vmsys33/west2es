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
    // Fetch all notifications (both seen and unseen)
    $stmt = $pdo->prepare("SELECT id, description, created_at, seen FROM notifications ORDER BY created_at DESC");
    $stmt->execute();

    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $notifications]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch notifications: ' . $e->getMessage()]);
}
?>
