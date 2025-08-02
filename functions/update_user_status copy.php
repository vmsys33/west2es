<?php
require_once 'db_connection.php';
require_once 'notification_helper.php';
header('Content-Type: application/json'); // Ensure JSON response


// Ensure session is started
session_start();

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($data['id_no']) || !isset($data['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    exit;
}

$id_no = $data['id_no'];
$status = $data['status'];

try {
    // Update user status
    $stmt = $pdo->prepare("UPDATE user_data SET status = :status WHERE id_no = :id_no");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id_no', $id_no);
    $stmt->execute();

    // Log the notification
    $userId = $_SESSION['user_id'];
    $role = $_SESSION['user_role'];

    try {
        logNotification($userId, $role, 'create', "Created an event: $name");
    } catch (Exception $e) {
        error_log("Notification logging failed: " . $e->getMessage());
    }
   

    echo json_encode(['status' => 'success', 'message' => 'User status updated successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update user status: ' . $e->getMessage()]);
}
?>


