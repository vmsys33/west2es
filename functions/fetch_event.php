<?php
require_once 'db_connection.php';
require_once 'notification_helper.php';
header('Content-Type: application/json');

session_start();
// Validate the request
if (!isset($_GET['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Event ID is required.']);
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($event) {
        // Log the notification for viewing event 
        $role = $_SESSION['user_role'];
        $userId = $_SESSION['user_id']; // Assuming user_id is stored in the session
        $eventName = $event['name'] ?? 'Unknown Event'; // Assuming 'name' column exists in 'events' table
        $userFullName = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
        
        try {
            logNotification($userId, $role, 'Edit', "$userFullName edit event details for event '$eventName' (ID: $id)");
        } catch (Exception $e) {
            error_log("Notification logging failed: " . $e->getMessage());
        }

        echo json_encode(['status' => 'success', 'data' => $event]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Event not found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch event details: ' . $e->getMessage()]);
}
?>
