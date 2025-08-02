<?php
require_once 'db_connection.php';
require_once 'notification_helper.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

session_start();

if (!isset($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Event ID is required.']);
    exit;
}

$id = $data['id'];

try {
    // Fetch event details before deletion for logging
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        echo json_encode(['status' => 'error', 'message' => 'Event not found.']);
        exit;
    }

    $eventName = $event['name'] ?? 'Unknown Event'; // Assuming 'name' column exists in 'events' table

    // Delete the event
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Log the notification for deleting the event
    $role = $_SESSION['user_role'];
    $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
    // Get the user's username, first name, and last name from the session
    $userFullName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        
    try {
        logNotification($userId, $role, 'delete', "($userFullName) deleted event '$eventName' (ID: $id)");
    } catch (Exception $e) {
        error_log("Notification logging failed: " . $e->getMessage());
    }

    echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete event: ' . $e->getMessage()]);
}
?>
